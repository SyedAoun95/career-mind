<?php
$stats = $stats ?? [
    'totalUsers' => 0,
    'adminUsers' => 0,
    'studentUsers' => 0,
    'profiles' => 0,
    'skills' => 0,
    'interests' => 0,
];
?>

<style>
    :root {
        --admin-primary: #1f2a44;
        --admin-accent: #6c8cff;
        --admin-accent-soft: rgba(108, 140, 255, 0.15);
        --admin-surface: #ffffff;
        --admin-muted: #6b7280;
    }

    .admin-hero {
        background: linear-gradient(135deg, #202b4b 0%, #4f73ff 100%);
        color: white;
        padding: 2.2rem;
        border-radius: 26px;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.25);
        position: relative;
        overflow: hidden;
    }

    .admin-hero::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -60px;
        top: -80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        filter: blur(2px);
    }

    .admin-card {
        background: var(--admin-surface);
        border-radius: 20px;
        padding: 1.6rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        height: 100%;
        border: 1px solid rgba(15, 23, 42, 0.04);
    }

    .admin-stat {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .admin-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: white;
        background: linear-gradient(135deg, #4f73ff, #7c9bff);
    }

    .admin-stat-value {
        font-size: 2.1rem;
        font-weight: 700;
        color: var(--admin-primary);
    }

    .admin-section-title {
        font-weight: 600;
        color: var(--admin-primary);
        margin-bottom: 1rem;
    }

    .admin-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8rem;
        background: rgba(255, 255, 255, 0.18);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .admin-action {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 16px;
        padding: 1rem 1.1rem;
        display: flex;
        gap: 0.9rem;
        align-items: center;
        background: #f7f8ff;
    }

    .admin-action i {
        color: var(--admin-accent);
        font-size: 1.2rem;
    }

    .admin-muted {
        color: var(--admin-muted);
    }

    .admin-quick-btn {
        border-radius: 12px;
        border: 1px solid var(--admin-accent);
        color: var(--admin-accent);
        font-weight: 600;
        background: transparent;
    }

    .admin-quick-btn:hover {
        background: var(--admin-accent);
        color: white;
    }
</style>

<div class="admin-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <h2 class="mb-2">Admin Dashboard</h2>
            <p class="mb-0">Operational overview for users, data, and AI readiness.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="admin-tag"><i class="fas fa-shield-check"></i> System Online</span>
            <span class="admin-tag"><i class="fas fa-server"></i> Data Layer Active</span>
            <span class="admin-tag"><i class="fas fa-wave-square"></i> AI Services Ready</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-user-group"></i></div>
                <div>
                    <div class="admin-stat-value"><?php echo $stats['totalUsers']; ?></div>
                    <small class="text-muted">Total Registered Users</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-id-badge"></i></div>
                <div>
                    <div class="admin-stat-value"><?php echo $stats['adminUsers']; ?></div>
                    <small class="text-muted">Admin Accounts</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="admin-stat-value"><?php echo $stats['studentUsers']; ?></div>
                    <small class="text-muted">Student Accounts</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="admin-card">
            <h5 class="admin-section-title">Data Coverage</h5>
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="admin-action">
                        <i class="fas fa-address-card"></i>
                        <div>
                            <div class="fw-semibold"><?php echo $stats['profiles']; ?></div>
                            <small class="text-muted">Profiles</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="admin-action">
                        <i class="fas fa-bolt"></i>
                        <div>
                            <div class="fw-semibold"><?php echo $stats['skills']; ?></div>
                            <small class="text-muted">Skills</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="admin-action">
                        <i class="fas fa-bookmark"></i>
                        <div>
                            <div class="fw-semibold"><?php echo $stats['interests']; ?></div>
                            <small class="text-muted">Interests</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 text-muted">
                These metrics represent live counts from the current database tables.
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card">
            <h5 class="admin-section-title">Admin Tasks</h5>
            <div class="d-flex flex-column gap-3">
                <div class="admin-action">
                    <i class="fas fa-users-gear"></i>
                    <div>
                        <div class="fw-semibold">User & Role Management</div>
                        <small class="text-muted">Review users, assign roles, and monitor access.</small>
                    </div>
                </div>
                <div class="admin-action">
                    <i class="fas fa-file-lines"></i>
                    <div>
                        <div class="fw-semibold">CV Analysis Oversight</div>
                        <small class="text-muted">Audit CV feedback and quality metrics.</small>
                    </div>
                </div>
                <div class="admin-action">
                    <i class="fas fa-diagram-project"></i>
                    <div>
                        <div class="fw-semibold">Recommendation Models</div>
                        <small class="text-muted">Manage datasets and model readiness (Phase 2).</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-8">
        <div class="admin-card">
            <h5 class="admin-section-title">System Highlights</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="fas fa-circle-check text-success me-2"></i>Three-tier architecture ready for scaling.</li>
                <li class="mb-2"><i class="fas fa-circle-check text-success me-2"></i>Secure auth and role-based access control active.</li>
                <li class="mb-2"><i class="fas fa-circle-check text-success me-2"></i>AI/ML modules prepared for phase 2 integration.</li>
                <li><i class="fas fa-hourglass-half text-warning me-2"></i>Datasets management screens pending implementation.</li>
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card">
            <h5 class="admin-section-title">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a class="btn admin-quick-btn" href="/admin/users">Review new users</a>
                <a class="btn admin-quick-btn" href="/admin/datasets">Upload dataset</a>
                <a class="btn admin-quick-btn" href="/admin/system">View system logs</a>
            </div>
        </div>
    </div>
</div>
