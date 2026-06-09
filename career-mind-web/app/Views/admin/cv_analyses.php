<?php
$analyses = $analyses ?? [];
?>

<style>
    .admin-panel {
        background: #ffffff;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .admin-table th {
        font-weight: 600;
        color: #1f2a44;
    }

    .pill {
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: rgba(79, 115, 255, 0.12);
        color: #3b5bdb;
    }
</style>

<div class="admin-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">CV Analysis Auditing</h3>
            <p class="text-muted mb-0">Review recent CV analyses and download reports.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/cv-analyses/export" class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Download CSV
            </a>
            <a href="/admin/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>CV File</th>
                    <th>Score</th>
                    <th>Missing Skills</th>
                    <th>Feedback</th>
                    <th>Analyzed</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($analyses)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No CV analyses found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($analyses as $analysis): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($analysis['user_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($analysis['user_email'] ?? ''); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($analysis['file_name'] ?? ''); ?></div>
                                <small class="text-muted">Uploaded: <?php echo htmlspecialchars($analysis['uploaded_at'] ?? ''); ?></small>
                            </td>
                            <td>
                                <span class="pill"><?php echo htmlspecialchars((string)($analysis['score'] ?? 'N/A')); ?></span>
                            </td>
                            <td>
                                <?php if (!empty($analysis['missing_skills'])): ?>
                                    <?php echo htmlspecialchars(implode(', ', $analysis['missing_skills'])); ?>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($analysis['feedback'])): ?>
                                    <ul class="mb-0">
                                        <?php foreach ($analysis['feedback'] as $tip): ?>
                                            <li><?php echo htmlspecialchars($tip); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted">No feedback</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($analysis['created_at'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
