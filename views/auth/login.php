<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-3">
            <div class="card-header text-center text-white" style="background: linear-gradient(65deg, #0f1521, #fe0000);">
                <h4 class="mb-0" style="color:white;">Login</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/auth/login" method="POST">
                    <div class="mb-3 position-relative">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0" 
                                id="username" 
                                name="username" 
                                placeholder="Enter your username"
                                required>
                        </div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control border-start-0" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember" class="form-label">Remember Me</label>
                        </div>
                        <a href="<?= APP_URL ?>/auth/forgot-password" class="small text-decoration-none">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn w-100"
                        style="background: linear-gradient(65deg, #0f1521, #fe0000); color:white; border:none;">
                        Login
                    </button>
                </form>

                <div class="mt-3 text-center">
                    Don't have an account? <a href="<?= APP_URL ?>/auth/register">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</div>