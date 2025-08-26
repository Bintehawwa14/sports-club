<?php
session_start();
include_once('../include/db_connect.php');

// Security check: Only admin or superadmin should update approval
// Add your login and role verification here if you have

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = intval($_POST['userid']);
    $is_approved = intval($_POST['is_approved']);

    // Prevent approving/deleting admins this way
    $userQuery = mysqli_query($con, "SELECT role FROM users WHERE id='$userid'");
    $userRow = mysqli_fetch_assoc($userQuery);

    if ($userRow && strtolower($userRow['role']) === 'admin') {
        echo "<script>alert('Admin approval status cannot be changed here.'); window.location='manage-users.php';</script>";
        exit;
    }

    $updateQuery = "UPDATE users SET is_approved = ? WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("ii", $is_approved, $userid);
    $stmt->execute();

    header("Location: manage-users.php");
    exit;
}
?>
