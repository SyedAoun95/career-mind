<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\Career;
use App\Models\Job;
use App\Models\CvAnalysis;

class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireAdmin();

        $userModel = new User();
        $profileModel = new Profile();
        $skillModel = new Skill();
        $interestModel = new Interest();

        $stats = [
            'totalUsers' => $userModel->countAll(),
            'adminUsers' => $userModel->countByRole('admin'),
            'studentUsers' => $userModel->countByRole('student'),
            'profiles' => $profileModel->countAll(),
            'skills' => $skillModel->countAll(),
            'interests' => $interestModel->countAll(),
        ];

        $this->adminView('admin/dashboard', [
            'stats' => $stats,
        ]);
    }

    public function users(): void
    {
        $this->requireAdmin();

        $userModel = new User();
        $users = $userModel->all();

        $message = $_GET['message'] ?? '';
        $error = $_GET['error'] ?? '';

        $this->adminView('admin/users', [
            'users' => $users,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function updateUserRole(): void
    {
        $this->requireAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');

        if ($userId <= 0 || !in_array($role, ['admin', 'student'], true)) {
            $this->redirect('/admin/users?error=Invalid%20user%20or%20role');
        }

        if ($userId === (int)($_SESSION['user_id'] ?? 0) && $role !== 'admin') {
            $this->redirect('/admin/users?error=You%20cannot%20remove%20your%20own%20admin%20access');
        }

        $userModel = new User();
        $userModel->updateRole($userId, $role);

        $this->redirect('/admin/users?message=User%20role%20updated');
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->redirect('/admin/users?error=Invalid%20user');
        }

        if ($userId === (int)($_SESSION['user_id'] ?? 0)) {
            $this->redirect('/admin/users?error=You%20cannot%20delete%20your%20own%20account');
        }

        $userModel = new User();
        $userModel->delete($userId);

        $this->redirect('/admin/users?message=User%20deleted');
    }

    public function datasets(): void
    {
        $this->requireAdmin();

        $careerModel = new Career();
        $jobModel = new Job();
        $skillModel = new Skill();

        $careerCount = $careerModel->countAll();
        $jobCount = $jobModel->countAll();
        $skillCount = $skillModel->countAll();

        $message = $_GET['message'] ?? '';
        $error = $_GET['error'] ?? '';

        $datasets = [
            [
                'name' => 'Career Path Taxonomy',
                'status' => $careerCount > 0 ? 'Ready' : 'Planned',
                'records' => $careerCount,
                'updated_at' => $careerCount > 0 ? 'Available' : 'Not uploaded',
            ],
            [
                'name' => 'Skills Dictionary',
                'status' => $skillCount > 0 ? 'Ready' : 'Draft',
                'records' => $skillCount,
                'updated_at' => $skillCount > 0 ? 'Available' : 'Not uploaded',
            ],
            [
                'name' => 'Job Roles Mapping',
                'status' => $jobCount > 0 ? 'Ready' : 'Planned',
                'records' => $jobCount,
                'updated_at' => $jobCount > 0 ? 'Available' : 'Not uploaded',
            ],
        ];

        $this->adminView('admin/datasets', [
            'datasets' => $datasets,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function uploadDataset(): void
    {
        $this->requireAdmin();

        $type = trim($_POST['dataset_type'] ?? '');
        if (!in_array($type, ['careers', 'jobs', 'skills'], true)) {
            $this->redirect('/admin/datasets?error=Invalid%20dataset%20type');
        }

        if (empty($_FILES['dataset_file']) || ($_FILES['dataset_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->redirect('/admin/datasets?error=Please%20upload%20a%20CSV%20file');
        }

        $file = $_FILES['dataset_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            $this->redirect('/admin/datasets?error=Only%20CSV%20files%20are%20allowed');
        }

        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            $this->redirect('/admin/datasets?error=Unable%20to%20read%20the%20CSV%20file');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->redirect('/admin/datasets?error=CSV%20file%20is%20empty');
        }

        $headerMap = $this->mapCsvHeader($header);

        $inserted = 0;
        if ($type === 'careers') {
            $inserted = $this->importCareersCsv($handle, $headerMap);
        } elseif ($type === 'jobs') {
            $inserted = $this->importJobsCsv($handle, $headerMap);
        } elseif ($type === 'skills') {
            $inserted = $this->importSkillsCsv($handle, $headerMap);
        }

        fclose($handle);

        $this->redirect('/admin/datasets?message=Imported%20' . $inserted . '%20record(s)%20for%20' . $type);
    }

    public function system(): void
    {
        $this->requireAdmin();

        $config = require __DIR__ . '/../../config/config.php';
        $aiBaseUrl = rtrim($config['ai']['base_url'] ?? 'http://localhost:5001', '/');
        $aiHealth = $this->checkAiHealth($aiBaseUrl . '/health');

        $cvAnalysisModel = new CvAnalysis();
        $recentCvAnalyses = $cvAnalysisModel->getAllWithUser(5);

        $system = [
            'php' => PHP_VERSION,
            'server_time' => date('Y-m-d H:i:s'),
            'environment' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server',
            'database' => 'Connected',
            'ai_service' => $aiHealth['status'],
            'ai_details' => $aiHealth['details'],
            'ai_latency' => $aiHealth['latency'],
        ];

        $this->adminView('admin/system', [
            'system' => $system,
            'recentCvAnalyses' => $recentCvAnalyses,
        ]);
    }

    public function cvAnalyses(): void
    {
        $this->requireAdmin();

        $cvAnalysisModel = new CvAnalysis();
        $analyses = $cvAnalysisModel->getAllWithUser();

        $this->adminView('admin/cv_analyses', [
            'analyses' => $analyses,
        ]);
    }

    public function exportCvAnalyses(): void
    {
        $this->requireAdmin();

        $cvAnalysisModel = new CvAnalysis();
        $analyses = $cvAnalysisModel->getAllWithUser();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="cv_analyses_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'User Name',
            'User Email',
            'CV File',
            'CV Uploaded At',
            'Summary',
            'Score',
            'Missing Skills',
            'Feedback',
            'Analysis Created At',
        ]);

        foreach ($analyses as $analysis) {
            fputcsv($output, [
                $analysis['user_name'] ?? '',
                $analysis['user_email'] ?? '',
                $analysis['file_name'] ?? '',
                $analysis['uploaded_at'] ?? '',
                $analysis['summary'] ?? '',
                $analysis['score'] ?? '',
                implode(', ', $analysis['missing_skills'] ?? []),
                implode(' | ', $analysis['feedback'] ?? []),
                $analysis['created_at'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    public function careers(): void
    {
        $this->requireAdmin();

        $careerModel = new Career();
        $jobModel = new Job();

        $careers = $careerModel->all();
        $jobs = $jobModel->all();

        $message = $_GET['message'] ?? '';
        $error = $_GET['error'] ?? '';

        $this->adminView('admin/careers', [
            'careers' => $careers,
            'jobs' => $jobs,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function addCareer(): void
    {
        $this->requireAdmin();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $requiredSkills = trim($_POST['required_skills'] ?? '');

        if ($title === '') {
            $this->redirect('/admin/careers?error=Career%20title%20is%20required');
        }

        $careerModel = new Career();
        $careerModel->create($title, $description, $requiredSkills);

        $this->redirect('/admin/careers?message=Career%20added');
    }

    public function deleteCareer(): void
    {
        $this->requireAdmin();

        $careerId = (int)($_POST['career_id'] ?? 0);
        if ($careerId <= 0) {
            $this->redirect('/admin/careers?error=Invalid%20career');
        }

        $careerModel = new Career();
        $careerModel->delete($careerId);

        $this->redirect('/admin/careers?message=Career%20removed');
    }

    public function addJob(): void
    {
        $this->requireAdmin();

        $careerId = (int)($_POST['career_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $requiredSkills = trim($_POST['required_skills'] ?? '');

        if ($title === '') {
            $this->redirect('/admin/careers?error=Job%20title%20is%20required');
        }

        $jobModel = new Job();
        $jobModel->create($careerId > 0 ? $careerId : null, $title, $level, $location, $requiredSkills);

        $this->redirect('/admin/careers?message=Job%20added');
    }

    public function deleteJob(): void
    {
        $this->requireAdmin();

        $jobId = (int)($_POST['job_id'] ?? 0);
        if ($jobId <= 0) {
            $this->redirect('/admin/careers?error=Invalid%20job');
        }

        $jobModel = new Job();
        $jobModel->delete($jobId);

        $this->redirect('/admin/careers?message=Job%20removed');
    }

    private function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? '') === 'admin';
    }

    private function requireAdmin(): void
    {
        $this->requireAuth();

        if (!$this->isAdmin()) {
            http_response_code(403);
            $this->view('errors/forbidden', [
                'message' => 'You do not have permission to access the admin dashboard.',
            ]);
            exit;
        }
    }

    private function adminView(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/layouts/admin_header.php';
        require __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../Views/layouts/footer.php';
    }

    private function checkAiHealth(string $endpoint): array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
        ]);

        $start = microtime(true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $latency = (microtime(true) - $start) * 1000;

        if ($response === false || $error) {
            return [
                'status' => 'Offline',
                'details' => 'Connection failed',
                'latency' => null,
            ];
        }

        $decoded = json_decode($response, true);
        $status = ($statusCode >= 200 && $statusCode < 300) ? 'Online' : 'Degraded';

        return [
            'status' => $status,
            'details' => $decoded['status'] ?? ('HTTP ' . $statusCode),
            'latency' => round($latency, 2),
        ];
    }

    private function mapCsvHeader(array $header): array
    {
        $map = [];
        foreach ($header as $index => $label) {
            $key = strtolower(trim((string)$label));
            $map[$key] = $index;
        }
        return $map;
    }

    private function importCareersCsv($handle, array $headerMap): int
    {
        if (!isset($headerMap['title'])) {
            $this->redirect('/admin/datasets?error=CSV%20must%20include%20a%20title%20column%20for%20careers');
        }

        $careerModel = new Career();
        $inserted = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $title = trim((string)($row[$headerMap['title']] ?? ''));
            if ($title === '') {
                continue;
            }
            $description = trim((string)($row[$headerMap['description']] ?? ''));
            $requiredSkills = trim((string)($row[$headerMap['required_skills']] ?? ''));

            $careerModel->create($title, $description, $requiredSkills);
            $inserted++;
        }

        return $inserted;
    }

    private function importJobsCsv($handle, array $headerMap): int
    {
        if (!isset($headerMap['title'])) {
            $this->redirect('/admin/datasets?error=CSV%20must%20include%20a%20title%20column%20for%20jobs');
        }

        $jobModel = new Job();
        $careerModel = new Career();
        $inserted = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $title = trim((string)($row[$headerMap['title']] ?? ''));
            if ($title === '') {
                continue;
            }

            $level = trim((string)($row[$headerMap['level']] ?? ''));
            $location = trim((string)($row[$headerMap['location']] ?? ''));
            $requiredSkills = trim((string)($row[$headerMap['required_skills']] ?? ''));

            $careerId = null;
            if (isset($headerMap['career_id'])) {
                $careerId = (int)($row[$headerMap['career_id']] ?? 0) ?: null;
            } elseif (isset($headerMap['career_title'])) {
                $careerTitle = trim((string)($row[$headerMap['career_title']] ?? ''));
                if ($careerTitle !== '') {
                    $careerId = $careerModel->findIdByTitle($careerTitle);
                }
            }

            $jobModel->create($careerId, $title, $level, $location, $requiredSkills);
            $inserted++;
        }

        return $inserted;
    }

    private function importSkillsCsv($handle, array $headerMap): int
    {
        $skillModel = new Skill();
        $inserted = 0;

        $nameIndex = $headerMap['skill_name'] ?? $headerMap['name'] ?? null;
        if ($nameIndex === null) {
            $this->redirect('/admin/datasets?error=CSV%20must%20include%20a%20skill_name%20or%20name%20column');
        }

        while (($row = fgetcsv($handle)) !== false) {
            $name = trim((string)($row[$nameIndex] ?? ''));
            if ($name === '') {
                continue;
            }

            $skillModel->createIfMissing($name);
            $inserted++;
        }

        return $inserted;
    }
}
