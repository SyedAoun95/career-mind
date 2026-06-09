<?php
$datasets = $datasets ?? [];
$message = $message ?? '';
$error = $error ?? '';
?>

<style>
    .admin-panel {
        background: #ffffff;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .dataset-card {
        border-radius: 16px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        padding: 1.2rem 1.4rem;
        background: #f8f9ff;
    }

    .status-pill {
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: rgba(79, 115, 255, 0.12);
        color: #3b5bdb;
    }

    .dataset-form {
        border-radius: 16px;
        border: 1px dashed rgba(15, 23, 42, 0.2);
        padding: 1.5rem;
        background: #ffffff;
    }
</style>

<div class="admin-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Dataset Management</h3>
            <p class="text-muted mb-0">Track datasets used for AI/ML training and recommendations.</p>
        </div>
        <a href="/admin/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($datasets as $dataset): ?>
            <div class="col-md-4">
                <div class="dataset-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><?php echo htmlspecialchars($dataset['name']); ?></h6>
                        <span class="status-pill"><?php echo htmlspecialchars($dataset['status']); ?></span>
                    </div>
                    <p class="text-muted mb-2">Records: <?php echo (int)$dataset['records']; ?></p>
                    <p class="text-muted mb-3">Last Updated: <?php echo htmlspecialchars($dataset['updated_at']); ?></p>
                    <span class="text-muted small">Use the upload form below.</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="dataset-form mt-4">
        <h5 class="mb-2">Upload CSV Dataset</h5>
        <p class="text-muted">Supported datasets: careers and jobs. The first row must be a header.</p>

        <form action="/admin/datasets/upload" method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Dataset Type</label>
                <select class="form-select" name="dataset_type" required>
                    <option value="careers">Careers</option>
                    <option value="skills">Skills</option>
                    <option value="jobs">Jobs</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">CSV File</label>
                <input class="form-control" type="file" name="dataset_file" accept=".csv" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Upload</button>
            </div>
        </form>

        <div class="mt-3">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-1">Careers CSV Header</h6>
                    <div class="text-muted small">title,description,required_skills</div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-1">Skills CSV Header</h6>
                    <div class="text-muted small">skill_name OR name</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <h6 class="mb-1">Jobs CSV Header</h6>
                    <div class="text-muted small">title,level,location,required_skills,career_id OR career_title</div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        Uploading careers/jobs will feed the AI recommendation engine directly.
    </div>
</div>
