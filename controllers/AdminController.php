<?php

class AdminController extends Controller
{
    public function login()
    {
        $this->view('admin/login');
    }

    public function authenticate()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                // Success → redirect
                $_SESSION['admin_id'] = $admin['id'];
                Flash::set('success', "Welcome back, $username!");
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            } else {
                Flash::set('error', "Password Incorrect for user: $username");
            }
        } else {
            Flash::set('error', "User not found: $username");
        }

        // Reload login view with error
        header("Location: " .APP_URL . "/admin/login"); // rediret back
        exit;
    }

    public function dashboard()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: " . APP_URL . "/admin/login");
            exit;
        }
        $this->view('admin/dashboard');
    }

    public function profile()
    {
        $adminModel = $this->model('AdminModel');
        $adminData = $adminModel->getAdminProfile($_SESSION['admin_id']);  // Fetch the profile from DB
        $this->view('admin/profile', $adminData);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            // $profileImage = $_FILES['profile_image'];

            // Validate data, etc.
            $adminModel = $this->model('AdminModel');
            
            // Check if a new image is uploaded
            // if ($profileImage['error'] === 0) {
            //     $imagePath = $this->uploadImage($profileImage);  // Handle image upload
            // } else {
            //     $imagePath = $_POST['current_image'];  // Keep the current image if not updated
            // }

            // Update admin data
            $updateSuccess = $adminModel->updateProfile($_SESSION['admin_id'], $username, $password);

            // Check if the update was successful
            if ($updateSuccess) {
                Flash::set('success', "Profile updated successfully!");
            } else {
                Flash::set('error', "Uh-oh! Failed to updated profile!");
            }

            // Redirect back to the profile page
            header('Location: ' . APP_URL . '/admin/profile');
            exit;
        }
    }

    private function uploadImage($file)
    {
        $targetDir = __DIR__ . '/../public/uploads/';
        $targetFile = $targetDir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $targetFile);
        
        return '/uploads/' . basename($file['name']);
    }

    public function logout()
    {
        session_destroy();
        Flash::set('success', "Logged out successfully.");
        header("Location: " . APP_URL . "/admin/login");
        exit;
    }
}
