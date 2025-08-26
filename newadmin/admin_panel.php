<?php 
include_once('../include/db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sports Match Scheduler</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: url('https://images.unsplash.com/photo-1521412644187-c49fa049e84d') no-repeat center center fixed;
      background-size: cover;
      color: #333;
    }
    .container {
      width: 80%;
      max-width: 700px;
      margin: 30px auto;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    .step { display: none; }
    .step.active { display: block; }
    label {
      display: block;
      margin-top: 15px;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      padding: 10px 20px;
      margin: 20px 10px 0 0;
      border: none;
      border-radius: 8px;
      background: #007BFF;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover { background: #0056b3; }
    .progress { display: flex; margin-bottom: 20px; }
    .progress div {
      flex: 1;
      height: 10px;
      background: #ccc;
      margin: 0 5px;
      border-radius: 10px;
    }
    .progress div.active { background: #007BFF; }
  </style>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <?php include_once('includes/sidebar.php');?>
  <div class="container">
    <h2>Match Scheduler </h2>

    <div class="progress">
      <div class="progress-bar step-bar-0 active"></div>
      <div class="progress-bar step-bar-1"></div>
      <div class="progress-bar step-bar-2"></div>
      <div class="progress-bar step-bar-3"></div>
      <div class="progress-bar step-bar-4"></div>
    </div>

    <form id="adminForm">
    
      <div class="step active">
        <label>Tournament Type:</label>
        <input type="text" id="tournament_type" required>
      </div>

     
      <div class="step">
        <label>Select Game:</label>
        <select id="game_id" required>
          <option value="">-- Select a Game --</option>
          <?php
            $result = mysqli_query($con, "SELECT * FROM games");
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<option value='{$row['id']}'>{$row['game_name']}</option>";
            }
          ?>
        </select>
      </div>

      
      <div class="step">
        <label>Select Round:</label>
        <select id="round" required>
          <option value="First Round">First Round</option>
          <option value="Quarterfinal">Quarterfinal</option>
          <option value="Semifinal">Semifinal</option>
          <option value="Final">Final</option>
        </select>
      </div>

      <div class="step">
        <label>Number of Teams:</label>
        <input type="number" id="number_of_teams" min="2" max="32" required>
      </div>

   
      <div class="step">
        <div id="teamInputsContainer"></div>
        <label>Players Per Team:</label>
        <input type="number" id="players_per_team" min="1" required>
        <button type="button" onclick="submitTeams()">Save Teams & Generate Matches</button>
      </div>

      <div style="margin-top: 20px;">
        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="nextStep()">Next</button>
      </div>
    </form>
  </div>

<script>
  let currentStep = 0;
  const steps = document.querySelectorAll('.step');
  const progressBars = document.querySelectorAll('.progress-bar');

  function showStep(index) {
    steps.forEach((step, i) => {
      step.classList.toggle('active', i === index);
      progressBars[i].classList.toggle('active', i <= index);
    });

    if (index === 4) {
      
      const numTeams = parseInt(document.getElementById('number_of_teams').value);
      const container = document.getElementById('teamInputsContainer');
      container.innerHTML = "<h3>Enter Team Names:</h3>";
      for (let i = 1; i <= numTeams; i++) {
        container.innerHTML += `<input type="text" name="team_name[]" placeholder="Team ${i} Name" required><br>`;
      }
    }
  }

  function nextStep() {
    if (currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  }

  function prevStep() {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  }

  function submitTeams() {
    const formData = new FormData();
    const teamInputs = document.getElementsByName('team_name[]');
    const tournament_type = document.getElementById('tournament_type').value;
    const game_id = document.getElementById('game_id').value;
    const round = document.getElementById('round').value;
    const number_of_teams = document.getElementById('number_of_teams').value;
    const players_per_team = document.getElementById('players_per_team').value;

    formData.append('tournament_type', tournament_type);
    formData.append('game_id', game_id);
    formData.append('round', round);
    formData.append('number_of_teams', number_of_teams);
    formData.append('players_per_team', players_per_team);

    teamInputs.forEach(input => {
      formData.append('team_name[]', input.value);
    });

    fetch('save_teams.php', {
      method: 'POST',
      body: formData
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
          
          const params = new URLSearchParams({
            tournament_type: tournament_type,
            game_id: game_id,
            round: round
          });
          window.location.href = 'generate_single_elimination.php?' + params.toString();
        } else {
          alert(data.message || 'Error saving teams.');
        }
      }).catch(error => {
        console.error('Error:', error);
        alert('Failed to save teams.');
      });
  }

 
  document.getElementById("adminForm").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
      e.preventDefault();
      nextStep();
    }
  });
</script>
</body>
</html>
