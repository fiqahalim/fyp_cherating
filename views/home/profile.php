<div class="container mt-4">
    <h2 class="mb-4">My Profile</h2>

    <form action="<?= APP_URL ?>/auth/updateProfile" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                class="form-control" 
                value="<?= htmlspecialchars($data['username'] ?? '') ?>" 
                required
            >
        </div>

        <div class="form-group mt-3">
            <label for="password">New Password (leave blank to keep current)</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-control"
            >
        </div>

        <button type="submit" class="btn btn-primary mt-4">Update Profile</button>
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-secondary mt-4">Back to Dashboard</a>
    </form>
</div>
