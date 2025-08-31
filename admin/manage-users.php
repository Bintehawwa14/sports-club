
<?php
session_start();
require_once '../include/db_connect.php';

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user'])) {
    error_log("Session user not set. Redirecting to index.php");
    header("Location: index.php");
    exit();
}

// Handle user deletion with prepared statements
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Check user role
    $checkStmt = mysqli_prepare($con, "SELECT role, email FROM users WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $user_id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row && strtolower($row['role']) === 'admin') {
        echo "<script>alert('Admin cannot be deleted.'); window.location.href='manage-users.php';</script>";
    } else {
        // Delete registrations
        $email = $row['email'];
        $deleteStmt1 = mysqli_prepare($con, "DELETE FROM tabletennis_players WHERE email = ?");
        mysqli_stmt_bind_param($deleteStmt1, "s", $email);
        mysqli_stmt_execute($deleteStmt1);

        $deleteStmt2 = mysqli_prepare($con, "DELETE FROM badminton_players WHERE email = ?");
        mysqli_stmt_bind_param($deleteStmt2, "s", $email);
        mysqli_stmt_execute($deleteStmt2);

        // Delete user
        $deleteStmt3 = mysqli_prepare($con, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($deleteStmt3, "i", $user_id);
        if (mysqli_stmt_execute($deleteStmt3)) {
            echo "<script>alert('Successfully deleted!'); window.location.href='manage-users.php';</script>";
        } else {
            error_log("Error deleting user: " . mysqli_error($con));
            echo "<script>alert('Error: " . mysqli_error($con) . "'); window.location.href='manage-users.php';</script>";
        }
        mysqli_stmt_close($deleteStmt1);
        mysqli_stmt_close($deleteStmt2);
        mysqli_stmt_close($deleteStmt3);
    }
    mysqli_stmt_close($checkStmt);
}

// Fetch users
$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($con, $userQuery);
if (!$userResult) {
    error_log("Error fetching users: " . mysqli_error($con));
    die("Error fetching users. Please check logs.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Manage Users Page" />
    <meta name="author" content="" />
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        #layoutSidenav {
            display: flex;
            flex: 1;
        }

        #layoutSidenav_content {
            flex: 1;
            padding: 15px;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
        }

        .users-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 95%;
            max-width: 1200px;
        }

        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .users-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .table-responsive {
            margin-top: 15px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        th {
            background-color: #f0f4f8;
            color: #1a3c6d;
            font-weight: 600;
        }

        td {
            color: #374151;
            background-color: #ffffff;
        }

        .action-link {
            color: #b91c1c;
            font-size: 16px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .action-link:hover {
            color: #7f1d1d;
        }

        .action-disabled {
            color: #6b7280;
            font-size: 13px;
        }

        .alert {
            padding: 10px;
            background-color: #fef2f2;
            color: #b91c1c;
            margin: 15px auto;
            border-radius: 5px;
            text-align: center;
            font-size: 13px;
            max-width: 800px;
        }

        @media (max-width: 768px) {
            .users-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .users-header h1 {
                font-size: 20px;
            }

            th, td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 8px;
            }

            th {
                background-color: #e6eef8;
                border-bottom: none;
            }

            td {
                border-bottom: 1px solid #e0e0e0;
            }

            .container-fluid {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .users-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php
    if (!file_exists('includes/navbar.php') || !file_exists('includes/sidebar.php')) {
        error_log("Navbar or sidebar file missing");
        echo "<div class='alert alert-danger text-center'>Error: Navigation files missing. Please check includes directory.</div>";
    } else {
        include_once('includes/navbar.php');
    ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="users-container">
                        <div class="users-header">
                            <h1>Manage Users</h1>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sno.</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email Id</th>
                                        <th>Contact No.</th>
                                        <th>CNIC</th>
                                        <th>Club/College</th>
                                        <th>Reg. Date</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($userResult)) {
                                    ?>
                                        <tr>
                                            <td><?= $cnt ?></td>
                                            <td><?= htmlspecialchars($row['fname']) ?></td>
                                            <td><?= htmlspecialchars($row['lname']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['contactno']) ?></td>
                                            <td><?= htmlspecialchars($row['cnic']) ?></td>
                                            <td><?= htmlspecialchars($row['club_college']) ?></td>
                                            <td><?= htmlspecialchars($row['posting_date']) ?></td>
                                            <td><?= htmlspecialchars($row['role']) ?></td>
                                            <td>
                                                <?php if (strtolower($row['role']) !== 'admin') { ?>
                                                    <a href="manage-users.php?id=<?= $row['id'] ?>" onclick="return confirm('Do you want to delete?')" class="action-link">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="action-disabled">Delete not allowed</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>
