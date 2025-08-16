    <?php
    session_start();

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Redirect if already logged in
    if (isset($_SESSION['logged_in'], $_SESSION['role']) && $_SESSION['role'] === 'staff') {
        header('Location: staff_dashboard.php');
        exit;
    }


    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Simple if-else check for testing
        if ($username === 'staff1' && $password === 'staff123') {
            // Login successful - create session
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = 'staff';
            $_SESSION['staff_username'] = 'staff';
            $_SESSION['staff_full_name'] = 'John Staff';
            
            // Redirect to dashboard
            header('Location: staff_dashboard.php');
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
    <title>Staff Login - Running Event</title>
    <link rel="stylesheet" href="./assets/css/staff_login.css" />   </head>
    <body>

    <div class="login-container">
        <img src="./img/Installersph.png" alt="Logo" class="login-logo" />
        <h1>Staff Login</h1>

        <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div>
                <input type="text" name="username" placeholder="Username" required />
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required />
            </div>
            <div>
                <button type="submit">Login</button>
            </div>
            <div>
        <a href="../admin_login.php">Login as Admin</a>
        </div>
        </form>
    </div>

    </body>
    </html>
