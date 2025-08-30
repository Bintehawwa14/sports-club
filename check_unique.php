<?php
require 'include/db_connect.php';

header('Content-Type: application/json');

if(isset($_POST['field']) && isset($_POST['value'])) {
    $field = $_POST['field'];
    $value = $_POST['value'];
    
    // Map field names to database column names
    $column_map = [
        'contact' => 'contactno',  // Map 'contact' to 'contactno' column
        'cnic' => 'cnic'
    ];
    
    // Validate field to prevent SQL injection
    if(!isset($column_map[$field])) {
        echo json_encode(['unique' => true]); // Assume unique if field is invalid
        exit;
    }
    
    $column = $column_map[$field];
    
    // Check if value exists in database
    $query = "SELECT id FROM users WHERE $column = ?";
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
    echo json_encode(['unique' => true]); // Assume unique if parameters are missing
}
?>