```php
<?php
session_start();
require_once '../../include/db_connect.php';

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['teamName'])) {
    error_log("Team not selected in URL");
    die("Team not selected.");
}

$teamName = $_GET['teamName'];

// Fetch team details using prepared statement
$sql = "SELECT * FROM badminton_players WHERE teamName = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $teamName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$team = mysqli_fetch_assoc($result);

if (!$team) {
    error_log("No team found for teamName: $teamName");
    die("No team found.");
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Badminton Team Details Page" />
    <meta name="author" content="" />
    <title>Badminton Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../../images/volleyballform.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            min-height: 100vh;
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

        .back-button {
            color: #1a3c6d;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #b91c1c;
        }

        .form-select {
            font-size: 14px;
            padding: 6px;
            border-radius: 4px;
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

            .form-select {
                width: 100%;
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .users-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .back-button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-4">
        <!-- Back Button -->
        <a href="all_bteams.php" class="back-button mb-4 d-inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Teams
        </a>

        <!-- Header -->
        <div class="users-container">
            <div class="users-header">
                <h1>Badminton Team: <?php echo htmlspecialchars($teamName); ?></h1>
            </div>

            <!-- Team Details Table -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Email</th>
                            <th>Date of Birth</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Chronic Illness</th>
                            <th>Allergies</th>
                            <th>Medications</th>
                            <th>Surgeries</th>
                            <th>Previous Injuries</th>
                            <th>Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Player 1 -->
                        <tr>
                            <td><?php echo htmlspecialchars($team['player1']); ?></td>
                            <td><?php echo htmlspecialchars($team['email']); ?></td>
                            <td><?php echo htmlspecialchars($team['dob1']); ?></td>
                            <td><?php echo htmlspecialchars($team['height1']); ?></td>
                            <td><?php echo htmlspecialchars($team['weight1']); ?></td>
                            <td><?php echo htmlspecialchars($team['chronic_illness1']); ?></td>
                            <td><?php echo htmlspecialchars($team['allergies1']); ?></td>
                            <td><?php echo htmlspecialchars($team['medications1']); ?></td>
                            <td><?php echo htmlspecialchars($team['surgeries1']); ?></td>
                            <td><?php echo htmlspecialchars($team['previous_injuries1']); ?></td>
                            <td>
                                <form method="post" action="update_statusb.php">
                                    <input type="hidden" name="player_name" value="<?php echo htmlspecialchars($team['player1']); ?>">
                                    <input type="hidden" name="team_name" value="<?php echo htmlspecialchars($teamName); ?>">
                                    <input type="hidden" name="game" value="badminton">
                                    <input type="hidden" name="redirect_back" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <select name="is_approved" class="form-select" onchange="this.form.submit()">
                                        <option value="pending" <?php if ($team['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="approved" <?php if ($team['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <!-- Player 2 -->
                        <tr>
                            <td><?php echo htmlspecialchars($team['player2']); ?></td>
                            <td><?php echo htmlspecialchars($team['email']); ?></td>
                            <td><?php echo htmlspecialchars($team['dob2']); ?></td>
                            <td><?php echo htmlspecialchars($team['height2']); ?></td>
                            <td><?php echo htmlspecialchars($team['weight2']); ?></td>
                            <td><?php echo htmlspecialchars($team['chronic_illness2']); ?></td>
                            <td><?php echo htmlspecialchars($team['allergies2']); ?></td>
                            <td><?php echo htmlspecialchars($team['medications2']); ?></td>
                            <td><?php echo htmlspecialchars($team['surgeries2']); ?></td>
                            <td><?php echo htmlspecialchars($team['previous_injuries2']); ?></td>
                            <td>
                                <form method="post" action="update_statusb.php">
                                    <input type="hidden" name="player_name" value="<?php echo htmlspecialchars($team['player2']); ?>">
                                    <input type="hidden" name="team_name" value="<?php echo htmlspecialchars($teamName); ?>">
                                    <input type="hidden" name="game" value="badminton">
                                    <input type="hidden" name="redirect_back" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <select name="is_approved" class="form-select" onchange="this.form.submit()">
                                        <option value="pending" <?php if ($team['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="approved" <?php if ($team['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```