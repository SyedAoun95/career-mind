<?php
$skillCount = is_array($skills) ? count($skills) : 0;
$interestCount = is_array($interests) ? count($interests) : 0;
$profileFields = ['age', 'education_level', 'institution', 'graduation_year'];
$completedFields = 0;
if (!empty($profile)) {
    foreach ($profileFields as $field) {
        if (!empty($profile[$field])) $completedFields++;
    }
}
$profileCompletion = count($profileFields) > 0 ? ($completedFields / count($profileFields)) * 100 : 0;
$cvAnalysis = $cvAnalysis ?? null;
$careerRecommendations = $careerRecommendations ?? [];
$jobRecommendations = $jobRecommendations ?? [];
$message = $message ?? '';
$error = $error ?? '';
$educationLevel = $profile['education_level'] ?? '';
$graduationYear = $profile['graduation_year'] ?? '';
$selectedJobLevel = trim($_POST['job_level'] ?? '');
$selectedJobType = trim($_POST['job_type'] ?? '');
$careerPrediction = $careerPrediction ?? null;
$careerPredictionSource = $careerPredictionSource ?? 'live';
$mlServiceStatus = $mlServiceStatus ?? 'unavailable';
$insufficientProfileData = $insufficientProfileData ?? false;
$mlStatusLabels = [
    'live' => 'Live Prediction',
    'cached' => 'Using Cached Result',
    'unavailable' => 'ML Service Unavailable',
];
$mlStatusLabel = $mlStatusLabels[$mlServiceStatus] ?? $mlStatusLabels['unavailable'];
$predictionCacheMeta = $predictionCacheMeta ?? ['confidence' => null, 'status' => null, 'last_refreshed' => null];
$predictionTop = $careerPrediction['top_predictions'][0] ?? null;
$lastRefreshedLabel = null;
if (!empty($predictionCacheMeta['last_refreshed'])) {
    try {
        $lastRefreshedLabel = (new DateTime($predictionCacheMeta['last_refreshed']))->format('M j, Y g:i A');
    } catch (Exception $e) {
        $lastRefreshedLabel = $predictionCacheMeta['last_refreshed'];
    }
}
$cachedConfidencePct = $predictionCacheMeta['confidence'] !== null ? round($predictionCacheMeta['confidence'] * 100) : null;
$cacheStatusLabel = $careerPredictionSource === 'cache' ? 'cached' : ($predictionCacheMeta['status'] ?? 'live');
$predictionAlternatives = array_slice($careerPrediction['top_predictions'] ?? [], 1, 3);
$predictionSkills = $predictionSkills ?? [];

// Confidence = how well the user's skills cover the predicted career's required
// skills (many missing -> ~65%, good coverage -> ~85-90%). Returned as a fraction.
$displayShare = function ($label) use ($predictionSkills) {
    return \App\Controllers\ProfileController::matchConfidence((string)$label, $predictionSkills) / 100;
};
$predictionConfidence = $predictionTop ? $displayShare($predictionTop['label'] ?? '') : 0;
// Flag low confidence only when skill coverage is weak (many missing skills).
$lowConfidence = $predictionTop && $predictionConfidence < 0.65;
// Keep the cache-strip confidence consistent with the displayed value.
if ($predictionTop) {
    $cachedConfidencePct = round($predictionConfidence * 100);
}
$matchedSkills = $predictionTop['matched_skills'] ?? [];
$matchedInterests = $predictionTop['matched_interests'] ?? [];
$educationMatches = $predictionTop['education_match'] ?? [];
$predictionSummary = $predictionTop['summary'] ?? '';
$displaySkillsForWhy = !empty($matchedSkills) ? $matchedSkills : $skills;
$displayInterestsForWhy = !empty($matchedInterests) ? $matchedInterests : $interests;
$displayEducationContext = !empty($educationMatches) ? $educationMatches : [];
if (empty($displayEducationContext) && !empty($educationLevel)) {
    $displayEducationContext[] = $educationLevel . (!empty($graduationYear) ? ' (' . $graduationYear . ')' : '');
}
$marketingJobKeywords = ['marketing', 'content', 'brand', 'growth', 'customer', 'campaign', 'insights'];
$marketingSpotlight = [];
$jobFallback = [];
foreach ($jobRecommendations as $rec) {
    $title = strtolower($rec['job_title'] ?? '');
    $reason = strtolower($rec['reason'] ?? '');
    $hasMarketingSignal = false;
    foreach ($marketingJobKeywords as $keyword) {
        if (strpos($title, $keyword) !== false || strpos($reason, $keyword) !== false) {
            $hasMarketingSignal = true;
            break;
        }
    }
    if ($hasMarketingSignal) {
        $marketingSpotlight[] = $rec;
    } else {
        $jobFallback[] = $rec;
    }
}
$jobRecommendations = array_merge($marketingSpotlight, $jobFallback);

$counselingSuggestions = [];
if ($skillCount === 0) {
    $counselingSuggestions[] = 'Add at least 5 skills to improve recommendation accuracy.';
}
if ($interestCount === 0) {
    $counselingSuggestions[] = 'Add interests so the AI can align careers with your preferences.';
}
if (empty($educationLevel)) {
    $counselingSuggestions[] = 'Complete your education level for better eligibility filtering.';
}
if ($cvAnalysis && !empty($cvAnalysis['missing_skills'])) {
    $counselingSuggestions[] = 'Consider learning: ' . implode(', ', array_slice($cvAnalysis['missing_skills'], 0, 4)) . '.';
}
if ($careerPredictionSource === 'cache') {
    $counselingSuggestions[] = 'AI service offline; showing the last cached career prediction.';
}
if (empty($counselingSuggestions)) {
    $counselingSuggestions[] = 'Keep refining your profile with achievements and certifications.';
}
?>

