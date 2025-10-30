<?php

class PasswordResetModel
{
    private $db;
    private $table = 'password_resets';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Save a password reset token
     */
    public function saveToken($user_id, $user_type, $token, $expires_at)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (user_id, user_type, token, expires_at) 
             VALUES (:user_id, :user_type, :token, :expires_at)"
        );
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_type', $user_type);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);

        return $stmt->execute();
    }

    /**
     * Get password reset record by token
     */
    public function getByToken($token)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE token = :token LIMIT 1");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update password for the correct user type
     */
    public function updatePassword($user_id, $newPassword, $user_type)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($user_type === 'admin') {
            $stmt = $this->db->prepare("UPDATE admins SET password = :password WHERE id = :user_id");
        } elseif ($user_type === 'customer') {
            $stmt = $this->db->prepare("UPDATE customers SET password = :password WHERE id = :user_id");
        } else {
            return false;
        }

        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    /**
     * Delete token after use
     */
    public function deleteToken($token)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    /**
     * Optional: Clean expired tokens
     */
    public function cleanExpiredTokens()
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE expires_at < :now");
        $stmt->bindParam(':now', $now);
        return $stmt->execute();
    }
}