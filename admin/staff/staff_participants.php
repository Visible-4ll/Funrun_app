<?php
require_once './staff_auth.php';


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$distanceFilter = isset($_GET['distance']) ? $_GET['distance'] : '';
$paymentFilter = isset($_GET['payment']) ? $_GET['payment'] : '';

// Build query
$query = "SELECT * FROM participants";
$countQuery = "SELECT COUNT(*) FROM participants";
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($distanceFilter)) {
    $conditions[] = "distance = ?";
    $params[] = $distanceFilter;
}

if (!empty($paymentFilter)) {
    $conditions[] = "payment_method = ?";
    $params[] = $paymentFilter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
    $countQuery .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY registration_date DESC LIMIT $perPage OFFSET $offset";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT DISTINCT distance FROM participants");
    $distances = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("SELECT DISTINCT payment_method FROM participants");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$totalPages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Participants - Running Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/staff_participants.css">
    <style>

    </style>
</head>

<body>
    <?php include './staff_header.php'; ?>

    <div class="admin-container">
        <h1>Manage Participants</h1>

        <div class="filters">
            <form method="get" action="./staff_participants.php">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="form-group">
                    <select name="distance">
                        <option value="">All Distances</option>
                        <?php foreach ($distances as $distance): ?>
                            <option value="<?= htmlspecialchars($distance) ?>" <?= $distance === $distanceFilter ? 'selected' : '' ?>>
                                <?= htmlspecialchars($distance) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="payment">
                        <option value="">All Payment Methods</option>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?= htmlspecialchars($method) ?>" <?= $method === $paymentFilter ? 'selected' : '' ?>>
                                <?= htmlspecialchars($method) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="filter-btn">Apply</button>
                <a href="./staff_participants.php" class="reset-btn">Reset</a>
            </form>
        </div>

        <div class="export-actions">
            <a href="export_participants.php?format=csv&search=<?= urlencode($search) ?>&distance=<?= urlencode($distanceFilter) ?>&payment=<?= urlencode($paymentFilter) ?>" class="export-btn">Export to CSV</a>
            <a href="export_participants.php?format=excel&search=<?= urlencode($search) ?>&distance=<?= urlencode($distanceFilter) ?>&payment=<?= urlencode($paymentFilter) ?>" class="export-btn">Export to Excel</a>
        </div>

        <table class="participants-table">
        <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Distance</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Payment</th>
        <th>Date</th>
        <th>Status</th>
        <th>Transaction No.</th>
        <th>Price</th>
        <th>Received By</th> <!-- New -->
    </tr>
</thead>
<tbody>
    <?php foreach ($participants as $participant): ?>
    <tr>
        <td><?= htmlspecialchars($participant['id']) ?></td>
        <td><?= htmlspecialchars($participant['full_name']) ?></td>
        <td><?= htmlspecialchars($participant['distance']) ?></td>
        <td><?= htmlspecialchars($participant['email']) ?></td>
        <td><?= htmlspecialchars($participant['phone_number']) ?></td>
        <td><?= htmlspecialchars($participant['payment_method']) ?></td>
        <td><?= date('M j, Y', strtotime($participant['registration_date'])) ?></td>
        <td><?= htmlspecialchars($participant['payment_status']) ?></td>
        <td><?= htmlspecialchars($participant['transaction_number']) ?></td>
        <td>₱<?= htmlspecialchars($participant['price']) ?></td>
        <td><?= htmlspecialchars($participant['received_by']) ?></td> <!-- New -->
    </tr>
    <?php endforeach; ?>
</tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&distance=<?= urlencode($distanceFilter) ?>&payment=<?= urlencode($paymentFilter) ?>" class="page-link">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&distance=<?= urlencode($distanceFilter) ?>&payment=<?= urlencode($paymentFilter) ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&distance=<?= urlencode($distanceFilter) ?>&payment=<?= urlencode($paymentFilter) ?>" class="page-link">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
