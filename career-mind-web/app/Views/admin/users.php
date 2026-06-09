<?php
$users = $users ?? [];
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

    .admin-table th {
        font-weight: 600;
        color: #1f2a44;
    }

    .role-badge {
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .role-admin {
        background: rgba(79, 115, 255, 0.12);
        color: #3b5bdb;
    }

    .role-student {
        background: rgba(34, 197, 94, 0.12);
        color: #15803d;
    }
</style>

<div class="admin-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">User Management</h3>
            <p class="text-muted mb-0">Manage user access and role assignments.</p>
        </div>
        <a href="/admin/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table align-middle admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Update Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td>
                                <span class="role-badge <?php echo ($user['role'] ?? '') === 'admin' ? 'role-admin' : 'role-student'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($user['role'] ?? 'student')); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['created_at'] ?? ''); ?>
                            </td>
                            <td>
                                <form action="/admin/users/role" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="student" <?php echo ($user['role'] ?? '') === 'student' ? 'selected' : ''; ?>>Student</option>
                                        <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                            <td>
                                <?php if ((int)($user['id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                    <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Current user</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
