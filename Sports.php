<?php require 'include/nav-bar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sports We Organize - The Game Maker</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- jQuery & Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background-color: #f1f3f5;
      font-family: 'Poppins', sans-serif;
      color: #2d3436;
    }

    /* Section Title */
    .section-title {
      font-size: 2.8rem;
      font-weight: 700;
      color: #2b2d42;
      text-transform: uppercase;
      position: relative;
      margin-bottom: 3rem;
      text-align: center;
      background: linear-gradient(45deg, #1a73e8, #28a745);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .section-title::after {
      content: '';
      width: 100px;
      height: 5px;
      background: linear-gradient(to right, #1a73e8, #28a745);
      position: absolute;
      bottom: -12px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 3px;
      transition: width 0.4s ease;
    }
    .section-title:hover::after {
      width: 150px;
    }

    /* Card Styling */
    .sport-card {
      background: linear-gradient(135deg, #ffffff 0%, #dfe6e9 100%);
      border: none;
      border-radius: 20px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
      transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
      overflow: hidden;
      position: relative;
    }
    .sport-card:hover {
      transform: translateY(-12px) scale(1.02);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
      background: linear-gradient(135deg, #f8f9fa 0%, #b2bec3 100%);
    }

    /* Card Content */
    .sport-card .col {
      padding: 2rem;
    }
    .sport-card h3 {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    }
   
    .sport-card p {
      font-size: 1.1rem;
      color: #2d3436;
      line-height: 1.7;
      margin-bottom: 1.5rem;
    }
    .sport-card a.stretched-link {
      display: inline-block;
      padding: 0.5rem 1.5rem;
      background: #1a73e8;
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      border-radius: 25px;
      transition: background 0.3s ease, transform 0.3s ease;
    }
    .sport-card a.stretched-link:hover {
      background: #0d47a1;
      transform: scale(1.05);
      text-decoration: none;
    }

    /* Image Styling */
    .sport-img {
      object-fit: cover;
      width: 220px;
      height: 280px;
      transition: transform 0.4s ease, opacity 0.4s ease;
      border-left: 3px solid rgba(0, 0, 0, 0.1);
    }
    .sport-card:hover .sport-img {
      transform: scale(1.05);
      opacity: 0.85;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .sport-card {
        margin-bottom: 2rem;
      }
      .sport-card h3 {
        font-size: 1.6rem;
      }
      .sport-card p {
        font-size: 1rem;
      }
      .sport-card .club-badge {
        font-size: 0.85rem;
      }
      .section-title {
        font-size: 2.2rem;
      }
      .sport-img {
        width: 100%;
        height: 200px;
        border-left: none;
        border-top: 3px solid rgba(0, 0, 0, 0.1);
      }
    }
  </style>
</head>
<body>

  <div class="container my-5">
    <h2 class="section-title">Sports We Organize</h2>

    <div class="row mb-3">
      <div class="col-md-6">
        <div class="row no-gutters sport-card" aria-labelledby="badminton-title">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 id="badminton-title" class="mb-0 d-inline-block mb-2 text-primary">Badminton</h3>
            
            <p class="card-text mb-auto">Be a part of the exciting badminton events!</p>
            <a href="badminton.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="sport-img" width="220" height="280" src="images/b.jpg" alt="Badminton players in action">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row no-gutters sport-card" aria-labelledby="cricket-title">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 id="cricket-title" class="mb-0 d-inline-block mb-2 text-success">Cricket</h3>
            
            <p class="mb-auto">Experience the best of women's cricket!</p>
            <a href="cricket.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="sport-img" width="220" height="280" src="images/c.jpg" alt="Cricket players on the field">
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <div class="row no-gutters sport-card" aria-labelledby="tabletennis-title">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 id="tabletennis-title" class="mb-0 d-inline-block mb-2 text-danger">Table Tennis</h3>
            
            <p class="card-text mb-auto">Join the thrilling table tennis matches.</p>
            <a href="tabletennis.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="sport-img" width="220" height="280" src="images/tt.jpg" alt="Table tennis match in progress">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row no-gutters sport-card" aria-labelledby="volleyball-title">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 id="volleyball-title" class="mb-0 d-inline-block mb-2 text-warning">Volleyball</h3>
           
            <p class="mb-auto">Experience the best of women's volleyball tournaments!</p>
            <a href="volleyball.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="sport-img" width="220" height="280" src="images/v.jpg" alt="Volleyball players in action">
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require 'include/footer.php'; ?>
</body>
</html>