<?php
if (!isset($_SESSION['registration_data'])) {
    $_SESSION['registration_data'] = [
        'distance' => '',
        'full_name' => '',
        'gender' => '',
        'home_address' => '',
        'email' => '',
        'phone_number' => '',
        'emergency_contact' => '',
        'shirt_size' => '',
        'payment_method' => '',
        'agreement1' => false,
        'agreement2' => false,
        'price' => 0.00
    ];
}

$currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$currentStep = max(1, min(5, $currentStep));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step'])) {
        $submittedStep = (int)$_POST['step'];

        switch ($submittedStep) {
            case 1:
                $distance = $_POST['distance'] ?? '';
                $_SESSION['registration_data']['distance'] = $distance;

                switch ($distance) {
                    case '3Km':
                        $_SESSION['registration_data']['price'] = 800;
                        break;
                    case '6Km':
                        $_SESSION['registration_data']['price'] = 1200;
                        break;
                    case '12Km':
                        $_SESSION['registration_data']['price'] = 2500;
                        break;
                    default:
                        $_SESSION['registration_data']['price'] = 0;
                        break;
                }
                break;

            case 2:
                $_SESSION['registration_data']['full_name'] = $_POST['full_name'] ?? '';
                $_SESSION['registration_data']['gender'] = $_POST['gender'] ?? '';
                $_SESSION['registration_data']['home_address'] = $_POST['home_address'] ?? '';
                $_SESSION['registration_data']['email'] = $_POST['email'] ?? '';
                $_SESSION['registration_data']['phone_number'] = $_POST['phone_number'] ?? '';
                $_SESSION['registration_data']['emergency_contact'] = $_POST['emergency_contact'] ?? '';
                $_SESSION['registration_data']['shirt_size'] = $_POST['shirt_size'] ?? '';
                break;

            case 3:
                if (empty($_POST['payment_method'])) {
                    die("Please select a payment method");
                }
                $_SESSION['registration_data']['payment_method'] = $_POST['payment_method'];
                break;

            case 4:
                $_SESSION['registration_data']['agreement1'] = isset($_POST['agreement1']);
                $_SESSION['registration_data']['agreement2'] = isset($_POST['agreement2']);

                try {
                    $transaction_number = 'TXN' . time() . rand(100, 999);

                    $stmt = $pdo->prepare("INSERT INTO participants (
                        distance, full_name, gender, home_address, email, 
                        phone_number, emergency_contact, shirt_size, payment_method,
                        agreement1, agreement2, payment_status, transaction_number, price
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmt->execute([
                        $_SESSION['registration_data']['distance'],
                        $_SESSION['registration_data']['full_name'],
                        $_SESSION['registration_data']['gender'],
                        $_SESSION['registration_data']['home_address'],
                        $_SESSION['registration_data']['email'],
                        $_SESSION['registration_data']['phone_number'],
                        $_SESSION['registration_data']['emergency_contact'],
                        $_SESSION['registration_data']['shirt_size'],
                        $_SESSION['registration_data']['payment_method'],
                        $_SESSION['registration_data']['agreement1'],
                        $_SESSION['registration_data']['agreement2'],
                        'Unpaid',
                        $transaction_number,
                        $_SESSION['registration_data']['price']
                    ]);

                    $registrationId = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("SELECT full_name, distance FROM participants WHERE id = ?");
                    $stmt->execute([$registrationId]);
                    $participant = $stmt->fetch();

                    $qrContent = json_encode([
                        'RUN-ID' => $registrationId,
                        'NAME' => $participant['full_name'],
                        'DISTANCE' => $participant['distance'],
                    ], JSON_UNESCAPED_UNICODE);

                    

                    require_once 'phpqrcode/qrlib.php';
                    $qrCodePath = 'assets/qrcodes/' . $registrationId . '.png';
                    QRcode::png($qrContent, $qrCodePath);

                    $stmt = $pdo->prepare("UPDATE participants SET qr_code_path = ? WHERE id = ?");
                    $stmt->execute([$qrCodePath, $registrationId]);

                    $_SESSION['registration_id'] = $registrationId;
                    $_SESSION['qr_code'] = $qrCodePath;
                    $_SESSION['participant_name'] = $participant['full_name'];
                    $_SESSION['participant_distance'] = $participant['distance'];
                    $_SESSION['transaction_number'] = $transaction_number;

                    header('Location: index.php?step=5');
                    exit;

                } catch (PDOException $e) {
                    die("Registration failed: " . $e->getMessage());
                }
        }

        if ($submittedStep < 4) {
            header('Location: index.php?step=' . ($submittedStep + 1));
            exit;
        }
    }
}

try {
    $stmt = $pdo->query("SELECT method_name FROM payment_methods WHERE is_active = TRUE");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error loading payment methods: " . $e->getMessage());
}
?>

