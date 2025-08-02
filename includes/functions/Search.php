<?php
function connect_db() {
    return new mysqli("localhost", "root", "", "funrun_app");
}

function get_participant_by_id($id) {
    $conn = connect_db();
    $stmt = $conn->prepare("SELECT * FROM participants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}