<?php
require_once 'admin_auth.php';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All password fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($currentPassword, $admin['password_hash'])) {
                // Update password
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$newHash, $_SESSION['admin_id']]);
                
                // Log the password change
                logAdminActivity("Changed password");
                
                $success = 'Password changed successfully';
            } else {
                $error = 'Current password is incorrect';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle payment methods update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_methods'])) {
    $methods = $_POST['payment_methods'] ?? [];
    $newMethod = trim($_POST['new_method'] ?? '');
    
    try {
        $pdo->beginTransaction();
        
        // Update existing methods
        $stmt = $pdo->prepare("UPDATE payment_methods SET is_active = FALSE");
        $stmt->execute();
        
        if (!empty($methods)) {
            $placeholders = implode(',', array_fill(0, count($methods), '?'));
            $stmt = $pdo->prepare("UPDATE payment_methods SET is_active = TRUE WHERE method_name IN ($placeholders)");
            $stmt->execute($methods);
        }
        
        // Add new method if provided
        if (!empty($newMethod)) {
            $stmt = $pdo->prepare("INSERT INTO payment_methods (method_name) VALUES (?)");
            $stmt->execute([$newMethod]);
        }
        
        $pdo->commit();
        
        // Log the update
        logAdminActivity("Updated payment methods");
        
        $success = 'Payment methods updated successfully';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get current payment methods
try {
    $stmt = $pdo->query("SELECT method_name, is_active FROM payment_methods");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Running Event</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Admin Settings</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="settings-section">
            <h2>Change Password</h2>
            <form method="post">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="save-btn">Change Password</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h2>Payment Methods</h2>
            <form method="post">
                <div class="payment-methods-list">
                    <?php foreach ($paymentMethods as $method): ?>
                        <div class="payment-method-item">
                            <input type="checkbox" id="method_<?= htmlspecialchars($method['method_name']) ?>" 
                                   name="payment_methods[]" value="<?= htmlspecialchars($method['method_name']) ?>" 
                                   <?= $method['is_active'] ? 'checked' : '' ?>>
                            <label for="method_<?= htmlspecialchars($method['method_name']) ?>">
                                <?= htmlspecialchars($method['method_name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-group">
                    <label for="new_method">Add New Payment Method</label>
                    <input type="text" id="new_method" name="new_method" placeholder="Enter new payment method">
                </div>
                
                <button type="submit" name="update_payment_methods" class="save-btn">Update Payment Methods</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h2>System Information</h2>
            <div class="system-info">
                <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
                <p><strong>Database:</strong> MySQL</p>
                <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
                <p><strong>Last Login:</strong> 
                    <?php 
                    if (!empty($_SESSION['admin_last_login'])) {
                        echo date('Y-m-d H:i:s', $_SESSION['admin_last_login']);
                    } else {
                        echo 'Never';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>