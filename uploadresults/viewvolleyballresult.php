<?php
session_start();
include_once('../include/db_connect.php');

// Fetch volleyball results with set details
$sql = "SELECT vr.*, 
               e.event_name,
               (SELECT COUNT(*) FROM volleyball_set_details WHERE result_id = vr.id) as set_count
        FROM volleyballresult vr 
        JOIN events e ON vr.event_name = e.event_name
        ORDER BY vr.match_date DESC, vr.event_name";
$result = mysqli_query($con, $sql);

$matches = [];
if ($result && mysqli_num_rows($result) > 0) {
    $matches = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // For each match, fetch set details
    foreach ($matches as &$match) {
        $result_id = $match['id'];
        $set_sql = "SELECT * FROM volleyball_set_details WHERE result_id = $result_id ORDER BY set_number";
        $set_result = mysqli_query($con, $set_sql);
        
        if ($set_result && mysqli_num_rows($set_result) > 0) {
            $match['sets'] = mysqli_fetch_all($set_result, MYSQLI_ASSOC);
        } else {
            $match['sets'] = [];
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
<title>Volleyball Match Results Viewer</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/volleyballpage.jpg);
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
      background-image: url(../images/volleyballform.jpg);
      background-size: cover;
      background-position: center;
    }

    h2 {
      text-align: center;
      color: white;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    h3 {
      text-align: center;
      color: #006400;
      margin-bottom: 20px;
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
      color: #333;
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

    .view-only {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 500;
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

    .set-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 10px;
    }

    .stat-item {
        padding: 5px;
        border-bottom: 1px dashed #eee;
    }

    .team-a-stats {
        color: #3498db;
        font-weight: 500;
    }

    .team-b-stats {
        color: #e74c3c;
        font-weight: 500;
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

    @media (max-width: 768px) {
      .two-column-row, .form-row {
        flex-direction: column;
      }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Volleyball Match Results</h2>

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
                  <div class="field">
                      <label>Final Score</label>
                      <div class="view-only-field"><?php echo $match['score_a'] . ' - ' . $match['score_b']; ?></div>
                  </div>
                </div>

                <button type="button" class="btn-sm-custom" onclick="toggleDetailedScore(this)">Show Detailed Result</button>

                <div class="detailedScore" style="display: none;">
                    <h3>Detailed Score</h3>
                    
                    <?php if (!empty($match['sets'])): ?>
                        <div class="two-column-row">
                            <?php foreach ($match['sets'] as $set): ?>
                                <?php if ($set['set_number'] <= 2): ?>
                                    <div class="set-section">
                                        <div class="form-section-title"><h4>Set <?php echo $set['set_number']; ?></h4></div>
                                        <div class="set-stats">
                                            <div class="team-a-stats">
                                                <div class="stat-item">Points: <?php echo $set['team_a_points']; ?></div>
                                                <div class="stat-item">Aces: <?php echo $set['team_a_aces']; ?></div>
                                                <div class="stat-item">Spikes: <?php echo $set['team_a_spikes']; ?></div>
                                                <div class="stat-item">Blocks: <?php echo $set['team_a_blocks']; ?></div>
                                                <div class="stat-item">Errors: <?php echo $set['team_a_errors']; ?></div>
                                            </div>
                                            <div class="team-b-stats">
                                                <div class="stat-item">Points: <?php echo $set['team_b_points']; ?></div>
                                                <div class="stat-item">Aces: <?php echo $set['team_b_aces']; ?></div>
                                                <div class="stat-item">Spikes: <?php echo $set['team_b_spikes']; ?></div>
                                                <div class="stat-item">Blocks: <?php echo $set['team_b_blocks']; ?></div>
                                                <div class="stat-item">Errors: <?php echo $set['team_b_errors']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="two-column-row">
                            <?php foreach ($match['sets'] as $set): ?>
                                <?php if ($set['set_number'] > 2 && $set['set_number'] <= 4): ?>
                                    <div class="set-section">
                                        <div class="form-section-title"><h4>Set <?php echo $set['set_number']; ?></h4></div>
                                        <div class="set-stats">
                                            <div class="team-a-stats">
                                                <div class="stat-item">Points: <?php echo $set['team_a_points']; ?></div>
                                                <div class="stat-item">Aces: <?php echo $set['team_a_aces']; ?></div>
                                                <div class="stat-item">Spikes: <?php echo $set['team_a_spikes']; ?></div>
                                                <div class="stat-item">Blocks: <?php echo $set['team_a_blocks']; ?></div>
                                                <div class="stat-item">Errors: <?php echo $set['team_a_errors']; ?></div>
                                            </div>
                                            <div class="team-b-stats">
                                                <div class="stat-item">Points: <?php echo $set['team_b_points']; ?></div>
                                                <div class="stat-item">Aces: <?php echo $set['team_b_aces']; ?></div>
                                                <div class="stat-item">Spikes: <?php echo $set['team_b_spikes']; ?></div>
                                                <div class="stat-item">Blocks: <?php echo $set['team_b_blocks']; ?></div>
                                                <div class="stat-item">Errors: <?php echo $set['team_b_errors']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php foreach ($match['sets'] as $set): ?>
                            <?php if ($set['set_number'] == 5): ?>
                                <div class="set-section">
                                    <div class="form-section-title"><h4>Set <?php echo $set['set_number']; ?></h4></div>
                                    <div class="set-stats">
                                        <div class="team-a-stats">
                                            <div class="stat-item">Points: <?php echo $set['team_a_points']; ?></div>
                                            <div class="stat-item">Aces: <?php echo $set['team_a_aces']; ?></div>
                                            <div class="stat-item">Spikes: <?php echo $set['team_a_spikes']; ?></div>
                                            <div class="stat-item">Blocks: <?php echo $set['team_a_blocks']; ?></div>
                                            <div class="stat-item">Errors: <?php echo $set['team_a_errors']; ?></div>
                                        </div>
                                        <div class="team-b-stats">
                                            <div class="stat-item">Points: <?php echo $set['team_b_points']; ?></div>
                                            <div class="stat-item">Aces: <?php echo $set['team_b_aces']; ?></div>
                                            <div class="stat-item">Spikes: <?php echo $set['team_b_spikes']; ?></div>
                                            <div class="stat-item">Blocks: <?php echo $set['team_b_blocks']; ?></div>
                                            <div class="stat-item">Errors: <?php echo $set['team_b_errors']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No detailed set information available for this match.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <h3>No Volleyball Results Available</h3>
                <p>There are no volleyball match results in the database yet.</p>
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