
<?php
session_start();
include_once('../include/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// for deleting user and their registrations
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Pehle role check karein
    $checkRole = mysqli_query($con, "SELECT role, email FROM users WHERE id='$user_id'");
    $row = mysqli_fetch_assoc($checkRole);

    if ($row && strtolower($row['role']) === 'admin') {
        echo "<script>alert('Admin cannot be deleted.');</script>";
    } else {
        // Delete all registrations first
        $email = $row['email'];
        mysqli_query($con, "DELETE FROM tabletennis_players WHERE email='$email'");
        mysqli_query($con, "DELETE FROM badminton_players WHERE email='$email'");
        // aapke baaki registration tables bhi yahan add karein

        // Ab user delete
        $sql = "DELETE FROM users WHERE id = $user_id";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Successfully deleted!'); 
                  window.location.href='manage-users.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        }
    }
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
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg'); /* Updated path */
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
    <?php include_once('includes/navbar.php'); ?>
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
                                    $ret = mysqli_query($con, "SELECT * FROM users");
                                    while ($row = mysqli_fetch_assoc($ret)) {
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>