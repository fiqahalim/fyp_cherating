<?php

class Flash
{
    public static function set($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function get($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // clear after showing
            return $msg;
        }
        return null;
    }

    public static function display()
    {
        $types = [
            'success'   => 'alert-success',
            'error'     => 'alert-danger',
            'warning'   => 'alert-warning',
            'info'      => 'alert-info'
        ];

        foreach ($types as $key => $class) {
            if ($msg = self::get($key)) {
                echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($msg);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
            }
        }
    }
}