<?php 
    if ($role === 'admin') {
        include_once __DIR__ . '/admin/layouts/admin_header.php'; 
    }
?>

<div class="container-fluid mt-4">
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <div class="row">
            <div class="col-12">
                <?php Flash::display(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img 
                        src="https://bootdey.com/img/Content/avatar/avatar7.png"
                        class="rounded-circle mb-3"
                        width="120"
                        alt="Profile Avatar"
                    >

                    <h5 class="mb-1">
                        <?= ($role === 'customer') ? htmlspecialchars($full_name) : htmlspecialchars($username) ?>
                    </h5>

                    <p class="text-muted small">
                        <?= ucfirst($role) ?>
                    </p>

                    <?php if ($role === 'customer'): ?>
                        <span class="badge <?= $is_verified ? 'badge-success' : 'badge-warning' ?>">
                            <?= $is_verified ? 'Verified' : 'Not Verified' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark">
                    <h5 class="mb-0 text-white">
                        <?= ($role === 'admin') ? 'Admin Profile Settings' : 'Customer Profile Settings' ?>
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= APP_URL ?>/updateProfile" method="POST">

                        <div class="row">
                            <?php if ($role === 'customer'): ?>
                                <!-- Full Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Full Name</label>
                                    <input type="text" class="form-control" name="full_name"
                                           value="<?= htmlspecialchars($full_name) ?>" required>
                                </div>
                            <?php endif; ?>

                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Username</label>
                                <input type="text" class="form-control" name="username"
                                       value="<?= htmlspecialchars($username) ?>" required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Email</label>
                                <input type="email" class="form-control" name="email"
                                       value="<?= htmlspecialchars($email) ?>" required>
                            </div>

                            <?php if ($role === 'customer'): ?>
                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                           value="<?= htmlspecialchars($phone) ?>" required>
                                </div>

                                <!-- Status (readonly display) -->
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Account Status</label>
                                    <input type="text" class="form-control"
                                           value="<?= ucfirst($status) ?>" readonly>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Password -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">New Password</label>
                                <input type="password" class="form-control"
                                       name="password"
                                       placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                Update Profile
                            </button>
                        </div>

                        <input type="hidden" name="role" value="<?= $role ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    if ($role === 'admin') {
        include_once __DIR__ . '/admin/layouts/admin_footer.php'; 
    }
?>