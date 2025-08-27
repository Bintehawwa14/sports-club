<?php
require 'include/db_connect.php';

header('Content-Type: application/json');

if(isset($_POST['field']) && isset($_POST['value'])) {
    $field = $_POST['field'];
    $value = $_POST['value'];
    
    // Validate field to prevent SQL injection
    $allowed_fields = ['contactno', 'cnic'];
    if(!in_array($field, $allowed_fields)) {
        echo json_encode(['unique' => false]);
        exit;
    }
    
    // Check if value exists in database
    $query = "SELECT id FROM users WHERE $field = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        echo json_encode(['unique' => false]);
    } else {
        echo json_encode(['unique' => true]);
    }
    
    $stmt->close();
    $con->close();
} else {
    echo json_encode(['unique' => false]);
}
?>