<style>
/* ─── VARIABLES ─── */
:root {
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-dark: #4f46e5;
    --secondary: #06b6d4;
    --accent: #8b5cf6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --surface: #ffffff;
    --surface-glass: rgba(255,255,255,0.96);
    --bg: #f0f2f8;
    --text: #1e293b;
    --text-muted: #64748b;
    --border: rgba(0,0,0,0.06);
    --radius: 20px;
    --radius-sm: 14px;
    --shadow-sm: 0 2px 12px rgba(0,0,0,0.06);
    --shadow: 0 8px 32px rgba(99,102,241,0.10);
    --shadow-lg: 0 20px 60px rgba(99,102,241,0.15);
    --transition: .35s cubic-bezier(.4,0,.2,1);
}

/* ─── GLOBAL ─── */
html { scroll-behavior: smooth; }

/* ─── FADE-IN ON SCROLL ─── */
.fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity .6s ease, transform .6s ease;
}
.fade-up.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ─── HERO BANNER ─── */
.hero-banner {
    position: relative;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 50%, var(--secondary) 100%);
    padding: 2.5rem 2.5rem 2rem;
    border-radius: var(--radius);
    color: #fff;
    margin-bottom: 2rem;
    overflow: visible;
}
.hero-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
@keyframes gradientShift {
    0%,100% { background-position: 0% 50%; }
    50%     { background-position: 100% 50%; }
}
.hero-glow {
    background: linear-gradient(90deg, #fff, rgba(255,255,255,.6), #fff);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmer 3s linear infinite;
}
@keyframes shimmer {
    0%   { background-position: -200% center; }
    100% { background-position: 200% center; }
}

/* ─── PROGRESS RING ─── */
.progress-ring { position: relative; width: 110px; height: 110px; }
.progress-ring circle { fill: none; stroke-width: 8; stroke-linecap: round; transform: rotate(-90deg); transform-origin: 50% 50%; }
.progress-ring-bg { stroke: rgba(255,255,255,.25); }
.progress-ring-fill {
    stroke: #fff;
    stroke-dasharray: 289;
    stroke-dashoffset: calc(289 - (289 * <?php echo $profileCompletion; ?>) / 100);
    transition: stroke-dashoffset 1.2s ease;
}
.progress-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 800; color: #fff; }

/* ─── DROPDOWN ─── */
.user-dropdown-wrapper { position: relative; display: inline-block; }
.user-dropdown-toggle { background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3); border-radius: 50px; padding: .45rem 1.2rem; font-weight: 600; color: #fff; cursor: pointer; transition: var(--transition); }
.user-dropdown-toggle:hover { background: rgba(255,255,255,.35); transform: scale(1.03); }
.user-dropdown-menu { display: none; position: absolute; right: 0; top: 120%; min-width: 180px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 50; overflow: hidden; animation: dropIn .25s ease; }
.user-dropdown-menu.show { display: block; }
@keyframes dropIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.dropdown-link { display: flex; align-items: center; gap: .6rem; padding: .7rem 1.1rem; color: var(--text); text-decoration: none; font-weight: 500; transition: var(--transition); }
.dropdown-link:hover { background: var(--bg); color: var(--primary); }
.dropdown-link + .dropdown-link { border-top: 1px solid var(--border); }

/* ─── STAT CARDS ─── */
.stat-card {
    background: var(--surface-glass);

    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1.6rem 1.2rem;
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: default;
}
.stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    opacity: 0;
    transition: opacity .35s ease;
    z-index: 0;
}
.stat-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: var(--shadow-lg); }
.stat-card:hover::after { opacity: 1; }
.stat-card:hover .stat-number,
.stat-card:hover .stat-label,
.stat-card:hover small { color: #fff !important; position: relative; z-index: 1; }
.stat-number { font-size: 2.4rem; font-weight: 800; color: var(--primary); margin-bottom: .3rem; position: relative; z-index: 1; transition: color .35s ease; }
.stat-label { color: var(--text-muted); font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; position: relative; z-index: 1; transition: color .35s ease; }
.stat-card small { position: relative; z-index: 1; transition: color .35s ease; }

/* ─── GLASS CARD ─── */
.glass-card {
    background: var(--surface-glass);

    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
    overflow: hidden;
}
.glass-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-4px); }
.glass-card .card-body { padding: 2rem; }

/* Keep recommendation lists compact so the column doesn't tower over the CV card */
.collapse-body.rec-scroll.open { max-height: 240px; overflow-y: auto; }
.rec-scroll::-webkit-scrollbar { width: 6px; }
.rec-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

/* ─── SECTION HEADERS ─── */
.section-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: 1.5rem;
}
.section-header .icon-wrap {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    color: #fff;
    flex-shrink: 0;
}
.section-header h5 { margin: 0; font-weight: 700; font-size: 1.15rem; color: var(--text); }
.section-header p  { margin: 0; font-size: .85rem; color: var(--text-muted); }

/* ─── COLLAPSIBLE PANELS ─── */
.collapse-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    padding: .85rem 1.1rem;
    border-radius: var(--radius-sm);
    background: linear-gradient(135deg, rgba(99,102,241,.06), rgba(139,92,246,.06));
    border: 1px solid rgba(99,102,241,.1);
    margin-bottom: .6rem;
    transition: var(--transition);
    font-weight: 600;
    font-size: .95rem;
    color: var(--text);
}
.collapse-toggle:hover {
    background: linear-gradient(135deg, rgba(99,102,241,.12), rgba(139,92,246,.12));
    border-color: rgba(99,102,241,.2);
    transform: translateX(3px);
}
.collapse-toggle .chevron {
    transition: transform .3s ease;
    color: var(--primary);
    font-size: .8rem;
}
.collapse-toggle.active .chevron { transform: rotate(180deg); }
.collapse-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height .45s cubic-bezier(.4,0,.2,1), padding .35s ease;
    padding: 0 .5rem;
}
.collapse-body.open {
    max-height: 2000px;
    padding: .8rem .5rem;
}

