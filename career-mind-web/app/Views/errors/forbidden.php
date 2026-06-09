<style>
    .error-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
        text-align: center;
    }

    .error-icon {
        font-size: 3rem;
        color: #e11d48;
    }
</style>

<div class="container py-5">
    <div class="error-card">
        <div class="error-icon mb-3">
            <i class="fas fa-ban"></i>
        </div>
        <h2 class="mb-2">Access Denied</h2>
        <p class="text-muted mb-4">
            <?php echo htmlspecialchars($message ?? 'You do not have permission to view this page.'); ?>
        </p>
        <a class="btn btn-outline-primary" href="/dashboard">Return to Dashboard</a>
    </div>
</div>
