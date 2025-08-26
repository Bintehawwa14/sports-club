<?php
session_start();
include_once('../include/db_connect.php');

// Fetch cricket results with detailed information
$sql = "SELECT cr.*, 
               e.event_name,
               (SELECT COUNT(*) FROM cricketresultdetails WHERE result_id = cr.id) as has_details
        FROM cricketresult cr 
        JOIN events e ON cr.event_name = e.event_name
        ORDER BY cr.match_date DESC, cr.event_name";
$result = mysqli_query($con, $sql);

$matches = [];
if ($result && mysqli_num_rows($result) > 0) {
    $matches = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // For each match, fetch detailed results
    foreach ($matches as &$match) {
        $result_id = $match['id'];
        $detail_sql = "SELECT * FROM cricketresultdetails WHERE result_id = $result_id";
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
<title>Cricket Match Results Viewer</title>
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/cricketpage.jpg);
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
      background-image: url(../images/cricketform.jpg);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      width: 70%;
      max-width: 1200px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
    }

    h2 {
      color: #3478bdff;
      margin: 0;
      font-size: 32px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    }
    
    .subtitle {
      color: #e41c1cff;
      font-size: 18px;
      margin-top: 5px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .field {
      flex: 1 1 220px;
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: bold;
      margin-bottom: 8px;
      color: #2c3e50;
      font-size: 14px;
    }

    .view-only-field {
      padding: 12px 15px;
      border: 2px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
      font-size: 15px;
      min-height: 20px;
    }

    .detailedScore {
        margin-top: 15px;
        padding: 20px;
        background: #e8f4fc;
        border-radius: 10px;
        display: none;
        border: 2px solid #3498db;
    }

    .toggle-btn {
        display: block;
        margin: 15px 0;
        padding: 12px 20px;
        font-size: 16px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .toggle-btn:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    .match-block {
        border: 2px solid #e0e0e0;
        padding: 25px;
        margin-bottom: 15px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    
    .match-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .match-info {
        background: linear-gradient(135deg, #2c3e50, #4a6580);
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    
    .team-box {
        flex: 1;
        padding: 20px;
        background: #f9f9f9;
        border: 2px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    
    .team-title {
        text-align: center;
        color: #2c3e50;
        padding-bottom: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #3498db;
        font-size: 20px;
    }
    
    .alert {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: center;
        font-size: 18px;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border: 2px solid #ffeaa7;
        color: #856404;
    }
    
    .score-display {
        font-weight: bold;
        font-size: 18px;
        text-align: center;
        padding: 15px;
        background-color: #ecf0f1;
        border-radius: 8px;
        margin: 15px 0;
        border: 2px solid #bdc3c7;
    }
    
    .winner-badge {
        background-color: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.9rem;
        margin-left: 10px;
        display: inline-block;
    }
    
    .toss-info {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 12px;
        border-radius: 8px;
        margin: 15px 0;
        text-align: center;
    }
    
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .stat-item {
        background: white;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        text-align: center;
    }
    
    .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .stat-label {
        font-size: 12px;
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
        .form-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .team-box {
            margin-bottom: 20px;
        }
        
        .container {
            width: 90%;
            padding: 20px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Cricket Match Results</h2>
        <div class="subtitle">View match statistics and scores</div>
    </div>
    
    <?php if (empty($matches)): ?>
        <div class="alert alert-warning">No cricket match results found in database!</div>
    <?php else: ?>
        <div id="matches-container">
            <?php foreach ($matches as $index => $match): ?>
            <div class="match-block">
                <div class="match-info">
                    <strong>Event:</strong> <?php echo htmlspecialchars($match['event_name']); ?> | 
                    <strong>Date:</strong> <?php echo date('F j, Y', strtotime($match['match_date'])); ?> | 
                    <strong>Round:</strong> <?php echo htmlspecialchars($match['round_name']); ?>
                </div>
                
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
                </div>
                
                <div class="form-row">
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
                  <div class="field">
                      <label>Total Overs</label>
                      <div class="view-only-field"><?php echo $match['total_overs']; ?></div>
                  </div>
                </div>
                
                <div class="toss-info">
                    <strong>Toss Winner:</strong> <?php echo htmlspecialchars($match['toss_winner']); ?> | 
                    <strong>Decision:</strong> <?php echo htmlspecialchars($match['decision_after_toss']); ?>
                </div>
                
                <div class="score-display">
                    Final Score: <?php echo $match['score_a']; ?> - <?php echo $match['score_b']; ?>
                </div>

                <button type="button" class="toggle-btn" onclick="toggleDetailedScore(this)">Show Detailed Statistics</button>
                
                <div class="detailedScore">
                    <h3 style="color: #2c3e50; text-align: center; margin-bottom: 20px;">Detailed Match Statistics</h3>
                    
                    <?php if (!empty($match['details'])): ?>
                    <div class="form-row">
                        <div class="team-box">
                            <div class="team-title team-a-color"><?php echo htmlspecialchars($match['team_a']); ?></div>
                            <div class="stat-grid">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['runs_a']; ?></div>
                                    <div class="stat-label">Runs</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['overs_a']; ?></div>
                                    <div class="stat-label">Overs</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['wickets_a']; ?></div>
                                    <div class="stat-label">Wickets</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['extras_a']; ?></div>
                                    <div class="stat-label">Extras</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['fours_a']; ?></div>
                                    <div class="stat-label">Fours</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['sixes_a']; ?></div>
                                    <div class="stat-label">Sixes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['catches_a']; ?></div>
                                    <div class="stat-label">Catches</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['outs_a']; ?></div>
                                    <div class="stat-label">Outs</div>
                                </div>
                            </div>
                        </div>

                        <div class="team-box">
                            <div class="team-title team-b-color"><?php echo htmlspecialchars($match['team_b']); ?></div>
                            <div class="stat-grid">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['runs_b']; ?></div>
                                    <div class="stat-label">Runs</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['overs_b']; ?></div>
                                    <div class="stat-label">Overs</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['wickets_b']; ?></div>
                                    <div class="stat-label">Wickets</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['extras_b']; ?></div>
                                    <div class="stat-label">Extras</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['fours_b']; ?></div>
                                    <div class="stat-label">Fours</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['sixes_b']; ?></div>
                                    <div class="stat-label">Sixes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['catches_b']; ?></div>
                                    <div class="stat-label">Catches</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $match['details']['outs_b']; ?></div>
                                    <div class="stat-label">Outs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No detailed statistics available for this match.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleDetailedScore(btn) {
        var details = btn.nextElementSibling;
        if (details.style.display === "none" || details.style.display === "") {
            details.style.display = "block";
            btn.textContent = "Hide Detailed Statistics";
        } else {
            details.style.display = "none";
            btn.textContent = "Show Detailed Statistics";
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
        printBtn.className = 'toggle-btn';
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