<?php
session_start();
include_once('../include/db_connect.php');

if (!isset($_SESSION['userid'])) {
    header('location:logout.php');
    exit;
} 
// Fetch all active sports from events table
$query = "SELECT DISTINCT sport FROM events WHERE status='active' ORDER BY sport ASC";
$result = mysqli_query($con, $query);

$sports = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $splitSports = explode(',', $row['sport']); // split by comma
        foreach ($splitSports as $sp) {
            $sports[] = trim($sp);
        }
    }
}

// Remove duplicates
$sports = array_unique($sports);?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('../images/alert.jpg');
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            padding: 80px;
        }
        h1 {
            margin-bottom: 30px;
            font-weight: 700;
            text-shadow: 2px 2px 8px #000;
            color: #fff;
        }
        .join-btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #28a745;
            color: white;
            font-weight: 700;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(40,167,69,0.6);
            transition: background-color 0.3s ease;
            margin: 10px;
        }
        .join-btn:hover {
            background-color: #1e7e34;
        }
        .message {
            font-size: 20px;
            font-weight: 600;
            padding: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #fff;
        }
    </style>
   
</head>
<body class="sb-nav-fixed">
<?php include_once('includes/navbar.php'); ?>
<div id="layoutSidenav">
<?php include_once('includes/sidebar.php'); ?>
<div id="layoutSidenav_content">
<main>
    <a href="get_event.php" class="back-btn">⬅ Back</a>
    <div class="box">
        <?php if (empty($sports)): ?>
            <div class="message">Currently no event is open for registration.</div>
        <?php else: ?>
            <h1>Registration is open for:</h1>
            <?php foreach ($sports as $sport): ?>
                <?php 
                // Convert sport name to lowercase and remove spaces for filename
                $fileName = strtolower(str_replace(' ', '', $sport));
                ?>
                <a href="../reg/<?php echo $fileName; ?>.php" class="join-btn">
                    Join <?php echo htmlspecialchars($sport); ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
         
    </div>
</main>
</div>
</div>
</body>
</html>
 
<style>
.back-btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 15px;
    background: linear-gradient(135deg, #ff416c, #ff4b2b);
    color: white;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
    transition: 0.3s ease-in-out;
}
.back-btn:hover {
    background: linear-gradient(135deg, #ff4b2b, #ff416c);
    transform: translateY(-3px);
    box-shadow: 0px 6px 12px rgba(0,0,0,0.3);
}
</style>