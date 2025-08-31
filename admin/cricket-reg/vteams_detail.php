```php
<?php
session_start();
require_once '../../include/db_connect.php';

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['team_name'])) {
    error_log("Team name not provided in URL");
    die("Team not selected.");
}

$teamName = $_GET['team_name'];

// Fetch players using prepared statement
$stmt = $con->prepare("SELECT player_name, age, position, height, handedness, weight, standing_reach, 
                       block_jump, approach_jump, chronic_illness, allergies, medications, surgeries, 
                       previous_injuries, is_approved 
                       FROM volleyball_players 
                       WHERE team_name = ?");
mysqli_stmt_bind_param($stmt, "s", $teamName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    error_log("Error fetching players: " . mysqli_error($con));
    die("Error fetching players. Please check logs.");
}

// Check team approval status
$stmt_check = $con->prepare("SELECT COUNT(*) as not_approved_count 
                             FROM volleyball_players 
                             WHERE team_name = ? AND is_approved != 'approved'");
mysqli_stmt_bind_param($stmt_check, "s", $teamName);
mysqli_stmt_execute($stmt_check);
$checkResult = mysqli_stmt_get_result($stmt_check);
$checkRow = $checkResult->fetch_assoc();
$teamStatus = ($checkRow['not_approved_count'] == 0) ? 'approved' : 'pending';

mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_check);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Volleyball Team Details Page" />
    <meta name="author" content="" />
    <title>Volleyball Team Details - <?php echo htmlspecialchars($teamName); ?></title>
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

        .users-header .badge {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 12px;
        }

        .badge-approved {
            background-color: #28a745;
            color: #fff;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #333;
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
            width: auto;
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

            .users-header .badge {
                font-size: 12px;
                padding: 4px 8px;
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
        <a href="all_teams.php" class="back-button mb-4 d-inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Teams
        </a>

        <!-- Header -->
        <div class="users-container">
            <div class="users-header">
                <h1>
                    Volleyball Team: <?php echo htmlspecialchars($teamName); ?>
                    <span id="team-status" class="badge <?php echo ($teamStatus == 'approved') ? 'badge-approved' : 'badge-pending'; ?>">
                        <?php echo strtoupper($teamStatus); ?>
                    </span>
                </h1>
            </div>

            <!-- Team Details Table -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Age</th>
                            <th>Position</th>
                            <th>Height</th>
                            <th>Handedness</th>
                            <th>Weight</th>
                            <th>Standing Reach</th>
                            <th>Block Jump</th>
                            <th>Approach Jump</th>
                            <th>Chronic Illness</th>
                            <th>Allergies</th>
                            <th>Medications</th>
                            <th>Surgeries</th>
                            <th>Previous Injuries</th>
                            <th>Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) { 
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['player_name'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['age'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['position'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['height'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['handedness'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['weight'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['standing_reach'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['block_jump'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['approach_jump'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['chronic_illness'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['allergies'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['medications'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['surgeries'] ?: 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($row['previous_injuries'] ?: 'None'); ?></td>
                                    <td>
                                        <form method="post" action="update_status.php">
                                            <input type="hidden" name="player_name" value="<?php echo htmlspecialchars($row['player_name']); ?>">
                                            <input type="hidden" name="team_name" value="<?php echo htmlspecialchars($teamName); ?>">
                                            <input type="hidden" name="game" value="volleyball">
                                            <input type="hidden" name="redirect_back" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                            <select name="is_approved" class="form-select" onchange="this.form.submit()">
                                                <option value="pending" <?php if ($row['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                                                <option value="approved" <?php if ($row['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="15" class="text-center alert">⚠ No players found for this team</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for form submission
        document.querySelectorAll('.form-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
```