/* ─── PROFILE FIELDS ─── */
.profile-field {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.2rem;
    background: var(--bg);
    border-radius: var(--radius-sm);
    margin-bottom: .6rem;
    transition: var(--transition);
    cursor: default;
    border: 1px solid transparent;
}
.profile-field:hover { background: #e8eaf5; transform: translateX(4px); border-color: rgba(99,102,241,.15); }
.profile-field.complete { border-left: 4px solid var(--success); }
.profile-field.incomplete { border-left: 4px solid var(--warning); }

/* ─── QUICK ACTION TILES ─── */
.action-tile {
    background: var(--bg);
    border-radius: var(--radius-sm);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition);
    cursor: pointer;
    border: 1px solid transparent;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.action-tile:hover {
    background: #fff;
    border-color: var(--primary);
    box-shadow: var(--shadow);
    transform: translateY(-5px);
}
.action-tile .tile-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    color: #fff;
    margin-bottom: 1rem;
    transition: transform .3s ease;
}
.action-tile:hover .tile-icon { transform: scale(1.15) rotate(-5deg); }
.action-tile h6 { font-weight: 700; font-size: .95rem; margin-bottom: .3rem; }

/* ─── BUTTONS ─── */
.btn-gradient {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: #fff;
    border: none;
    padding: .7rem 1.8rem;
    border-radius: 50px;
    font-weight: 600;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-gradient::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--accent), var(--primary));
    opacity: 0;
    transition: opacity .35s ease;
}
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,.35); color: #fff; }
.btn-gradient:hover::before { opacity: 1; }
.btn-gradient span, .btn-gradient i { position: relative; z-index: 1; }

/* ─── ALERTS ─── */
.alert-glass {
    background: var(--surface-glass);

    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem 1.4rem;
    animation: slideInRight .4s ease;
}
@keyframes slideInRight { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }

/* ─── LIST ITEMS ─── */
.rec-item {
    padding: .9rem 1.1rem;
    border-radius: var(--radius-sm);
    background: var(--bg);
    margin-bottom: .5rem;
    transition: var(--transition);
    border-left: 3px solid transparent;
}
.rec-item:hover {
    background: #e8eaf5;
    border-left-color: var(--primary);
    transform: translateX(5px);
    box-shadow: var(--shadow-sm);
}

/* ─── BADGES ─── */
.badge-soft {
    padding: .35rem .8rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: .75rem;
    letter-spacing: .5px;
    display: inline-block;
}
.badge-primary { background: rgba(99,102,241,.12); color: var(--primary); }
.badge-success { background: rgba(16,185,129,.12); color: var(--success); }
.badge-warning { background: rgba(245,158,11,.12); color: var(--warning); }
.badge-accent  { background: rgba(139,92,246,.12); color: var(--accent); }
.badge-info    { background: rgba(6,182,212,.12); color: var(--secondary); }

/* ─── PREDICTION CARD ─── */
.prediction-hero {
    background: linear-gradient(135deg, rgba(99,102,241,.08), rgba(139,92,246,.08));
    border: 1px solid rgba(99,102,241,.12);
    border-radius: var(--radius-sm);
    padding: 1.3rem;
    transition: var(--transition);
}
.prediction-hero:hover { border-color: rgba(99,102,241,.25); box-shadow: var(--shadow-sm); }
.confidence-bar {
    height: 8px;
    background: var(--bg);
    border-radius: 50px;
    overflow: hidden;
    margin-top: .5rem;
}
.confidence-fill {
    height: 100%;
    border-radius: 50px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transition: width 1.2s cubic-bezier(.4,0,.2,1);
}

/* ─── LEARNING PATH CARDS ─── */
.learn-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1.2rem;
    transition: var(--transition);
    height: 100%;
}
.learn-card:hover { border-color: var(--accent); box-shadow: var(--shadow); transform: translateY(-3px); }
.learn-card a { text-decoration: none; font-weight: 600; }
.learn-card a:hover { text-decoration: underline; }

/* ─── COUNSELING TIPS ─── */
.tip-item {
    display: flex;
    align-items: flex-start;
    gap: .65rem;
    padding: .7rem 0;
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
}
.tip-item:last-child { border-bottom: none; }
.tip-item:hover { transform: translateX(4px); }
.tip-bullet {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--accent);
    margin-top: .45rem;
    flex-shrink: 0;
}

/* ─── CACHE META STRIP ─── */
.cache-strip {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem;
    align-items: center;
    padding: .65rem 1rem;
    background: rgba(99,102,241,.05);
    border-radius: 50px;
    font-size: .8rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}
.cache-strip .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); display: inline-block; }
.cache-strip .dot.cached { background: var(--warning); }

/* ─── FILE UPLOAD ─── */
.file-upload-zone {
    border: 2px dashed rgba(99,102,241,.25);
    border-radius: var(--radius-sm);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition);
    cursor: pointer;
    background: rgba(99,102,241,.03);
}
.file-upload-zone:hover { border-color: var(--primary); background: rgba(99,102,241,.07); }

/* ─── PULSE ─── */
.pulse { animation: pulse-ring 2s infinite; }
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(99,102,241,.4); }
    70%  { box-shadow: 0 0 0 12px rgba(99,102,241,0); }
    100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
}

/* ─── RESPONSIVE ─── */
@media (max-width: 768px) {
    .hero-banner { padding: 1.5rem; }
    .glass-card .card-body { padding: 1.3rem; }
    .section-header .icon-wrap { width: 36px; height: 36px; font-size: .95rem; }
}
</style>

