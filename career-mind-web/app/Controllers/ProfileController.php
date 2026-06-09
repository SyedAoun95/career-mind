<?php

namespace App\Controllers;

use App\Models\Profile;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\CvFile;
use App\Models\CvAnalysis;
use App\Models\CareerPredictionCache;
use App\Models\Career;
use App\Models\CareerRecommendation;
use App\Models\JobRecommendation;
use App\Services\LearningPathAdvisor;
use App\Services\MlLogger;

class ProfileController extends BaseController
{
    /** Minimum number of profile skills required before we show an ML career prediction. */
    private const MIN_SKILLS_FOR_PREDICTION = 2;

    public function dashboard(): void
    {
        $this->requireAuth();
        $profileModel = new Profile();
        $profile = $profileModel->getByUserId($_SESSION['user_id']);
        $skillModel = new Skill();
        $interestModel = new Interest();
        $skills = $skillModel->getUserSkills($_SESSION['user_id']);
        $interests = $interestModel->getUserInterests($_SESSION['user_id']);

        $hideDashboardResults = !empty($_SESSION['dashboard_hide_results']);

        $cvAnalysis = null;
        $careerRecommendations = [];
        $jobRecommendations = [];
        $careerPrediction = null;
        $careerPredictionSource = 'none';
        $predictionCacheMeta = ['confidence' => null, 'status' => null, 'last_refreshed' => null];
        $mlServiceStatus = 'unavailable';
        $insufficientProfileData = false;

        // CV analysis and recommendations can be hidden on login (they may be stale
        // from a previous session — the user re-generates them by uploading a CV).
        if (!$hideDashboardResults) {
            $cvAnalysisModel = new CvAnalysis();
            $cvAnalysis = $cvAnalysisModel->getLatestByUser($_SESSION['user_id']);

            $careerRecommendationModel = new CareerRecommendation();
            $jobRecommendationModel = new JobRecommendation();
            $careerRecommendations = $careerRecommendationModel->getLatestByUser($_SESSION['user_id']);
            $jobRecommendations = $jobRecommendationModel->getLatestByUser($_SESSION['user_id']);
        }

        // The career prediction is computed live from the current profile (not stale),
        // so it always runs — independent of the hide-results flag.
        $predictionCacheModel = new CareerPredictionCache();
        $careerPredictionSource = 'live';

        // The prediction is driven by the user's most recent CV (durably stored), so
        // the ML card always reflects the last uploaded CV — even after a reload or
        // re-login. Falls back to profile skills when no CV has been analysed yet.
        $latestCvSkills = (new CvAnalysis())->getLatestExtractedSkills($_SESSION['user_id']);
        $predictionSkills = count($latestCvSkills) >= self::MIN_SKILLS_FOR_PREDICTION ? $latestCvSkills : $skills;

        // A prediction is only meaningful with real signal. With too few skills
        // the model falls back to a near-random base-rate guess, so we show an
        // "add more skills" prompt instead of a misleading career card.
        if (count($predictionSkills) < self::MIN_SKILLS_FOR_PREDICTION) {
            $insufficientProfileData = true;
            $careerPrediction = null;
            $careerPredictionSource = 'none';
            $mlServiceStatus = 'unavailable';
        } else {
            // Probe ML service health before making a heavyweight /predict call
            $mlHealthy = $this->checkMlHealth();

            if ($mlHealthy) {
                $careerPrediction = $this->callPredict([
                    'profile' => $profile,
                    'skills' => $predictionSkills,
                    'interests' => $interests,
                ]);
            }

            if ($careerPrediction) {
                $mlServiceStatus = 'live';
                $confidence = $careerPrediction['top_predictions'][0]['score'] ?? null;
                $predictionCacheModel->upsert($_SESSION['user_id'], $careerPrediction, $confidence);
                $predictionCacheMeta = $predictionCacheModel->getMetadata($_SESSION['user_id']);
            } else {
                // Fall back to cached prediction
                $careerPrediction = $predictionCacheModel->getByUser($_SESSION['user_id']);
                if ($careerPrediction) {
                    $careerPredictionSource = 'cache';
                    $mlServiceStatus = 'cached';
                } else {
                    $careerPredictionSource = 'none';
                    $mlServiceStatus = 'unavailable';
                }
                $predictionCacheMeta = $predictionCacheModel->getMetadata($_SESSION['user_id']);
            }
        }

        $message = $_GET['message'] ?? '';
        $error = $_GET['error'] ?? '';

        $missingSkills = $cvAnalysis['missing_skills'] ?? [];
        $learningPathSuggestions = $hideDashboardResults ? [] : LearningPathAdvisor::recommend($missingSkills);

        $this->view('profile/dashboard', [
            'profile' => $profile,
            'skills' => $skills,
            'interests' => $interests,
            'cvAnalysis' => $cvAnalysis,
            'careerRecommendations' => $careerRecommendations,
            'jobRecommendations' => $jobRecommendations,
            'careerPrediction' => $careerPrediction,
            'careerPredictionSource' => $careerPredictionSource,
            'predictionCacheMeta' => $predictionCacheMeta,
            'mlServiceStatus' => $mlServiceStatus,
            'insufficientProfileData' => $insufficientProfileData,
            'predictionSkills' => $predictionSkills,
            'learningPathSuggestions' => $learningPathSuggestions,
            'hideDashboardResults' => $hideDashboardResults,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function clearDashboardResults(): void
    {
        $this->requireAuth();
        $_SESSION['dashboard_hide_results'] = true;
        $this->redirect('/dashboard?message=Dashboard%20results%20cleared.%20Upload%20a%20new%20CV%20to%20see%20fresh%20results.');
    }

    public function editProfile(): void
    {
        $this->requireAuth();
        $profileModel = new Profile();
        $profile = $profileModel->getByUserId($_SESSION['user_id']);

        $skillModel = new Skill();
        $interestModel = new Interest();

        $skills = $skillModel->getUserSkills($_SESSION['user_id']);
        $interests = $interestModel->getUserInterests($_SESSION['user_id']);

        $this->view('profile/edit', [
            'profile' => $profile,
            'skills' => implode(', ', $skills),
            'interests' => implode(', ', $interests),
            'skillCatalog' => $skillModel->allNames(),
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireAuth();

        $data = [
            'age' => trim($_POST['age'] ?? ''),
            'education_level' => trim($_POST['education_level'] ?? ''),
            'institution' => trim($_POST['institution'] ?? ''),
            'graduation_year' => trim($_POST['graduation_year'] ?? ''),
        ];

        $profileModel = new Profile();
        $profileModel->upsert($_SESSION['user_id'], $data);

        $skills = $_POST['skills'] ?? '';
        $interests = $_POST['interests'] ?? '';

        $skillModel = new Skill();
        $interestModel = new Interest();

        $skillModel->syncUserSkills($_SESSION['user_id'], $skills);
        $interestModel->syncUserInterests($_SESSION['user_id'], $interests);

        $this->redirect('/dashboard');
    }

    public function uploadCv(): void
    {
        $this->requireAuth();
        unset($_SESSION['dashboard_hide_results']);

        if (empty($_FILES['cv_file']) || ($_FILES['cv_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->cvError('Please upload a valid CV file');
        }

        $file = $_FILES['cv_file'];
        $maxBytes = 6 * 1024 * 1024;
        if (($file['size'] ?? 0) > $maxBytes) {
            $this->cvError('CV file must be 6MB or smaller');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];

        if (!in_array($extension, $allowed, true)) {
            $this->cvError('Only PDF, DOC, and DOCX files are allowed');
        }

        $mimeType = mime_content_type($file['tmp_name']) ?: '';
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        if (!in_array($mimeType, $allowedMimes, true)) {
            $this->cvError('Invalid file type. Upload a PDF or Word document');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/cv';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $safeName = sprintf('%s_%s.%s', $_SESSION['user_id'], bin2hex(random_bytes(6)), $extension);
        $storedPath = $uploadDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $storedPath)) {
            $this->cvError('Failed to store uploaded CV');
        }

        $cvFileModel = new CvFile();
        $cvId = $cvFileModel->create($_SESSION['user_id'], $file['name'], '/uploads/cv/' . $safeName);

        $analysis = $this->callCvAnalysis($storedPath, $file['name']);

        $cvAnalysisModel = new CvAnalysis();
        $summary = $analysis['summary'] ?? 'CV analysis completed.';
        if (!empty($analysis['schema_error'])) {
            $summary = $analysis['schema_error'];
        }

        $cvAnalysisModel->create(
            $cvId,
            $summary,
            $analysis['missing_skills'] ?? [],
            $analysis['feedback'] ?? [],
            isset($analysis['score']) ? (int)$analysis['score'] : null,
            $analysis['extracted_skills'] ?? []
        );

        if (!empty($analysis['schema_error'])) {
            $this->cvError($analysis['schema_error']);
        }

        if (empty($analysis['summary'])) {
            $this->cvError('CV analysis failed. Please try again');
        }

        // The AI service flags documents that aren't actually a CV/résumé.
        if (isset($analysis['is_cv']) && $analysis['is_cv'] === false) {
            $this->cvError($summary);
        }

        // Everything below is driven by THIS CV's extracted skills, so each upload
        // produces fresh, CV-specific results (prediction + recommendations).
        $predictionSkills = $this->resolveCvSkills($analysis['extracted_skills'] ?? []);
        $prediction = $this->predictFromSkills($predictionSkills);
        $recommendations = $this->recommendFromSkills($predictionSkills);

        if ($this->isAjaxRequest()) {
            $this->json([
                'ok' => true,
                'analysis' => [
                    'file_name' => $file['name'],
                    'score' => isset($analysis['score']) ? (int)$analysis['score'] : null,
                    'summary' => $analysis['summary'] ?? '',
                    'missing_skills' => array_values($analysis['missing_skills'] ?? []),
                    'feedback' => array_values($analysis['feedback'] ?? []),
                ],
                'prediction' => $prediction,
                'recommendations' => $recommendations,
            ]);
        }

        $this->redirect('/dashboard?message=CV%20analysis%20completed');
    }

    /**
     * Pick the skills that should drive the CV's prediction + recommendations:
     * the CV's extracted skills lead; profile skills are a fallback when the CV
     * yields too few. Also remembered in the session so the dashboard stays in sync.
     */
    private function resolveCvSkills(array $cvSkills): array
    {
        $cvSkills = array_values(array_filter(array_map('trim', $cvSkills)));
        $skills = count($cvSkills) >= self::MIN_SKILLS_FOR_PREDICTION
            ? $cvSkills
            : (new Skill())->getUserSkills($_SESSION['user_id']);

        $_SESSION['last_cv_skills'] = count($skills) >= self::MIN_SKILLS_FOR_PREDICTION ? $skills : null;
        return $skills;
    }

    /**
     * Career prediction from a skill set. Caches it and returns a display-ready
     * payload: top label + normalised "share among shortlist" confidence + alternatives.
     */
    private function predictFromSkills(array $skills): ?array
    {
        if (count($skills) < self::MIN_SKILLS_FOR_PREDICTION) {
            return null;
        }
        $userId = $_SESSION['user_id'];
        $result = $this->callPredict([
            'profile' => (new Profile())->getByUserId($userId),
            'skills' => $skills,
            'interests' => (new Interest())->getUserInterests($userId),
        ]);
        $top = $result['top_predictions'][0] ?? null;
        if (!$top) {
            return null;
        }

        (new CareerPredictionCache())->upsert($userId, $result, $top['score'] ?? null);

        $entries = array_slice($result['top_predictions'], 0, 4);
        return [
            'label' => $top['label'] ?? '',
            'confidence' => self::matchConfidence($top['label'] ?? '', $skills),
            'summary' => $top['summary'] ?? '',
            'alternatives' => array_map(fn($p) => [
                'label' => $p['label'] ?? '',
                'confidence' => self::matchConfidence($p['label'] ?? '', $skills),
            ], array_slice($entries, 1)),
        ];
    }

    /**
     * Confidence as how well the user's skills cover the predicted career's
     * required skills: many skills missing -> ~65%, good coverage -> ~85-90%.
     * Returns a percentage in the 62-93 range.
     */
    public static function matchConfidence(string $careerTitle, array $userSkills): int
    {
        $required = (new Career())->requiredSkillsFor($careerTitle);
        if (empty($required)) {
            return 75; // neutral default when the career has no skill list
        }
        $have = array_map(fn($s) => strtolower(trim($s)), $userSkills);
        $matched = 0;
        foreach ($required as $skill) {
            foreach ($have as $h) {
                if ($h !== '' && (str_contains($skill, $h) || str_contains($h, $skill))) {
                    $matched++;
                    break;
                }
            }
        }
        $coverage = $matched / count($required);          // 0.0 – 1.0
        return max(62, min(93, (int)round(62 + $coverage * 31)));
    }

    /**
     * Regenerate the Career & Job recommendations from a skill set, persist them,
     * and return the lists for the AJAX response so the panel updates live.
     */
    private function recommendFromSkills(array $skills): ?array
    {
        if (empty($skills)) {
            return null;
        }
        $userId = $_SESSION['user_id'];
        $result = $this->callRecommendations([
            'profile' => (new Profile())->getByUserId($userId),
            'skills' => $skills,
            'interests' => (new Interest())->getUserInterests($userId),
            'filters' => [],
        ]);
        if (!empty($result['schema_error'])) {
            return null;
        }

        $careers = $result['career_recommendations'] ?? [];
        $jobs = $result['job_recommendations'] ?? [];

        $careerModel = new CareerRecommendation();
        $jobModel = new JobRecommendation();
        $careerModel->clearByUser($userId);
        $jobModel->clearByUser($userId);
        $careerModel->createMany($userId, $careers);
        $jobModel->createMany($userId, $jobs);

        $fmt = fn($items) => array_map(fn($i) => [
            'title' => $i['title'] ?? '',
            'reason' => $i['reason'] ?? '',
        ], $items);

        return ['careers' => $fmt($careers), 'jobs' => $fmt($jobs)];
    }

    /**
     * Report a CV-upload failure — JSON for AJAX requests, redirect otherwise.
     */
    private function cvError(string $message): void
    {
        if ($this->isAjaxRequest()) {
            $this->json(['ok' => false, 'error' => $message], 400);
        }
        $this->redirect('/dashboard?error=' . urlencode($message));
    }

    public function requestRecommendations(): void
    {
        $this->requireAuth();
        unset($_SESSION['dashboard_hide_results']);

        $profileModel = new Profile();
        $profile = $profileModel->getByUserId($_SESSION['user_id']);
        $skillModel = new Skill();
        $interestModel = new Interest();
        $skills = $skillModel->getUserSkills($_SESSION['user_id']);
        $interests = $interestModel->getUserInterests($_SESSION['user_id']);

        $filters = [
            'job_level' => trim($_POST['job_level'] ?? ''),
            'job_type' => trim($_POST['job_type'] ?? ''),
        ];

        $payload = [
            'profile' => $profile,
            'skills' => $skills,
            'interests' => $interests,
            'filters' => $filters,
        ];

        $recommendations = $this->callRecommendations($payload);

        if (!empty($recommendations['schema_error'])) {
            $this->redirect('/dashboard?error=' . urlencode($recommendations['schema_error']));
        }

        $careerRecommendationModel = new CareerRecommendation();
        $jobRecommendationModel = new JobRecommendation();

        $careerRecommendationModel->clearByUser($_SESSION['user_id']);
        $jobRecommendationModel->clearByUser($_SESSION['user_id']);

        $careerRecommendationModel->createMany($_SESSION['user_id'], $recommendations['career_recommendations'] ?? []);
        $jobRecommendationModel->createMany($_SESSION['user_id'], $recommendations['job_recommendations'] ?? []);

        $this->redirect('/dashboard?message=Recommendations%20generated');
    }

    private function aiBaseUrl(): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        $baseUrl = $config['ai']['base_url'] ?? 'http://localhost:5001';
        return rtrim($baseUrl, '/');
    }

    private function callCvAnalysis(string $filePath, string $originalName): array
    {
        $endpoint = $this->aiBaseUrl() . '/cv/analyze';
        $ch = curl_init($endpoint);
        $postFields = [
            'cv_file' => new \CURLFile($filePath, mime_content_type($filePath) ?: 'application/octet-stream', $originalName),
        ];

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        if ($response === false || $error) {
            return [
                'summary' => 'AI service unavailable. CV stored for later analysis.',
                'missing_skills' => [],
                'feedback' => ['We will analyze your CV once the AI service is online.'],
                'score' => null,
            ];
        }

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            $schemaCheck = $this->validateAiSchema($decoded);
            if ($schemaCheck !== null) {
                return ['schema_error' => $schemaCheck];
            }
            return $decoded;
        }

        return [
            'summary' => 'CV analysis completed.',
            'missing_skills' => [],
            'feedback' => [],
            'score' => null,
        ];
    }

    private function callRecommendations(array $payload): array
    {
        $endpoint = $this->aiBaseUrl() . '/recommendations';
        $ch = curl_init($endpoint);
        $json = json_encode($payload);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        if ($response === false || $error) {
            return [
                'career_recommendations' => [],
                'job_recommendations' => [],
            ];
        }

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            $schemaCheck = $this->validateAiSchema($decoded);
            if ($schemaCheck !== null) {
                return ['schema_error' => $schemaCheck];
            }
            return $decoded;
        }

        return [
            'career_recommendations' => [],
            'job_recommendations' => [],
        ];
    }

    /**
     * Lightweight probe: GET /health with a short timeout.
     * Returns true only if the ML service responds with {"status":"ok"|"degraded"}.
     */
    private function checkMlHealth(): bool
    {
        $endpoint = $this->aiBaseUrl() . '/health';
        $ch = curl_init($endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $curlError || $httpCode >= 400) {
            MlLogger::warn('ML health check failed', [
                'endpoint' => $endpoint,
                'http_code' => $httpCode,
                'curl_error' => $curlError,
            ]);
            return false;
        }

        $decoded = json_decode($response, true);
        $status = $decoded['status'] ?? '';
        return in_array($status, ['ok', 'degraded'], true);
    }

    private function callPredict(array $payload): ?array
    {
        $endpoint = $this->aiBaseUrl() . '/predict';
        $ch = curl_init($endpoint);
        $json = json_encode($payload);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $curlError) {
            $isTimeout = in_array($curlErrno, [CURLE_OPERATION_TIMEDOUT, CURLE_COULDNT_CONNECT], true);
            MlLogger::error('ML /predict call failed', [
                'endpoint' => $endpoint,
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
                'timeout' => $isTimeout,
            ]);
            return null;
        }

        if ($httpCode >= 400) {
            MlLogger::warn('ML /predict non-OK response', [
                'endpoint' => $endpoint,
                'http_code' => $httpCode,
            ]);
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function expectedAiSchemaVersion(): int
    {
        return 1;
    }

    private function validateAiSchema(array $decoded): ?string
    {
        if (!array_key_exists('schema_version', $decoded)) {
            return null;
        }

        $schemaVersion = (int)$decoded['schema_version'];
        if ($schemaVersion !== $this->expectedAiSchemaVersion()) {
            return sprintf('AI response schema mismatch (expected %d, got %d).', $this->expectedAiSchemaVersion(), $schemaVersion);
        }

        return null;
    }
}
