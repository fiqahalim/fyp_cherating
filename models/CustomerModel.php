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
}