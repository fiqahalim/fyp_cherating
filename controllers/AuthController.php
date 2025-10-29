<?php

class AuthController extends Controller
{
    public function login()
    {
        $this->view('auth/login');
    }

    public function authenticate()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Load models
        $adminModel = $this->model('AdminModel');
        $customerModel = $this->model('CustomerModel');

        // Check admin first
        $admin = $adminModel->getByUsername($username);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = $admin['id']; //unified key
            $_SESSION['username'] = $username;

            Flash::set('success', "Welcome back, Admin $username!");
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        // Try customer next
        $customer = $customerModel->getByUsername($username);

        if ($customer && password_verify($password, $customer['password'])) {
            $_SESSION['role'] = 'customer';
            $_SESSION['user_id'] = $customer['id']; // unified key
            $_SESSION['username'] = $username;

            Flash::set('success', "Welcome back, $username!");
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        // if both fail
        Flash::set('error', "Invalid username or password.");
        header("Location: " . APP_URL . "/auth/login");
        exit;
    }

    public function register()
    {
        $this->view('auth/register');
    }

    public function registerProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_URL . "/auth/register");
            exit;
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($full_name) || empty($email) || empty($username) || empty($password) || empty($phone)) {
            Flash::set('error', "All fields are required.");
            header("Location: " . APP_URL . "/auth/register");
            exit;
        }

        $customerModel = $this->model('CustomerModel');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists (verified + pending)
        $existingUsername = $customerModel->getByUsername($username, true);
        $pendingUsername = $customerModel->getPendingByUsername($username);

        if ($existingUsername || $pendingUsername) {
            Flash::set('error', "Username already taken.");
            header("Location: " . APP_URL . "/auth/register");
            exit;
        }

        $existingEmail = $customerModel->getByEmail($email, true);
        $pendingEmail = $customerModel->getPendingByEmail($email);

        if ($existingEmail || $pendingEmail) {
            Flash::set('error', "Email already registered.");
            header("Location: " . APP_URL . "/auth/register");
            exit;
        }

        // Check if a pending user exists by email or phone
        $pendingCustomer = $customerModel->getPendingByEmailOrPhone($email, $phone);

        if ($pendingCustomer) {
            // Previous OTP still valid?
            $now = new DateTime();
            $expires_at = new DateTime($pendingCustomer['verification_expires_at'] ?? 'now');

            if ($expires_at > $now && !empty($pendingCustomer['verification_code'])) {
                // Reuse previous OTP
                $verification_code = $pendingCustomer['verification_code'];
            } else {
                // Generate new OTP and update pending record
                $verification_code = rand(100000, 999999);
                $customerModel->updateVerificationCode(
                    $pendingCustomer['id'],
                    $verification_code,
                    $hashedPassword,
                    $full_name,
                    $username,
                    $phone
                );
            }
        } else {
            // No pending user, create new one
            $verification_code = rand(100000, 999999);
            $customerModel->createCustomer($full_name, $email, $username, $hashedPassword, $phone, $verification_code);
        }

        // Send OTP via WhatsApp
        $apiKey = "9874625"; // Replace with your actual key
        $message = urlencode(
            "🌴 Cherating Guest House 🌴\n\n" .
            "Hello $full_name,\n" .
            "Use the code below to verify your account:\n\n" .
            "🔹 Code: $verification_code\n" .
            "🔹 Username: $username\n" .
            "🔹 Email: $email\n\n" .
            "Valid 10 mins. Thank you! 💚"
        );
        $url = "https://api.callmebot.com/whatsapp.php?phone=" . urlencode($phone) . "&text=$message&apikey=$apiKey";
        @file_get_contents($url);

        Flash::set('success', "A verification code has been sent to your WhatsApp. Please enter it below to verify.");
        header("Location: " . APP_URL . "/auth/verify");
        exit;
    }

    public function verify()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['verification_code'] ?? '');

            if (empty($code)) {
                Flash::set('error', "Please enter your verification code.");
                header("Location: " . APP_URL . "/auth/verify");
                exit;
            }

            $customerModel = $this->model('CustomerModel');

            // Check and mark verified
            if ($customerModel->verifyCustomer($code)) {
                Flash::set('success', "Verification successful! You can now log in.");
                header("Location: " . APP_URL . "/auth/login");
                exit;
            } else {
                Flash::set('error', "Invalid or expired verification code. Please try again.");
                header("Location: " . APP_URL . "/auth/verify");
                exit;
            }
        }

        // Show verification form
        $this->view('auth/verify');
    }

    public function dashboard()
    {
        // Ensure session is active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check login
        if (empty($_SESSION['role'])) {
            header("Location: " . APP_URL . "/auth/login");
            exit;
        }

        $role = $_SESSION['role'];
        $data = ['role' => $role];

        // Handle Admin Dashboard
        if ($role === 'admin') {
            $this->view('admin/dashboard', $data);
            return;
        }

        // Handle Customer Dashboard
        if ($role === 'customer') {
            // Handle possible session ID variations
            $customerId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

            if (!$customerId) {
                // If no ID is found, force logout or redirect
                session_destroy();
                header("Location: " . APP_URL . "/auth/login");
                exit;
            }

            // Load booking data
            $bookingModel = new BookingModel();

            // Optional: add a safeguard in case the method doesn’t exist
            if (method_exists($bookingModel, 'getBookingsByCustomer')) {
                $bookings = $bookingModel->getBookingsByCustomer($customerId);
            } else {
                $bookings = [];
            }

            $data['bookings'] = $bookings;

            $this->view('home/dashboard', $data);
            return;
        }

        // If user role is unknown
        echo "Access denied.";
    }

    public function profile()
    {
        if (!isset($_SESSION['role'])) {
            header("Location: " . APP_URL . "/auth/login");
            exit;
        }

        $role = $_SESSION['role'];
        $data = [];

        if ($role === 'admin') {
            $adminModel = $this->model('AdminModel');
            $data = $adminModel->getAdminProfile($_SESSION['user_id']);
        } elseif ($role === 'customer') {
            $customerModel = $this->model('CustomerModel');
            $data = $customerModel->getProfile($_SESSION['user_id']);
        } else {
            echo "Access denied.";
            exit;
        }

        $data['role'] = $role;
        $this->view('profile', $data);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['role'])) {
            header("Location: " . APP_URL . "/auth/login");
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $role = $_SESSION['role'];

        if ($role === 'admin') {
            $adminModel = $this->model('AdminModel');
            $adminModel->updateProfile($_SESSION['admin_id'], $username, $password);
        } else {
            $customerModel = $this->model('CustomerModel');
            $customerModel->updateProfile($_SESSION['customer_id'], $username, $password);
        }

        Flash::set('success', "Profile updated successfully!");
        header("Location: " . APP_URL . "/profile");
        exit;
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
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session variables
        $_SESSION = [];

        // Destroy the session cookie if it exists
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        // Flash message and redirect
        Flash::set('success', "Logged out successfully.");
        header("Location: " . APP_URL . "/auth/login");
        exit;
    }
}