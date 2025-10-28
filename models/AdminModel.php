<?php

class AdminModel
{
    private $db;

    // Constructor
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get Admin Profile Data
    public function getAdminProfile($admin_id)
    {
        $stmt = $this->db->prepare("SELECT username, password FROM admins WHERE id = ?");
        $stmt->execute([$admin_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Return the admin data
    }

    // Update Admin Profile
    public function updateProfile($admin_id, $username, $password)
    {
        // Hash the password first and store it in a variable
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the query to update admin details
        $stmt = $this->db->prepare(
            "UPDATE admins SET username = :username, password = :password  WHERE id = :admin_id"
        );

        // Bind parameters to the query
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);  // passing the variable with hashed password
        // $stmt->bindParam(':profile_image', $profileImage);

        // Execute the query to update the profile
        return $stmt->execute();
    }
}