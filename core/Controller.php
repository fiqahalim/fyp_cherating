<?php

class Controller {
    // Load model
    public function model($model) {
        require_once __DIR__ . "/../models/" . $model . ".php";
        return new $model();
    }

    // Load view
    public function view($view, $data = []) {
        //  Check if the user is an admin
        $isAdmin = isset($_SESSION['admin_id']); // check if admin logged in
        $data['isAdmin'] = $isAdmin; // Add this data to pass to the view

        extract($data);
        // Include footer based on admin status
        if (!$isAdmin) {
            require_once __DIR__ . "/../views/layout/header.php";
            require_once __DIR__ . "/../views/" . $view . ".php";
            require_once __DIR__ . "/../views/layout/footer.php";
        } else {
            require_once __DIR__ . "/../views/admin/layouts/admin_header.php";
            require_once __DIR__ . "/../views/" . $view . ".php";
            require_once __DIR__ . "/../views/admin/layouts/admin_footer.php";
        }
    }
}