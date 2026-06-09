<?php
$system = $system ?? [];
$recentCvAnalyses = $recentCvAnalyses ?? [];
?>

<style>
    .admin-panel {
        background: #ffffff;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .system-card {
        border-radius: 16px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        padding: 1.4rem;
        background: #f8f9ff;
    }

    .system-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
</style>

<div class="admin-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">System Monitoring</h3>
            <p class="text-muted mb-0">Operational status of platform services.</p>
        </div>
        <a href="/admin/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="system-card">
                <div class="system-label">PHP Version</div>
                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($system['php'] ?? ''); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="system-card">
                <div class="system-label">Server Time</div>
                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($system['server_time'] ?? ''); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="system-card">
                <div class="system-label">Environment</div>
                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($system['environment'] ?? ''); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="system-card">
                <div class="system-label">Database</div>
                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($system['database'] ?? ''); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="system-card">
                <div class="system-label">AI Service</div>
                <div class="fw-semibold mt-2"><?php echo htmlspecialchars($system['ai_service'] ?? ''); ?></div>
                <div class="text-muted small mt-1">
                    Status: <?php echo htmlspecialchars($system['ai_details'] ?? 'Unknown'); ?>
                    <?php if (!empty($system['ai_latency'])): ?>
                        · <?php echo htmlspecialchars((string)$system['ai_latency']); ?> ms
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h5 class="mb-3">Recent CV Analyses</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>CV File</th>
                        <th>Score</th>
                        <th>Analyzed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentCvAnalyses)): ?>
                        <tr>
                            <td colspan="4" class="text-muted text-center">No recent CV analyses found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentCvAnalyses as $analysis): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($analysis['user_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($analysis['file_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars((string)($analysis['score'] ?? 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars($analysis['created_at'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        Monitoring shows real-time AI health plus recent CV analysis activity.
    </div>
</div>
