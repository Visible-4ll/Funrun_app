<?php
require_once 'admin_auth.php';

if (!isset($_GET['id'])) {
    header('Location: admin_participants.php');
    exit;
}

$id = (int)$_GET['id'];

// Get participant data
try {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
    $stmt->execute([$id]);
    $participant = $stmt->fetch();
    
    if (!$participant) {
        header('Location: admin_participants.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'home_address' => trim($_POST['home_address'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'emergency_contact' => trim($_POST['emergency_contact'] ?? ''),
        'shirt_size' => trim($_POST['shirt_size'] ?? ''),
        'payment_method' => trim($_POST['payment_method'] ?? ''),
        'distance' => trim($_POST['distance'] ?? ''),
        'agreement1' => isset($_POST['agreement1']),
        'agreement2' => isset($_POST['agreement2']),
        'id' => $id
    ];
    
    try {
        $stmt = $pdo->prepare("UPDATE participants SET 
            full_name = :full_name,
            gender = :gender,
            home_address = :home_address,
            email = :email,
            phone_number = :phone_number,
            emergency_contact = :emergency_contact,
            shirt_size = :shirt_size,
            payment_method = :payment_method,
            distance = :distance,
            agreement1 = :agreement1,
            agreement2 = :agreement2
            WHERE id = :id");
        
        $stmt->execute($updatedData);
        
        // Log the update
        logAdminActivity("Updated participant ID: $id");
        
        $_SESSION['success_message'] = 'Participant updated successfully';
        header("Location: admin_view_participant.php?id=$id");
        exit;
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get payment methods for dropdown
try {
    $stmt = $pdo->query("SELECT method_name FROM payment_methods WHERE is_active = TRUE");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant - Running Event</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Edit Participant</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($participant['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="distance">Distance</label>
                <select id="distance" name="distance" required>
                    <option value="3Km" <?= $participant['distance'] === '3Km' ? 'selected' : '' ?>>3Km</option>
                    <option value="6Km" <?= $participant['distance'] === '6Km' ? 'selected' : '' ?>>6Km</option>
                    <option value="12Km" <?= $participant['distance'] === '12Km' ? 'selected' : '' ?>>12Km</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?= $participant['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $participant['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $participant['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="home_address">Home Address</label>
                <textarea id="home_address" name="home_address" required><?= htmlspecialchars($participant['home_address']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($participant['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?= htmlspecialchars($participant['phone_number']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="emergency_contact">Emergency Contact</label>
                <input type="text" id="emergency_contact" name="emergency_contact" value="<?= htmlspecialchars($participant['emergency_contact']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="shirt_size">Shirt Size</label>
                <select id="shirt_size" name="shirt_size" required>
                    <option value="XS" <?= $participant['shirt_size'] === 'XS' ? 'selected' : '' ?>>XS</option>
                    <option value="S" <?= $participant['shirt_size'] === 'S' ? 'selected' : '' ?>>S</option>
                    <option value="M" <?= $participant['shirt_size'] === 'M' ? 'selected' : '' ?>>M</option>
                    <option value="L" <?= $participant['shirt_size'] === 'L' ? 'selected' : '' ?>>L</option>
                    <option value="XL" <?= $participant['shirt_size'] === 'XL' ? 'selected' : '' ?>>XL</option>
                    <option value="XXL" <?= $participant['shirt_size'] === 'XXL' ? 'selected' : '' ?>>XXL</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <?php foreach ($paymentMethods as $method): ?>
                        <option value="<?= htmlspecialchars($method) ?>" <?= $participant['payment_method'] === $method ? 'selected' : '' ?>>
                            <?= htmlspecialchars($method) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="agreement1" <?= $participant['agreement1'] ? 'checked' : '' ?>>
                    Terms & Conditions
                </label>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="agreement2" <?= $participant['agreement2'] ? 'checked' : '' ?>>
                    I used Think that i am not enough
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Save Changes</button>
                <a href="admin_view_participant.php?id=<?= $id ?>" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>