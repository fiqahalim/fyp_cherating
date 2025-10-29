<div class="container d-flex justify-content-center align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-3">
            <div class="card-header text-center bg-success text-white">
                <h4>Register</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/auth/register" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>
                <div class="mt-3 text-center">
                    Already have an account? <a href="<?= APP_URL ?>/auth/login">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
