<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-lg rounded-3">
            <div class="card-header text-center text-white" style="background: linear-gradient(65deg, #0f1521, #fe0000);">
                <h4 class="mb-0" style="color:white;">Register</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/auth/register" method="POST">
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="John Smith" required>
                        </div>
                    </div>

                    <!-- Email & Phone in one row -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="johnsmith@example.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="+60123456789">
                            </div>
                        </div>
                    </div>

                    <!-- Username & Password in one row -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="johnsmith" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="**********" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 mt-3" style="background: linear-gradient(65deg, #0f1521, #fe0000); color:white; border:none;">Register</button>
                </form>
                <div class="mt-3 text-center">
                    Already have an account? <a href="<?= APP_URL ?>/auth/login">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>