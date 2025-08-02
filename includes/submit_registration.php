<?php
function submitRegistration($data) {
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "funrun_app";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        //unique registration ID
        $registrationId = uniqid('RUN', true);
        
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO participants (
            distance, 
            full_name, 
            gender, 
            home_address, 
            email, 
            phone_number, 
            emergency_contact, 
            shirt_size, 
            payment_method, 
            agreement1, 
            agreement2,
            qr_code_path
        ) VALUES (
            :distance, 
            :full_name, 
            :gender, 
            :home_address, 
            :email, 
            :phone_number, 
            :emergency_contact, 
            :shirt_size, 
            :payment_method, 
            :agreement1, 
            :agreement2,
            :qr_code_path
        )");
        
        
        require_once 'phpqrcode/qrlib.php';
        $qrCodePath = 'qrcodes/' . $registrationId . '.png';
        QRcode::png($registrationId, $qrCodePath);
        
        $stmt->bindParam(':distance', $data['distance']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':home_address', $data['home_address']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone_number', $data['phone_number']);
        $stmt->bindParam(':emergency_contact', $data['emergency_contact']);
        $stmt->bindParam(':shirt_size', $data['shirt_size']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':agreement1', $data['agreement1'], PDO::PARAM_BOOL);
        $stmt->bindParam(':agreement2', $data['agreement2'], PDO::PARAM_BOOL);
        $stmt->bindParam(':qr_code_path', $qrCodePath);
        
        $stmt->execute();
        
        return [
            'success' => true,
            'message' => 'Registration successful',
            'registration_id' => $registrationId,
            'qr_code' => $qrCodePath
        ];
    } catch(PDOException $e) {
        return [
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ];
    }
}
?>