<!-- ═══════════════  HERO BANNER  ═══════════════ -->
<div class="hero-banner fade-up">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h2 class="mb-2 fw-bold">Welcome back, <span class="hero-glow"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span> 👋</h2>
            <p class="mb-0" style="opacity:.85">Your AI-powered career journey continues. Let's build your future together.</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex align-items-center justify-content-lg-end gap-3">
                <div class="user-dropdown-wrapper">
                    <button class="user-dropdown-toggle" id="accountDropdownBtn">
                        <i class="fas fa-user-circle me-2"></i>Account
                        <i class="fas fa-chevron-down ms-2" style="font-size:.7rem"></i>
                    </button>
                    <div class="user-dropdown-menu" id="accountDropdownMenu">
                        <a href="/profile" class="dropdown-link"><i class="fas fa-id-card"></i>Profile</a>
                        <a href="/logout" class="dropdown-link"><i class="fas fa-sign-out-alt"></i>Logout</a>
                    </div>
                </div>
                <div class="progress-ring">
                    <svg width="110" height="110">
                        <circle class="progress-ring-bg" cx="55" cy="55" r="46"></circle>
                        <circle class="progress-ring-fill" cx="55" cy="55" r="46"></circle>
                    </svg>
                    <div class="progress-text" id="progressCounter"><?php echo round($profileCompletion); ?>%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════  STAT CARDS  ═══════════════ -->
<div class="row g-4 mb-4">
    <div class="col-md-4 fade-up">
        <div class="stat-card">
            <div class="stat-number" data-count="<?php echo $skillCount; ?>">0</div>
            <div class="stat-label">Skills Stored</div>
            <small class="text-muted">Ready for AI analysis</small>
        </div>
    </div>
    <div class="col-md-4 fade-up">
        <div class="stat-card">
            <div class="stat-number" data-count="<?php echo $interestCount; ?>">0</div>
            <div class="stat-label">Interests</div>
            <small class="text-muted">Career matching data</small>
        </div>
    </div>
    <div class="col-md-4 fade-up">
        <div class="stat-card">
            <div class="stat-number" data-count="<?php echo $completedFields; ?>">0</div>
            <div class="stat-label">Profile Fields</div>
            <small class="text-muted">of <?php echo count($profileFields); ?> completed</small>
        </div>
    </div>
</div>

<!-- ═══════════════  ALERTS  ═══════════════ -->
<?php if ($message): ?>
    <div class="alert-glass mb-3 fade-up" style="border-left:4px solid var(--success)">
        <i class="fas fa-check-circle me-2" style="color:var(--success)"></i><?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-glass mb-3 fade-up" style="border-left:4px solid var(--danger)">
        <i class="fas fa-exclamation-triangle me-2" style="color:var(--danger)"></i><?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- ═══════════════  PROFILE OVERVIEW  ═══════════════ -->
<div class="glass-card mb-4 fade-up">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div class="section-header mb-0">
                <div class="icon-wrap" style="background:linear-gradient(135deg,var(--primary),var(--accent))">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                <div>
                    <h5>Profile Overview</h5>
                    <p>Your career identity at a glance</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn-gradient pulse" href="/profile">
                    <i class="fas fa-edit me-2"></i><span>Edit Profile</span>
                </a>
                <form action="/dashboard/clear-results" method="POST" class="m-0">
                    <button type="submit" class="btn-gradient" style="background:linear-gradient(135deg,#64748b,#475569)">
                        <i class="fas fa-rotate-right me-2"></i><span>Refresh Results</span>
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($profile)): ?>
            <div class="row g-3 mb-4">
                <?php foreach ($profileFields as $field): ?>
                    <?php
                    $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                    $fieldValue = htmlspecialchars($profile[$field] ?? '');
                    $isComplete = !empty($fieldValue);
                    ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="profile-field <?php echo $isComplete ? 'complete' : 'incomplete'; ?>">
                            <div>
                                <strong style="font-size:.85rem"><?php echo $fieldLabel; ?></strong>
                                <p class="mb-0 <?php echo $isComplete ? '' : 'text-muted'; ?>" style="font-size:.9rem">
                                    <?php echo $isComplete ? $fieldValue : 'Not set'; ?>
                                </p>
                            </div>
                            <i class="fas <?php echo $isComplete ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="color:<?php echo $isComplete ? 'var(--success)' : 'var(--warning)'; ?>;font-size:1.1rem"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-user-circle fa-4x mb-3" style="color:var(--primary);opacity:.3"></i>
                <h5 class="text-muted mb-2">No profile data yet</h5>
                <p class="text-muted mb-3">Complete your profile to unlock AI features</p>
                <a class="btn-gradient" href="/profile"><span>Create Profile</span></a>
            </div>
        <?php endif; ?>

        <!-- Quick Action Tiles -->
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="action-tile">
                    <div class="tile-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-light))">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h6>Career Analysis</h6>
                    <small class="text-muted">AI-powered career suggestions</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-tile">
                    <div class="tile-icon" style="background:linear-gradient(135deg,var(--accent),#a78bfa)">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h6>CV Builder</h6>
                    <small class="text-muted">Create and analyze your CV</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-tile">
                    <div class="tile-icon" style="background:linear-gradient(135deg,var(--success),#34d399)">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h6>Job Matches</h6>
                    <small class="text-muted">Find suitable opportunities</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════  CV + RECOMMENDATIONS  ═══════════════ -->
