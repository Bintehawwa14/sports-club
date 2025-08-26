<?php require 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - The Game Make</title>
  

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- jQuery & Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    /* Optional custom styling for About Us page */
    body {
      background-color: #f8f9fa;
    }
    .bd-placeholder-img {
      object-fit: cover;
    }
  </style>
</head>
<body>

  <div class="container my-4">
    <h2 class="text-center mb-4">Sports We Organize</h2>

    <div class="row mb-2">
      <div class="col-md-6">
        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 class="mb-0 d-inline-block mb-2 text-primary">Badminton</h3>
            <div class="mb-1 text-muted">FGKWSC</div>
            <p class="card-text mb-auto">Be a part of the exciting badminton events!</p>
            <a href="badminton.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="bd-placeholder-img" width="200" height="250" src="images/b.jpg" alt="">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 class="mb-0 d-inline-block mb-2 text-success">Cricket</h3>
            <div class="mb-1 text-muted">FGKWSC</div>
            <p class="mb-auto">Experience the best of women's cricket!</p>
            <a href="cricket.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="bd-placeholder-img" width="200" height="250" src="images/c.jpg" alt="">
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-md-6">
        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 class="mb-0 d-inline-block mb-2 text-danger">Table Tennis</h3>
            <div class="mb-1 text-muted">FGKWSC</div>
            <p class="card-text mb-auto">Join the thrilling table tennis matches.</p>
            <a href="tabletennis.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="bd-placeholder-img" width="200" height="250" src="images/tt.jpg" alt="">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <div class="col p-4 d-flex flex-column position-static">
            <h3 class="mb-0 d-inline-block mb-2 text-warning">Volleyball</h3>
            <div class="mb-1 text-muted">FGKWSC</div>
            <p class="mb-auto">Experience the best of women's volleyball tournaments!</p>
            <a href="volleyball.php" class="stretched-link">Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img class="bd-placeholder-img" width="200" height="250" src="images/v.jpg" alt="">
          </div>
        </div>
      </div>
    </div>

  </div>
    <?php require 'include/footer.php';?>
</body>
</html>
