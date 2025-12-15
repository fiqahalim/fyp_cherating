<?php

class CustomerModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /* ----------------------------
     * BASIC GETTERS
     * ---------------------------- */
    public function getByUsername($username, $onlyVerified = false)
    {
        $sql = "SELECT * FROM customers WHERE username = ?";
        if ($onlyVerified) {
            $sql .= " AND is_verified = 1";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email, $onlyVerified = false)
    {
        $sql = "SELECT * FROM customers WHERE email = ?";
        if ($onlyVerified) {
            $sql .= " AND is_verified = 1";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByPhone($phone)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByVerificationCode($code)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE verification_code = ? 
            AND is_verified = 0 
            AND verification_expires_at > NOW()
        ");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProfile($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ----------------------------
     * CREATE + UPDATE METHODS
     * ---------------------------- */
    public function createCustomer($full_name, $email, $username, $hashedPassword, $phone, $verification_code = null)
    {
        $expires_at = $verification_code ? date('Y-m-d H:i:s', strtotime('+10 minutes')) : null;
        $is_verified = $verification_code ? 0 : 1;
        $status = $verification_code ? 'inactive' : 'active';

        $stmt = $this->db->prepare("
            INSERT INTO customers 
            (full_name, email, username, password, phone, verification_code, verification_expires_at, is_verified, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $full_name,
            $email,
            $username,
            $hashedPassword,
            $phone,
            $verification_code,
            $expires_at,
            $is_verified,
            $status
        ]);
    }

    public function verifyCustomer($code)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE verification_code = ? 
            AND is_verified = 0 
            AND verification_expires_at > NOW()
        ");
        $stmt->execute([$code]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $update = $this->db->prepare("
                UPDATE customers 
                SET is_verified = 1, status = 'active', verification_code = NULL, verification_expires_at = NULL 
                WHERE id = ?
            ");
            return $update->execute([$customer['id']]);
        }

        return false;
    }

    public function updateProfile($id, $username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            UPDATE customers 
            SET username = :username, password = :password 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    /* ----------------------------
     * OPTIONAL: Resend verification
     * ---------------------------- */
    public function resendVerification($email, $newCode)
    {
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $stmt = $this->db->prepare("
            UPDATE customers 
            SET verification_code = ?, verification_expires_at = ? 
            WHERE email = ? AND is_verified = 0
        ");
        return $stmt->execute([$newCode, $expires_at, $email]);
    }

    /* ----------------------------
     * PENDING REGISTRATION METHODS
     * ---------------------------- */
    // Get pending user by email or phone
    public function getPendingByEmailOrPhone($email, $phone)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE (email = ? OR phone = ?) 
            AND is_verified = 0
        ");
        $stmt->execute([$email, $phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check pending username
    public function getPendingByUsername($username)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE username = ? AND is_verified = 0
        ");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check pending email
    public function getPendingByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE email = ? AND is_verified = 0
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update OTP & optional user info for pending registration
    public function updateVerificationCode($id, $code, $hashedPassword, $full_name, $username, $phone)
    {
        $stmt = $this->db->prepare("
            UPDATE customers 
            SET verification_code = ?, verification_expires_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE),
                password = ?, full_name = ?, username = ?, phone = ?
            WHERE id = ?
        ");
        return $stmt->execute([$code, $hashedPassword, $full_name, $username, $phone, $id]);
    }

    /* ----------------------------
     * BOOKING GUEST METHODS
     * ---------------------------- */
    /**
     * Finds an existing customer by email or creates a new one 
     * for a non-logged-in guest booking.
     * @param string $name
     * @param string $phone
     * @param string $email
     * @return int|bool Customer ID on success, false on failure.
     */
    public function findOrCreateGuest($name, $phone, $email, $username = null, $password = null)
    {
        // 1. Check if a customer already exists with this email
        $customer = $this->getByEmail($email);

        if ($customer) {
            // Customer found, return existing ID
            return $customer['id'];
        }

        // 2. Customer not found, must create a new record.
        try {
            // A. Ensure required NOT NULL fields are present
            if (empty($username)) {
                $baseUsername = 'guest_' . strtolower(substr(str_replace(['@', '.', '-'], '', $email), 0, 10));
                $counter = 0;
                $tempUsername = $baseUsername;
                
                // Ensure generated username is unique (max 10 tries)
                while ($this->getByUsername($tempUsername)) {
                    $counter++;
                    $tempUsername = $baseUsername . $counter;
                    if ($counter > 10) { 
                         // Fallback to a truly unique ID if simple counter fails
                        $tempUsername = 'guest_' . uniqid();
                        break; 
                    }
                }
                $username = $tempUsername;
            }

            // Generate a cryptographically secure random password if missing
            if (empty($password)) {
                // This password is only for satisfying the DB constraint; the guest won't use it.
                $password = bin2hex(random_bytes(16)); 
            }

            // B. Check if the provided username is already taken (to prevent UNIQUE constraint error)
            if ($this->getByUsername($username)) {
                file_put_contents('debug.txt', "Customer creation error: Username already taken: " . $username . "\n", FILE_APPEND);
                return false; 
            }

            // C. Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // --- Insert New Guest/Customer ---
            $stmt = $this->db->prepare("
                INSERT INTO customers 
                (full_name, email, username, password, phone, is_verified, status)
                VALUES (?, ?, ?, ?, ?, 1, 'active')
            ");
            
            $stmt->execute([
                $name,
                $email,
                $username,
                $hashedPassword,
                $phone
            ]);
            
            return $this->db->lastInsertId();

        } catch (Exception $e) {
            file_put_contents('debug.txt', "Customer creation error (findOrCreateGuest): " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    /**
     * Checks if a user already exists with the given email or username.
     * @param string $email
     * @param string $username (optional)
     * @return bool True if a customer exists with either, false otherwise.
     */
    public function checkExistence($email, $username = null)
    {
        $params = [$email];
        $sql = "SELECT COUNT(*) FROM customers WHERE email = ?";

        if ($username !== null && $username !== '') {
            $sql .= " OR username = ?";
            $params[] = $username;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
}