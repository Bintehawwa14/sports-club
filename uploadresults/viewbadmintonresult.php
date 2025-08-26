<?php
session_start();
include_once('../include/db_connect.php');

// Fetch badminton results with detailed information
$sql = "SELECT br.*, 
               e.event_name,
               (SELECT COUNT(*) FROM badmintonresultdetails WHERE id = br.id) as has_details
        FROM badmintonresult br 
        JOIN events e ON br.event_name = e.event_name
        ORDER BY br.match_date DESC, br.event_name";
$result = mysqli_query($con, $sql);

// Check for query errors
if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

$matches = [];
if (mysqli_num_rows($result) > 0) {
    $matches = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // For each match, fetch detailed results
    foreach ($matches as &$match) {
        $result_id = $match['id'];
        $detail_sql = "SELECT * FROM badmintonresultdetails WHERE id = $result_id";
        $detail_result = mysqli_query($con, $detail_sql);
        
        if ($detail_result && mysqli_num_rows($detail_result) > 0) {
            $match['details'] = mysqli_fetch_assoc($detail_result);
        } else {
            $match['details'] = [];
        }
    }
    unset($match); // break the reference
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Badminton Match Results Viewer</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/badmintonpage.jpg);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      width: 70%;
      max-width: 1200px;
      background-image: url(../images/badmintonform.jpg);
      background-size: cover;
      background-position: center;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    
    h3 {
      text-align: center;
      color: #cf0dafff;
      margin-bottom: 20px;
    }

    .alert-warning {
        background-color: #fff3cd;
        border: 2px solid #ffcc00;
        color: #856404;
        font-weight: bold;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
    }

    .field {
      flex: 1 1 200px;
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .view-only-field {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #f8f9fa;
      min-height: 38px;
    }

    .detailedScore {
        margin-top: 10px;
        padding: 10px;
        background: #eef;
        border-radius: 5px;
    }

    .btn-sm-custom {
        display: block;
        margin: 10px 0;
        padding: 8px 16px;
        font-size: 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-sm-custom:hover {
        background-color: #0056b3;
    }

    .match-block {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        background: #f9f9f9;
    }

    .two-column-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .two-column-row .set-section {
      flex: 1;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 8px;
      background-color: #fff;
    }

    .winner-badge {
        background-color: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        margin-left: 8px;
    }

    .no-results {
        text-align: center;
        padding: 30px;
        background-color: #fff3cd;
        border-radius: 8px;
        border: 1px solid #ffeaa7;
    }

    .score-display {
        font-size: 1.2rem;
        font-weight: bold;
        color: #2c3e50;
        text-align: center;
        padding: 10px;
        background-color: #ecf0f1;
        border-radius: 5px;
        margin: 10px 0;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .stat-item {
        background: white;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        text-align: center;
    }

    .stat-value {
        font-size: 16px;
        font-weight: bold;
        color: #2c3e50;
    }

    .stat-label {
        font-size: 11px;
        color: #7f8c8d;
        text-transform: uppercase;
    }

    .team-a-color {
        color: #3498db;
    }

    .team-b-color {
        color: #e74c3c;
    }

    @media (max-width: 768px) {
      .two-column-row, .form-row {
        flex-direction: column;
      }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Badminton Match Results</h2>

    <div id="matches-container">
        <?php if (!empty($matches)): ?>
            <?php foreach ($matches as $index => $match): ?>
            <div class="match-block">
                <div class="form-row">
                  <div class="field">
                      <label>Event Name</label>
                      <div class="view-only-field"><?php echo htmlspecialchars($match['event_name']); ?></div>
                  </div>
                  <div class="field">
                      <label>Match Date</label>
                      <div class="view-only-field"><?php echo date('F j, Y', strtotime($match['match_date'])); ?></div>
                  </div>
                  <div class="field">
                      <label>Round</label>
                      <div class="view-only-field"><?php echo htmlspecialchars($match['round_name']); ?></div>
                  </div>
                  <div class="field">
                      <label>Team A Name</label>
                      <div class="view-only-field">
                          <?php echo htmlspecialchars($match['team_a']); ?>
                          <?php if ($match['winner'] == $match['team_a']): ?>
                              <span class="winner-badge">Winner</span>
                          <?php endif; ?>
                      </div>
                  </div>
                  <div class="field">
                      <label>Team B Name</label>
                      <div class="view-only-field">
                          <?php echo htmlspecialchars($match['team_b']); ?>
                          <?php if ($match['winner'] == $match['team_b']): ?>
                              <span class="winner-badge">Winner</span>
                          <?php endif; ?>
                      </div>
                  </div>
                </div>

                <div class="score-display">
                    Final Score: <?php echo $match['score_a'] . ' - ' . $match['score_b']; ?>
                </div>

                <button type="button" class="btn-sm-custom" onclick="toggleDetailedScore(this)">Show Detailed Result</button>

                <div class="detailedScore" style="display: none;">
                    <h3>Detailed Score</h3>
                    
                    <?php if (!empty($match['details'])): ?>
                        <div class="two-column-row">
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 1</h3></div>
                                <div class="stat-grid">
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set1_team_a']) ? $match['details']['set1_team_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Points</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set1_team_b']) ? $match['details']['set1_team_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Points</div>
                                    </div>
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set1_faults_a']) ? $match['details']['set1_faults_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Faults</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set1_faults_b']) ? $match['details']['set1_faults_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Faults</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 2</h3></div>
                                <div class="stat-grid">
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set2_team_a']) ? $match['details']['set2_team_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Points</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set2_team_b']) ? $match['details']['set2_team_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Points</div>
                                    </div>
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set2_faults_a']) ? $match['details']['set2_faults_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Faults</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set2_faults_b']) ? $match['details']['set2_faults_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Faults</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="two-column-row">
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 3</h3></div>
                                <div class="stat-grid">
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set3_team_a']) ? $match['details']['set3_team_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Points</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set3_team_b']) ? $match['details']['set3_team_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Points</div>
                                    </div>
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set3_faults_a']) ? $match['details']['set3_faults_a'] : '0'; ?></div>
                                        <div class="stat-label">Team A Faults</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['set3_faults_b']) ? $match['details']['set3_faults_b'] : '0'; ?></div>
                                        <div class="stat-label">Team B Faults</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="set-section">
                                <div class="form-section-title"><h3>Other Match Stats</h3></div>
                                <div class="stat-grid">
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['net_errors_a']) ? $match['details']['net_errors_a'] : '0'; ?></div>
                                        <div class="stat-label">Net Errors</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['net_errors_b']) ? $match['details']['net_errors_b'] : '0'; ?></div>
                                        <div class="stat-label">Net Errors</div>
                                    </div>
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['smashes_a']) ? $match['details']['smashes_a'] : '0'; ?></div>
                                        <div class="stat-label">Smashes</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['smashes_b']) ? $match['details']['smashes_b'] : '0'; ?></div>
                                        <div class="stat-label">Smashes</div>
                                    </div>
                                    <div class="stat-item team-a-color">
                                        <div class="stat-value"><?php echo isset($match['details']['let_serves_a']) ? $match['details']['let_serves_a'] : '0'; ?></div>
                                        <div class="stat-label">Let Serves</div>
                                    </div>
                                    <div class="stat-item team-b-color">
                                        <div class="stat-value"><?php echo isset($match['details']['let_serves_b']) ? $match['details']['let_serves_b'] : '0'; ?></div>
                                        <div class="stat-label">Let Serves</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No detailed statistics available for this match.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <h3>No Badminton Results Available</h3>
                <p>There are no badminton match results in the database yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleDetailedScore(btn) {
        var details = btn.nextElementSibling;
        if (details.style.display === "none" || details.style.display === "") {
            details.style.display = "block";
            btn.textContent = "Hide Detailed Result";
        } else {
            details.style.display = "none";
            btn.textContent = "Show Detailed Result";
        }
    }

    // Print functionality
    function printResults() {
        window.print();
    }

    // Add print button to page
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.createElement('button');
        printBtn.textContent = 'Print Results';
        printBtn.className = 'btn-sm-custom';
        printBtn.style.position = 'fixed';
        printBtn.style.top = '20px';
        printBtn.style.right = '20px';
        printBtn.style.zIndex = '1000';
        printBtn.onclick = printResults;
        
        document.body.appendChild(printBtn);
    });
</script>
</body>
</html>