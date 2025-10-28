<?php

class Auth
{
    public static function checkAdmin()
    {
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: /fyp_cherating/admin/login");
            exit;
        }
    }
}