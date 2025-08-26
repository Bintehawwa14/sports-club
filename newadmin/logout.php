<?php
session_start();
session_destroy();
// destroy session
$_SESSION = [];
session_destroy();

// no-cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// redirect to index
header("Location: /sports-club/index.php");
exit();

?>
<script language="javascript">
document.location="../index.php";
</script>