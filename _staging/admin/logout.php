<?php
declare(strict_types=1);
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login
header('Location: /admin/login.php');
exit;
?>