<?php
session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Send no-cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to index
header("Location: /sports-club/index.php");
exit();
?>
