    <?php
    session_start();

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Redirect if already logged in
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header('Location: admin_dashboard.php');
        exit;
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Simple if-else check for testing
        if ($username === 'admin' && $password === 'admin123') {
            // Login successful - create session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = 'admin';
            $_SESSION['admin_full_name'] = 'System Administrator';
            
            // Redirect to dashboard
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login - Running Event</title>
    <link rel="stylesheet" href="assets/css/admin_login.css" />
    
    </head>
    <body>

    <div class="login-container">
        <!-- Replace 'logo.png' with your actual logo path -->
        <img src="assets/img/Installersph.png" alt="Logo" class="login-logo" />
        <h1>Admin</h1>

        <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
        <div>
            <input type="text" id="username" name="username" placeholder="Username" required />
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="Password" required/>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
        </form>
    </div>

    </body>
    </html>
