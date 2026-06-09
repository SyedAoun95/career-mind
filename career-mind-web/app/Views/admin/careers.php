<?php
$careers = $careers ?? [];
$jobs = $jobs ?? [];
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

    .admin-section-card {
        border-radius: 16px;
        padding: 1.4rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #f8f9ff;
    }
</style>

<div class="admin-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Careers & Jobs Management</h3>
            <p class="text-muted mb-0">Maintain career paths, job roles, and required skills.</p>
        </div>
        <a href="/admin/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="admin-section-card">
                <h5 class="mb-3">Add Career</h5>
                <form action="/admin/careers/add" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Career Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Required Skills (comma-separated)</label>
                        <input type="text" name="required_skills" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Career</button>
                </form>
            </div>

            <div class="admin-section-card mt-4">
                <h5 class="mb-3">Current Careers</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Required Skills</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($careers)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No careers added yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($careers as $career): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($career['title'] ?? ''); ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($career['required_skills'] ?? ''); ?></td>
                                        <td class="text-end">
                                            <form action="/admin/careers/delete" method="POST">
                                                <input type="hidden" name="career_id" value="<?php echo (int)($career['id'] ?? 0); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="admin-section-card">
                <h5 class="mb-3">Add Job Role</h5>
                <form action="/admin/jobs/add" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Career Path (optional)</label>
                        <select name="career_id" class="form-select">
                            <option value="">Select career</option>
                            <?php foreach ($careers as $career): ?>
                                <option value="<?php echo (int)$career['id']; ?>"><?php echo htmlspecialchars($career['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Level</label>
                            <input type="text" name="level" class="form-control" placeholder="Entry Level">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Remote / City">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Required Skills (comma-separated)</label>
                        <input type="text" name="required_skills" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Job</button>
                </form>
            </div>

            <div class="admin-section-card mt-4">
                <h5 class="mb-3">Current Job Roles</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Career</th>
                                <th>Level</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jobs)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No job roles added yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['title'] ?? ''); ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($job['career_title'] ?? 'Unassigned'); ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($job['level'] ?? ''); ?></td>
                                        <td class="text-end">
                                            <form action="/admin/jobs/delete" method="POST">
                                                <input type="hidden" name="job_id" value="<?php echo (int)($job['id'] ?? 0); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
