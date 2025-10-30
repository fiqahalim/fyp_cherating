<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-3">
            <div class="card-header text-center text-white" style="background: linear-gradient(45deg, #0f1521, #00c6ff);">
                <h4 class="mb-0" style="color:white;">Forgot Password</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/auth/forgot-password" method="POST">
                    <div class="mb-3 position-relative">
                        <label for="email" class="form-label">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0" 
                                id="email" 
                                name="email" 
                                placeholder="Enter your username or email"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>

                <div class="mt-3 text-center">
                    <a href="<?= APP_URL ?>/auth/login">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>