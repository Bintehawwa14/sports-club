<?php
session_start();
include_once('../include/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="User Profile View Page" />
    <meta name="author" content="" />
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/alert.jpg');
            background-size: cover;
            background-position: top center;
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
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .edit-link {
            background-color: #2563eb;
            color: #ffffff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .edit-link:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .edit-link:active {
            transform: translateY(0);
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
            width: 35%;
        }

        td {
            color: #374151;
            background-color: #ffffff;
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
            .profile-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .profile-header h1 {
                font-size: 20px;
            }

            .edit-link {
                padding: 7px 12px;
                font-size: 12px;
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
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .edit-link {
                width: 100%;
                text-align: center;
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
                    <?php 
                    $userid = $_SESSION['user']['id'];
                    $query = mysqli_query($con, "SELECT * FROM users WHERE id='$userid'");
                    if (!$query) {
                        die("Query failed: " . mysqli_error($con));
                    }
                    if ($result = mysqli_fetch_array($query)) {
                    ?>
                    <div class="profile-container">
                        <div class="profile-header">
                            <h1><?php echo htmlspecialchars($result['fname']); ?>'s Profile</h1>
                            <a class="edit-link" href="edit-profile.php">Edit Profile</a>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <th>First Name</th>
                                    <td><?php echo htmlspecialchars($result['fname']); ?></td>
                                </tr>
                                <tr>
                                    <th>Last Name</th>
                                    <td><?php echo htmlspecialchars($result['lname']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($result['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact No.</th>
                                    <td><?php echo htmlspecialchars($result['contactno']); ?></td>
                                </tr>
                                <tr>
                                    <th>Reg. Date</th>
                                    <td><?php echo htmlspecialchars($result['posting_date']); ?></td>
                                </tr>
                                <tr>
                                    <th>CNIC</th>
                                    <td><?php echo htmlspecialchars($result['cnic']); ?></td>
                                </tr>
                                <tr>
                                    <th>Club/College</th>
                                    <td><?php echo htmlspecialchars($result['club_college']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                    } else {
                        echo "<div class='alert'>User not found!</div>";
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>