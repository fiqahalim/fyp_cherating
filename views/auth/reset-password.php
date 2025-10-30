<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-3">
            <div class="card-header text-center text-white" style="background: linear-gradient(45deg, #0f1521, #00c6ff);">
                <h4 class="mb-0" style="color:white;">Reset Password</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/auth/reset-password" method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?= APP_URL ?>/auth/login">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
