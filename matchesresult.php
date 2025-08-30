<?php
session_start();
include_once("include/db_connect.php");
include_once("include/nav-bar.php");

// Fetch all games
$result = mysqli_query($con, "SELECT * FROM games");

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Cricket Teams Count
$q1 = mysqli_query($con, "SELECT COUNT(*) AS total FROM cricket_teams");
$cricket_count = mysqli_fetch_assoc($q1)['total'];

// Badminton Players Count
$q2 = mysqli_query($con, "SELECT COUNT(*) AS total FROM badminton_players");
$badminton_count = mysqli_fetch_assoc($q2)['total'];

// Table Tennis Players Count
$q3 = mysqli_query($con, "SELECT COUNT(*) AS total FROM tabletennis_players");
$tabletennis_count = mysqli_fetch_assoc($q3)['total'];

// Volleyball Teams Count
$q4 = mysqli_query($con, "SELECT COUNT(*) AS total FROM volleyball_teams");
$volleyball_count = mysqli_fetch_assoc($q4)['total'];

// Fetch match results
$query = "SELECT id, event_name, game, round, team1_name, team2_name, match_date, team1_score, team2_score, total_over, result_winner 
          FROM matches  
          ORDER BY match_date DESC";
$match_result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --volleyball-color: #3498db;
            --cricket-color: #2ecc71;
            --badminton-color: #e74c3c;
            --tabletennis-color: #f39c12;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #333;
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1 {
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
            color: white;
            padding: 2px;
            margin-bottom: 15px;
        }
        
        p {
            font-size: 1.2rem;
            opacity: 0.9;
            text-align: center;
            color: white;
            padding: 25px;
        }
        
        .dashboard-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: var(--dark-color);
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sports-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .sport-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .sport-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 600;
            font-size: 1.4rem;
        }
        
        .volleyball .card-header {
            background: var(--volleyball-color);
        }
        
        .cricket .card-header {
            background: var(--cricket-color);
        }
        
        .badminton .card-header {
            background: var(--badminton-color);
        }
        
        .tabletennis .card-header {
            background: var(--tabletennis-color);
        }
        
        .card-body {
            padding: 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-body i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .volleyball .card-body i {
            color: var(--volleyball-color);
        }
        
        .cricket .card-body i {
            color: var(--cricket-color);
        }
        
        .badminton .card-body i {
            color: var(--badminton-color);
        }
        
        .tabletennis .card-body i {
            color: var(--tabletennis-color);
        }
        
        .card-body p {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }
        
        .card-footer {
            background-color: var(--dark-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
        }
        
        .card-footer a {
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .matches-table {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .match-row {
            display: grid;
            grid-template-columns: 1fr 2fr 2fr 1fr 1fr 1fr;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        .match-row.header {
            font-weight: 600;
            background-color: var(--dark-color);
            color: white;
            border-radius: 5px;
        }
        
        .team-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .badge-volleyball {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--volleyball-color);
        }
        
        .badge-cricket {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--cricket-color);
        }
        
        .badge-badminton {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--badminton-color);
        }
        
        .badge-tabletennis {
            background-color: rgba(243, 156, 18, 0.2);
            color: var(--tabletennis-color);
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .results-table th, 
        .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .results-table th {
            background-color: var(--dark-color);
            color: white;
        }
        
        .results-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .results-table tr:hover {
            background-color: #e9e9e9;
        }
        
        .filter-section {
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .match-row {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }
            
            .match-row.header {
                display: none;
            }
            
            .sports-cards {
                grid-template-columns: 1fr;
            }
            
            .results-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header">
            <h1>Matches Results</h1>
            <p>Manage matches, view results, and track team statistics</p>
        </div>
        
        <div class="dashboard-section">
            <div class="section-title">
                <h3>Sports Dashboard</h3>
            </div>
            
            <div class="sports-cards">
                <div class="sport-card volleyball">
                    <div class="card-header">
                        Volleyball
                    </div>
                    <div class="card-body">
                        <i class="fas fa-volleyball-ball"></i>
                        <p><?php echo $volleyball_count; ?> Teams Registered</p>
                    </div>
                    <div class="card-footer">
                        <a href="uploadresults/viewvolleyballresult.php">View Detailed Volleyball Results</a>
                        <div><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
                
                <div class="sport-card cricket">
                    <div class="card-header">
                        Cricket
                    </div>
                    <div class="card-body">
                        <i class="fas fa-baseball-ball"></i>
                        <p><?php echo $cricket_count; ?> Teams Registered</p>
                    </div>
                    <div class="card-footer">
                        <a href="uploadresults/viewcricketresult.php">View Detailed Cricket Results</a>
                        <div><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
                
                <div class="sport-card badminton">
                    <div class="card-header">
                        Badminton
                    </div>
                    <div class="card-body">
                        <i class="fas fa-table-tennis"></i>
                        <p><?php echo $badminton_count; ?> Players Registered</p>
                    </div>
                    <div class="card-footer">
                        <a href="uploadresults/viewbadmintonresult.php">View Detailed Badminton Results</a>
                        <div><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
                
                <div class="sport-card tabletennis">
                    <div class="card-header">
                        Table Tennis
                    </div>
                    <div class="card-body">
                        <i class="fas fa-table-tennis"></i>
                        <p><?php echo $tabletennis_count; ?> Players Registered</p>
                    </div>
                    <div class="card-footer">
                        <a href="uploadresults/viewtabletennisresult.php">View Detailed Table Tennis Results</a>
                        <div><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-section">
            <div class="section-title">
                <h3>Recent Match Results</h3>
                <div class="filter">
                    <select class="form-select" id="recentSportFilter" style="width: 200px;">
                        <option value="all">All Sports</option>
                        <option value="volleyball">Volleyball</option>
                        <option value="cricket">Cricket</option>
                        <option value="badminton">Badminton</option>
                        <option value="table tennis">Table Tennis</option>
                    </select>
                </div>
            </div>
            
            <div class="match-row header">
                <div>Sport</div>
                <div>Teams</div>
                <div>Event</div>
                <div>Score</div>
                <div>Date</div>
                <div>Winner</div>
            </div>
            
            <div id="recentMatchesContainer">
                <?php
                if ($match_result && $match_result->num_rows > 0) {
                    while ($row = $match_result->fetch_assoc()) {
                        // Determine badge class based on game type
                        $badgeClass = '';
                        switch(strtolower($row['game'])) {
                            case 'volleyball':
                                $badgeClass = 'badge-volleyball';
                                break;
                            case 'cricket':
                                $badgeClass = 'badge-cricket';
                                break;
                            case 'badminton':
                                $badgeClass = 'badge-badminton';
                                break;
                            case 'table tennis':
                                $badgeClass = 'badge-tabletennis';
                                break;
                            default:
                                $badgeClass = 'badge-volleyball';
                        }
                        
                        // Format score based on game type
                        $scoreDisplay = $row['team1_score'] . '-' . $row['team2_score'];
                        
                        echo "<div class='match-row' data-sport='" . strtolower($row['game']) . "'>";
                        echo "<div><span class='team-badge $badgeClass'>" . ucwords($row['game']) . "</span></div>";
                        echo "<div>" . $row['team1_name'] . " vs " . $row['team2_name'] . "</div>";
                        echo "<div>" . $row['event_name'] . "</div>";
                        echo "<div>" . $scoreDisplay . "</div>";
                        echo "<div>" . $row['match_date'] . "</div>";
                        echo "<div>" . $row['result_winner'] . "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='match-row'><div colspan='6' class='text-center'>No match results found</div></div>";
                }
                ?>
            </div>
        </div>
        
        <div class="dashboard-section">
            <div class="section-title">
                <h3>All Match Results</h3>
                <div class="filter">
                    <select class="form-select" id="allSportFilter" style="width: 200px;">
                        <option value="all">All Sports</option>
                        <option value="volleyball">Volleyball</option>
                        <option value="cricket">Cricket</option>
                        <option value="badminton">Badminton</option>
                        <option value="table tennis">Table Tennis</option>
                    </select>
                </div>
            </div>
            
            <table class="results-table" id="allMatchesTable">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Game</th>
                        <th>Round</th>
                        <th>Team 1</th>
                        <th>Team 2</th>
                        <th>Match Date</th>
                        <th>Team 1 Score</th>
                        <th>Team 2 Score</th>
                        <th>Total Over</th>
                        <th>Winner</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Reset and reuse the match result query
                $match_result->data_seek(0);
                
                if ($match_result && $match_result->num_rows > 0) {
                    while ($row = $match_result->fetch_assoc()) {
                        echo "<tr data-sport='" . strtolower($row['game']) . "'>";
                        echo "<td>{$row['event_name']}</td>";
                        echo "<td>{$row['game']}</td>";
                        echo "<td>{$row['round']}</td>";
                        echo "<td>{$row['team1_name']}</td>";
                        echo "<td>{$row['team2_name']}</td>";
                        echo "<td>{$row['match_date']}</td>";
                        echo "<td>{$row['team1_score']}</td>";
                        echo "<td>{$row['team2_score']}</td>";
                        echo "<td>{$row['total_over']}</td>";
                        echo "<td>{$row['result_winner']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No match results found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Function to filter results by sport for recent matches
        document.getElementById('recentSportFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#recentMatchesContainer .match-row');
            
            rows.forEach(row => {
                if (row.classList.contains('header')) return;
                
                if (filterValue === 'all' || row.getAttribute('data-sport') === filterValue) {
                    row.style.display = 'grid';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Function to filter results by sport for all matches table
        document.getElementById('allSportFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#allMatchesTable tbody tr');
            
            rows.forEach(row => {
                if (filterValue === 'all' || row.getAttribute('data-sport') === filterValue) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    <?php require 'include/footer.php';?>
</body>
</html>
<?php 
// Close database connection if needed
if (isset($con)) {
    $con->close();
}