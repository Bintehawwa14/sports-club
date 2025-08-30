<?php
include 'include/db_connect.php';

if (isset($_POST['id'], $_POST['column'], $_POST['status'], $_POST['table'])) {
    $id = intval($_POST['id']);
    $column = $_POST['column'];
    $status = $_POST['status'];
    $table = $_POST['table'];

    // Sirf badminton & tabletennis tables allowed
    $allowedTables = ['badminton_players','tabletennis_players'];
    $allowedCols = ['is_approved1','is_approved2'];

    if (in_array($table, $allowedTables) && in_array($column, $allowedCols)) {
        $stmt = $con->prepare("UPDATE $table SET $column=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        echo "success";
    } else {
        echo "invalid request";
    }
}
?>