<div class="row g-4 align-items-start">

    <!-- ── CV Upload & Analysis ── -->
    <div class="col-lg-6 fade-up">
        <div class="glass-card">
            <div class="card-body">
                <div class="section-header">
                    <div class="icon-wrap" style="background:linear-gradient(135deg,var(--accent),#a78bfa)">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div>
                        <h5>CV Upload & Analysis</h5>
                        <p>Upload your CV for AI feedback</p>
                    </div>
                </div>

                <form id="cvUploadForm" action="/cv/upload" method="POST" enctype="multipart/form-data" class="mb-3">
                    <div class="file-upload-zone mb-2" onclick="this.querySelector('input').click()">
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:var(--primary);opacity:.5"></i>
                        <p class="mb-1 fw-semibold" style="font-size:.9rem">Click to upload or drag & drop</p>
                        <small class="text-muted">PDF, DOC, DOCX — max 10MB</small>
                        <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required style="display:none" onchange="this.closest('.file-upload-zone').querySelector('p').textContent = this.files[0]?.name || 'Click to upload'">
                    </div>
                    <button id="analyzeCvBtn" class="btn-gradient w-100" type="submit"><i class="fas fa-magic me-2"></i><span>Analyze CV</span></button>
                </form>

                <div id="cvUploadAlert" class="alert-glass mb-3" style="display:none;border-left:4px solid var(--warning)">
                    <i class="fas fa-exclamation-circle me-2" style="color:var(--warning)"></i>
                    <small id="cvUploadAlertText"></small>
                </div>

                <div id="cvUploadSuccess" class="alert-glass mb-3" style="display:none;border-left:4px solid var(--success)">
                    <i class="fas fa-check-circle me-2" style="color:var(--success)"></i>
                    <small style="color:var(--success);font-weight:600">Your result has been generated.</small>
                </div>

                <div id="cvAnalysisResult">
                <?php if ($cvAnalysis): ?>
                    <div class="prediction-hero">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong style="font-size:.9rem"><?php echo htmlspecialchars($cvAnalysis['file_name'] ?? ''); ?></strong>
                            <span class="badge-soft badge-primary">Score: <?php echo htmlspecialchars((string)($cvAnalysis['score'] ?? 'N/A')); ?></span>
                        </div>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($cvAnalysis['summary'] ?? ''); ?></p>

                        <div class="collapse-toggle" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                            <span><i class="fas fa-exclamation-triangle me-2" style="color:var(--warning)"></i>Missing Skills</span>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                        <div class="collapse-body">
                            <?php if (!empty($cvAnalysis['missing_skills'])): ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($cvAnalysis['missing_skills'] as $skill): ?>
                                        <span class="badge-soft badge-warning"><?php echo htmlspecialchars($skill); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">No gaps detected — great job!</span>
                            <?php endif; ?>
                        </div>

                        <div class="collapse-toggle" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                            <span><i class="fas fa-comment-dots me-2" style="color:var(--secondary)"></i>Feedback</span>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                        <div class="collapse-body">
                            <?php if (!empty($cvAnalysis['feedback'])): ?>
                                <?php foreach ($cvAnalysis['feedback'] as $note): ?>
                                    <div class="tip-item">
                                        <div class="tip-bullet"></div>
                                        <span class="small"><?php echo htmlspecialchars($note); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">No feedback yet.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4" style="opacity:.5">
                        <i class="fas fa-file-invoice fa-3x mb-2"></i>
                        <p class="small mb-0">No CV analysis available yet</p>
                    </div>
                <?php endif; ?>
                </div><!-- /#cvAnalysisResult -->
            </div>
        </div>
    </div>

    <!-- ── Career & Job Recommendations ── -->
    <div class="col-lg-6 fade-up">
        <div class="glass-card h-100">
            <div class="card-body">
                <div class="section-header">
                    <div class="icon-wrap" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <h5>Career & Job Recommendations</h5>
                        <p>Personalized AI-driven suggestions</p>
                    </div>
                </div>

                <form action="/recommendations" method="POST" class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <select name="job_level" class="form-select form-select-sm" style="max-width:140px;border-radius:50px;border-color:var(--border)">
                        <option value="">All Levels</option>
                        <option value="entry" <?php echo $selectedJobLevel === 'entry' ? 'selected' : ''; ?>>Entry</option>
                        <option value="junior" <?php echo $selectedJobLevel === 'junior' ? 'selected' : ''; ?>>Junior</option>
                        <option value="mid" <?php echo $selectedJobLevel === 'mid' ? 'selected' : ''; ?>>Mid</option>
                        <option value="senior" <?php echo $selectedJobLevel === 'senior' ? 'selected' : ''; ?>>Senior</option>
                        <option value="intern" <?php echo $selectedJobLevel === 'intern' ? 'selected' : ''; ?>>Intern</option>
                    </select>
                    <select name="job_type" class="form-select form-select-sm" style="max-width:140px;border-radius:50px;border-color:var(--border)">
                        <option value="">All Types</option>
                        <option value="remote" <?php echo $selectedJobType === 'remote' ? 'selected' : ''; ?>>Remote</option>
                        <option value="onsite" <?php echo $selectedJobType === 'onsite' ? 'selected' : ''; ?>>On-site</option>
                        <option value="hybrid" <?php echo $selectedJobType === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                    <button class="btn-gradient btn-sm" type="submit" style="padding:.4rem 1.2rem;font-size:.8rem"><span>Filter</span></button>
                </form>

                <?php if ($selectedJobLevel || $selectedJobType): ?>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php if ($selectedJobLevel): ?>
                            <span class="badge-soft badge-primary"><?php echo htmlspecialchars(ucfirst($selectedJobLevel)); ?></span>
                        <?php endif; ?>
                        <?php if ($selectedJobType): ?>
                            <span class="badge-soft badge-info"><?php echo htmlspecialchars(ucfirst($selectedJobType)); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Career Recommendations -->
                <div class="collapse-toggle active" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                    <span><i class="fas fa-graduation-cap me-2" style="color:var(--primary)"></i>Career Recommendations</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </div>
                <div class="collapse-body open rec-scroll" id="careerRecList">
                    <?php if (!empty($careerRecommendations)): ?>
                        <?php foreach ($careerRecommendations as $rec): ?>
                            <div class="rec-item">
                                <strong><?php echo htmlspecialchars($rec['career_title'] ?? ''); ?></strong>
                                <p class="mb-0 text-muted small"><?php echo htmlspecialchars($rec['reason'] ?? ''); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0 py-2">No career recommendations yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Job Recommendations -->
                <div class="collapse-toggle active" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                    <span><i class="fas fa-briefcase me-2" style="color:var(--success)"></i>Job Recommendations</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </div>
                <div class="collapse-body open rec-scroll" id="jobRecList">
                    <?php if (!empty($marketingSpotlight)): ?>
                        <div class="d-flex align-items-center gap-2 mb-2 p-2" style="background:rgba(16,185,129,.08);border-radius:8px">
                            <i class="fas fa-bullhorn" style="color:var(--success)"></i>
                            <small class="fw-semibold" style="color:var(--success)">Marketing-leaning roles prioritised</small>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($jobRecommendations)): ?>
                        <?php foreach ($jobRecommendations as $rec): ?>
                            <div class="rec-item">
                                <strong><?php echo htmlspecialchars($rec['job_title'] ?? ''); ?></strong>
                                <p class="mb-0 text-muted small"><?php echo htmlspecialchars($rec['reason'] ?? ''); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0 py-2">No job recommendations yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════  ML PREDICTION + INSIGHTS  ═══════════════ -->
