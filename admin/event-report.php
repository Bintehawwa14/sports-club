```php
<?php
session_start();
require_once '../include/db_connect.php';

// Fetch all events for dropdown
$eventQuery = "SELECT id, event_name FROM events";
$eventResult = mysqli_query($con, $eventQuery);
if (!$eventResult) {
    die("Error fetching events: " . mysqli_error($con));
}

// Event filter
$selectedEvent = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

// Fetch matches with event name using prepared statements
$sql = "SELECT 
            m.id, 
            m.event_id, 
            e.event_name, 
            m.game, 
            m.team1_name, 
            m.team2_name, 
            m.round, 
            m.match_date, 
            m.match_status, 
            m.toss_winner, 
            m.team1_score, 
            m.team2_score, 
            m.total_over, 
            m.winner_name, 
            m.loser_name, 
            m.result_winner 
        FROM matches m 
        LEFT JOIN events e ON m.event_id = e.id";
if ($selectedEvent) {
    $sql .= " WHERE m.event_id = ?";
}
$sql .= " ORDER BY m.id DESC";

$stmt = mysqli_prepare($con, $sql);
if ($selectedEvent) {
    mysqli_stmt_bind_param($stmt, "i", $selectedEvent);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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

        .users-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
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
            background-color: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
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
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-4">
        <!-- Back Button -->
        <a href="dashboard.php" class="btn btn-link text-blue-600 mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>

        <!-- Header -->
        <div class="users-container">
            <div class="users-header mb-4">
                <h1>📋 Event Report</h1>
            </div>

            <!-- Event Selection Form -->
            <form method="POST" class="mb-4 text-center">
                <label for="event_id" class="fw-bold me-2">Select Event:</label>
                <select name="event_id" id="event_id" class="form-select d-inline-block w-auto mx-2" required>
                    <option value="">-- Choose Event --</option>
                    <?php while ($ev = mysqli_fetch_assoc($eventResult)): ?>
                        <option value="<?php echo $ev['id']; ?>" <?php if ($selectedEvent == $ev['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($ev['event_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Show Report</button>
            </form>

            <!-- Matches Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Match Date</th>
                            <th>Game</th>
                            <th>Team 1</th>
                            <th>Team 2</th>
                            <th>Round</th>
                            <th>Status</th>
                            <th>Toss Winner</th>
                            <th>Team 1 Score</th>
                            <th>Team 2 Score</th>
                            <th>Total Overs</th>
                            <th>Winner</th>
                            <th>Loser</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                    <td><?php echo date("d M Y", strtotime($row['match_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['game']); ?></td>
                                    <td><?php echo htmlspecialchars($row['team1_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['team2_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['round']); ?></td>
                                    <td><?php echo htmlspecialchars($row['match_status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['toss_winner']); ?></td>
                                    <td><?php echo htmlspecialchars($row['team1_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['team2_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['total_over']); ?></td>
                                    <td><?php echo htmlspecialchars($row['winner_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['loser_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['result_winner']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="14" class="text-center alert">⚠ No matches found for this event!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```