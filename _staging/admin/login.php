<?php
declare(strict_types=1);
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if ($password) {
        // Load auth config from outside web root
        $authFile = __DIR__ . '/../../secrets/admin_auth.php';
        
        if (file_exists($authFile)) {
            $authConfig = require $authFile;
            
            if (isset($authConfig['password_hash']) && password_verify($password, $authConfig['password_hash'])) {
                $_SESSION['admin'] = true;
                $_SESSION['last_activity'] = time();
                header('Location: /admin/');
                exit;
            }
        }
    }
    
    $error = 'Invalid password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Cobra Scholarship</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Login</h1>
            <p>Enter your administrator password to access the dashboard.</p>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>