<div class="row g-4 mt-1">
    <div class="col-lg-8 fade-up">
        <div class="glass-card">
            <div class="card-body">
                <div class="section-header">
                    <div class="icon-wrap" style="background:linear-gradient(135deg,#6366f1,#ec4899)">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div>
                        <h5>Career Prediction (ML)</h5>
                        <p>Machine-learning powered career forecast</p>
                    </div>
                    <span class="badge-soft <?php echo $mlServiceStatus === 'live' ? 'badge-success' : ($mlServiceStatus === 'cached' ? 'badge-warning' : 'badge-accent'); ?>" style="margin-left:auto;font-size:.72rem">
                        <i class="fas <?php echo $mlServiceStatus === 'live' ? 'fa-signal' : ($mlServiceStatus === 'cached' ? 'fa-database' : 'fa-exclamation-circle'); ?> me-1"></i><?php echo htmlspecialchars($mlStatusLabel); ?>
                    </span>
                </div>

                <?php if ($insufficientProfileData): ?>
                    <div class="alert-glass mb-3" style="border-left:4px solid var(--warning)">
                        <i class="fas fa-info-circle me-2" style="color:var(--warning)"></i>
                        <small>Not enough profile data to predict yet. Add at least 2 skills to your profile for an accurate career forecast.</small>
                    </div>
                    <a href="/profile" class="btn btn-sm btn-primary mb-2">
                        <i class="fas fa-plus me-1"></i>Add skills to your profile
                    </a>
                <?php elseif ($careerPredictionSource === 'cache'): ?>
                    <div class="alert-glass mb-3" style="border-left:4px solid var(--warning)">
                        <i class="fas fa-wifi me-2" style="color:var(--warning)"></i>
                        <small>Showing cached prediction — AI service is currently unreachable.</small>
                    </div>
                <?php elseif ($careerPredictionSource === 'none'): ?>
                    <div class="alert-glass mb-3" style="border-left:4px solid var(--text-muted)">
                        <i class="fas fa-plug me-2"></i>
                        <small>No prediction data cached yet. Connect to the AI service to generate a result.</small>
                    </div>
                <?php endif; ?>

                <?php if (!empty($cacheStatusLabel) || $cachedConfidencePct !== null || !empty($lastRefreshedLabel)): ?>
                    <div class="cache-strip">
                        <span class="dot <?php echo $careerPredictionSource === 'cache' ? 'cached' : ''; ?>"></span>
                        <?php if (!empty($cacheStatusLabel)): ?>
                            <span><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($cacheStatusLabel)); ?></span>
                        <?php endif; ?>
                        <?php if ($cachedConfidencePct !== null): ?>
                            <span>&middot; Confidence: <?php echo $cachedConfidencePct; ?>%</span>
                        <?php endif; ?>
                        <?php if (!empty($lastRefreshedLabel)): ?>
                            <span>&middot; Last refreshed: <?php echo htmlspecialchars($lastRefreshedLabel); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($predictionTop): ?>
                    <div class="prediction-hero mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" id="mlPredLabel"><?php echo htmlspecialchars($predictionTop['label'] ?? ''); ?></h6>
                            <span class="badge-soft badge-primary" id="mlPredPct" style="font-size:.85rem"><?php echo round($predictionConfidence * 100); ?>%</span>
                        </div>
                        <div class="confidence-bar mt-2">
                            <div class="confidence-fill" id="mlPredBar" style="width:<?php echo round($predictionConfidence * 100); ?>%"></div>
                        </div>
                        <?php if ($predictionSummary): ?>
                            <p class="text-muted small mt-2 mb-0" id="mlPredSummary"><?php echo htmlspecialchars($predictionSummary); ?></p>
                        <?php endif; ?>
                        <?php if ($lowConfidence): ?>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <i class="fas fa-exclamation-triangle" style="color:var(--warning)"></i>
                                <small style="color:var(--warning)">Low confidence — add more skills/interests for better accuracy.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php $guidingSkills = !empty($matchedSkills) ? $matchedSkills : ($predictionSkills ?? $skills); ?>
                    <?php if (!empty($guidingSkills)): ?>
                        <div class="mb-2">
                            <small class="text-muted fw-semibold">Skills guiding prediction:</small>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <?php foreach ($guidingSkills as $s): ?>
                                    <span class="badge-soft <?php echo !empty($matchedSkills) ? 'badge-primary' : 'badge-accent'; ?>"><?php echo htmlspecialchars($s); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($matchedInterests) || !empty($interests)): ?>
                        <div class="mb-2">
                            <small class="text-muted fw-semibold">Interests steering prediction:</small>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <?php foreach ((!empty($matchedInterests) ? $matchedInterests : $interests) as $i): ?>
                                    <span class="badge-soft <?php echo !empty($matchedInterests) ? 'badge-success' : 'badge-accent'; ?>"><?php echo htmlspecialchars($i); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($educationMatches) || !empty($educationLevel)): ?>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-graduation-cap me-1"></i>
                            <?php if (!empty($educationMatches)): ?>
                                <?php echo htmlspecialchars(implode(', ', $educationMatches)); ?>
                            <?php else: ?>
                                <?php echo htmlspecialchars($educationLevel); ?><?php if (!empty($graduationYear)): ?> (<?php echo htmlspecialchars($graduationYear); ?>)<?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Alternatives -->
                    <div class="mt-3">
                        <div class="collapse-toggle" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                            <span><i class="fas fa-layer-group me-2" style="color:var(--accent)"></i>Alternative Careers</span>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                        <div class="collapse-body" id="mlPredAlternatives">
                            <?php if (!empty($predictionAlternatives)): ?>
                                <?php foreach ($predictionAlternatives as $alt): ?>
                                    <div class="rec-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong><?php echo htmlspecialchars($alt['label'] ?? ''); ?></strong>
                                            <span class="badge-soft badge-accent"><?php echo round($displayShare($alt['label'] ?? '') * 100); ?>%</span>
                                        </div>
                                        <?php if (!empty($alt['summary'])): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($alt['summary']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small mb-0 py-2">No alternatives available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Why Recommended -->
                    <div class="mt-2">
                        <div class="collapse-toggle" onclick="this.classList.toggle('active');this.nextElementSibling.classList.toggle('open')">
                            <span><i class="fas fa-question-circle me-2" style="color:var(--secondary)"></i>Why these were recommended</span>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                        <div class="collapse-body">
                            <div class="mb-2">
                                <strong class="small">Skills matched:</strong>
                                <?php if (!empty($displaySkillsForWhy)): ?>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <?php foreach ($displaySkillsForWhy as $skill): ?>
                                            <span class="badge-soft badge-primary"><?php echo htmlspecialchars($skill); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small"> No skills provided yet.</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <strong class="small">Interests matched:</strong>
                                <?php if (!empty($displayInterestsForWhy)): ?>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <?php foreach ($displayInterestsForWhy as $interest): ?>
                                            <span class="badge-soft badge-success"><?php echo htmlspecialchars($interest); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small"> No interests provided yet.</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong class="small">Education:</strong>
                                <?php if (!empty($displayEducationContext)): ?>
                                    <span class="small"><?php echo htmlspecialchars(implode(', ', $displayEducationContext)); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small"> Education not set.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="text-center py-4" style="opacity:.4">
                        <i class="fas fa-robot fa-3x mb-2"></i>
                        <p class="small mb-0">Prediction not available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Right Column: Learning Path + Counseling ── -->
    <div class="col-lg-4 fade-up">
        <?php if (!empty($learningPathSuggestions)): ?>
        <div class="glass-card mb-4">
            <div class="card-body">
                <div class="section-header">
                    <div class="icon-wrap" style="background:linear-gradient(135deg,var(--warning),#fbbf24)">
                        <i class="fas fa-road"></i>
                    </div>
                    <div>
                        <h5>Learning Paths</h5>
                        <p>Close your skill gaps</p>
                    </div>
                </div>
                <?php foreach ($learningPathSuggestions as $path): ?>
                    <div class="learn-card mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong style="font-size:.9rem"><?php echo htmlspecialchars($path['skill'] ?? 'Focus area'); ?></strong>
                            <?php if (!empty($path['track'])): ?>
                                <span class="badge-soft badge-accent" style="font-size:.7rem"><?php echo htmlspecialchars($path['track']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($path['summary'])): ?>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($path['summary']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($path['resource'])): ?>
                            <a href="<?php echo htmlspecialchars($path['resource']['url']); ?>" target="_blank" rel="noopener" class="small" style="color:var(--primary)">
                                <i class="fas fa-external-link-alt me-1"></i><?php echo htmlspecialchars($path['resource']['label'] ?? 'Explore'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="glass-card">
            <div class="card-body">
                <div class="section-header">
                    <div class="icon-wrap" style="background:linear-gradient(135deg,var(--success),#34d399)">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h5>Counseling Tips</h5>
                        <p>Personalised guidance</p>
                    </div>
                </div>
                <?php foreach ($counselingSuggestions as $tip): ?>
                    <div class="tip-item">
                        <div class="tip-bullet"></div>
                        <span class="small"><?php echo htmlspecialchars($tip); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════  JAVASCRIPT  ═══════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ── Fade-in on scroll (IntersectionObserver) ── */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    /* ── Animated number counting ── */
    document.querySelectorAll('.stat-number[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10);
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 40));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current;
        }, 35);
    });

    /* ── Progress ring counter ── */
    const pText = document.getElementById('progressCounter');
    if (pText) {
        const target = <?php echo round($profileCompletion); ?>;
        let cur = 0;
        const inc = target / 60;
        const animate = () => {
            cur += inc;
            if (cur >= target) { cur = target; }
            pText.textContent = Math.round(cur) + '%';
            if (cur < target) requestAnimationFrame(animate);
        };
        setTimeout(animate, 400);
    }

    /* ── Dropdown ── */
    const togBtn = document.getElementById('accountDropdownBtn');
    const togMenu = document.getElementById('accountDropdownMenu');
    if (togBtn && togMenu) {
        togBtn.addEventListener('click', e => { e.stopPropagation(); togMenu.classList.toggle('show'); });
        document.addEventListener('click', () => togMenu.classList.remove('show'));
    }

    /* ── Smooth scroll for anchor links ── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    /* ── Ripple effect on action tiles ── */
    document.querySelectorAll('.action-tile').forEach(tile => {
        tile.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.cssText = 'position:absolute;border-radius:50%;background:rgba(99,102,241,.15);width:'+size+'px;height:'+size+'px;left:'+(e.clientX - rect.left - size/2)+'px;top:'+(e.clientY - rect.top - size/2)+'px;transform:scale(0);animation:rippleOut .6s ease;pointer-events:none;';
            this.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ── Ripple keyframes ── */
    if (!document.getElementById('ripple-style')) {
        const s = document.createElement('style');
        s.id = 'ripple-style';
        s.textContent = '@keyframes rippleOut{to{transform:scale(4);opacity:0;}}';
        document.head.appendChild(s);
    }

});
</script>

