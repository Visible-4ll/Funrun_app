<?php
function getEventSetting($name, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM event_settings WHERE setting_name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}
?>
