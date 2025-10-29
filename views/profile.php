<div class="container-fluid">
    <?php Flash::display(); ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= ($role === 'admin') ? 'Admin Profile' : 'My Profile' ?></h6>
                </div>
                <div class="card-body">
                    <form action="<?= APP_URL ?>/updateProfile" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $username ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>
                        
                        <!-- <div class="form-group">
                            <label for="profile_image">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image">
                            <input type="hidden" name="current_image" value="<?= $profile_image ?>">
                        </div> -->
                        
                        <!-- <div class="form-group">
                            <img src="<?= APP_URL . $profile_image ?>" alt="Profile Image" style="width: 150px; height: 150px;">
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>