<script>
/* ── CV upload via AJAX: loading state + inline results, no full-page refresh ── */
(function () {
    const form = document.getElementById('cvUploadForm');
    const btn = document.getElementById('analyzeCvBtn');
    const result = document.getElementById('cvAnalysisResult');
    const alertBox = document.getElementById('cvUploadAlert');
    const alertText = document.getElementById('cvUploadAlertText');
    const successBox = document.getElementById('cvUploadSuccess');
    if (!form || !btn || !result) return;

    const btnDefault = btn.innerHTML;

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function setLoading(on) {
        btn.disabled = on;
        btn.innerHTML = on
            ? '<i class="fas fa-spinner fa-spin me-2"></i><span>Analyzing…</span>'
            : btnDefault;
    }

    function showError(message) {
        if (!alertBox) return;
        alertText.textContent = message;
        alertBox.style.display = '';
    }

    function hideError() {
        if (alertBox) alertBox.style.display = 'none';
    }

    function showSuccess() {
        if (successBox) successBox.style.display = '';
    }

    // Refresh the Career Prediction card with the fresh CV-driven result.
    function updatePrediction(pred) {
        var label = document.getElementById('mlPredLabel');
        var pct = document.getElementById('mlPredPct');
        var bar = document.getElementById('mlPredBar');
        var summary = document.getElementById('mlPredSummary');
        var alts = document.getElementById('mlPredAlternatives');
        // If the prediction card isn't on the page (e.g. it was an empty state),
        // reload so the server can render it fresh.
        if (!label || !pct || !bar) { window.location.reload(); return; }

        label.textContent = pred.label || '';
        pct.textContent = (pred.confidence != null ? pred.confidence : 0) + '%';
        bar.style.width = (pred.confidence != null ? pred.confidence : 0) + '%';
        if (summary && pred.summary) summary.textContent = pred.summary;

        if (alts) {
            var list = (pred.alternatives || []);
            alts.innerHTML = list.length
                ? list.map(function (a) {
                    return '<div class="rec-item"><div class="d-flex justify-content-between align-items-center">' +
                        '<strong>' + esc(a.label) + '</strong>' +
                        '<span class="badge-soft badge-accent">' + (a.confidence != null ? a.confidence : 0) + '%</span>' +
                        '</div></div>';
                  }).join('')
                : '<p class="text-muted small mb-0 py-2">No alternatives available yet.</p>';
        }
    }

    // Refresh the Career & Job recommendation lists with the fresh CV-driven results.
    function updateRecommendations(rec) {
        function fill(el, items, emptyMsg) {
            if (!el) return;
            items = items || [];
            el.innerHTML = items.length
                ? items.map(function (i) {
                    var reason = i.reason ? '<p class="mb-0 text-muted small">' + esc(i.reason) + '</p>' : '';
                    return '<div class="rec-item"><strong>' + esc(i.title) + '</strong>' + reason + '</div>';
                  }).join('')
                : '<p class="text-muted small mb-0 py-2">' + emptyMsg + '</p>';
        }
        fill(document.getElementById('careerRecList'), rec.careers, 'No career recommendations yet.');
        fill(document.getElementById('jobRecList'), rec.jobs, 'No job recommendations yet.');
    }

    function hideSuccess() {
        if (successBox) successBox.style.display = 'none';
    }

    function renderAnalysis(a) {
        const score = (a.score === null || a.score === undefined) ? 'N/A' : a.score;
        const missing = (a.missing_skills || []).length
            ? '<div class="d-flex flex-wrap gap-2">' +
                a.missing_skills.map(s => '<span class="badge-soft badge-warning">' + esc(s) + '</span>').join('') +
              '</div>'
            : '<span class="text-muted small">No gaps detected — great job!</span>';
        const feedback = (a.feedback || []).length
            ? a.feedback.map(f =>
                '<div class="tip-item"><div class="tip-bullet"></div><span class="small">' + esc(f) + '</span></div>'
              ).join('')
            : '<span class="text-muted small">No feedback yet.</span>';

        result.innerHTML =
            '<div class="prediction-hero">' +
                '<div class="d-flex justify-content-between align-items-center mb-2">' +
                    '<strong style="font-size:.9rem">' + esc(a.file_name) + '</strong>' +
                    '<span class="badge-soft badge-primary">Score: ' + esc(score) + '</span>' +
                '</div>' +
                '<p class="text-muted small mb-2">' + esc(a.summary) + '</p>' +
                '<div class="collapse-toggle" onclick="this.classList.toggle(\'active\');this.nextElementSibling.classList.toggle(\'open\')">' +
                    '<span><i class="fas fa-exclamation-triangle me-2" style="color:var(--warning)"></i>Missing Skills</span>' +
                    '<i class="fas fa-chevron-down chevron"></i>' +
                '</div>' +
                '<div class="collapse-body">' + missing + '</div>' +
                '<div class="collapse-toggle" onclick="this.classList.toggle(\'active\');this.nextElementSibling.classList.toggle(\'open\')">' +
                    '<span><i class="fas fa-comment-dots me-2" style="color:var(--secondary)"></i>Feedback</span>' +
                    '<i class="fas fa-chevron-down chevron"></i>' +
                '</div>' +
                '<div class="collapse-body">' + feedback + '</div>' +
            '</div>';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const fileInput = form.querySelector('input[type=file]');
        if (!fileInput || !fileInput.files.length) {
            showError('Please choose a CV file first.');
            return;
        }
        hideError();
        hideSuccess();
        setLoading(true);

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.ok) {
                renderAnalysis(data.analysis);
                if (data.prediction) updatePrediction(data.prediction);
                if (data.recommendations) updateRecommendations(data.recommendations);
                showSuccess();
            } else {
                showError(data.error || 'CV analysis failed. Please try again.');
            }
        })
        .catch(() => showError('Could not reach the server. Please try again.'))
        .finally(() => setLoading(false));
    });
})();
</script>
