<?php
// Database connection
$con = new mysqli("localhost", "root", "", "sports-club");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Game Maker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery & Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        #myCarousel {
            position: relative;
            height: 500px; 
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
        .carousel-caption h3 { 
            color: red; 
            font-weight: bold; 
        }
        .carousel-caption p { 
            color: yellow; 
            font-size: 18px; 
        }
        .text-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: yellow;
            font-size: 36px;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px 30px;
            border-radius: 10px;
            text-shadow: 2px 2px 5px black;
        }
        .text-overlay h1 {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }
        .text-overlay h2 {
            font-size: 1.5rem;
        }
        .img-container {
            position: relative;
            width: 45%;
            cursor: pointer;
        }
        .img-container img {
            width: 200%;
            border-radius: 10px;
            display: block;
        }
        .game-name {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-150%, -90%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 5px black;
            text-align: center;
            pointer-events: none;
        }
        
        /* Existing About Us and FAQs Styles */
        .section-title {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 0 auto 22px; /* center with auto */
            background: #f1f1f1;
            padding: 10px 20px;
            border-radius: 8px;
            display: block;   /* change here */
            width: fit-content; /* text ke hisaab se box */
        }

        .about-text {
            max-width: 800px;
            margin: 0 auto 30px;
            font-size: 20px;
            color: #555;
            line-height: 1.7;
        }
        .values-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }
        .values-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            text-align: center;
        }
        .value-box {
            flex: 1 1 200px;
            max-width: 250px;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .value-box:last-child {
            border-right: none;
        }
        .value-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .value-box h4 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .value-box p {
            font-size: 14px;
            color: #666;
        }
        @media (max-width: 768px) {
            .value-box {
                border-right: none;
                border-bottom: 1px solid #ddd;
                padding-bottom: 15px;
            }
            .value-box:last-child {
                border-bottom: none;
            }
        }
        .faq-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            background: #f1f1f1;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .faq-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .faq-question {
            background: none;
            border: none;
            width: 100%;
            font-size: 18px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-weight: 600;
        }
        .faq-icon {
            margin-right: 10px;
            font-size: 20px;
        }
        .faq-answer {
            display: none;
            padding: 10px 0 0 35px;
            color: #555;
            font-size: 15px;
        }
        .faq-answer.active {
            display: block;
        }
    </style>
</head>

<body style="background-color: white;">
    <?php require 'include/nav-bar.php'; ?>

    <!-- Carousel -->
    <div id="myCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
                <img src="images/tt.jpg" class="d-block w-100" style="height: 500px;" alt="Table Tennis">
                <div class="carousel-caption">
                    <h3>Table Tennis Championship</h3>
                    <p>Join the thrilling table tennis matches!</p>
                </div>
                <div class="text-overlay">
                    <h1>The Game Maker</h1>
                    <h2>FG Kharian Women Sports Club</h2>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <img src="images/b.jpg" class="d-block w-100" style="height: 500px;" alt="Badminton">
                <div class="carousel-caption">
                    <h3>Badminton Tournament</h3>
                    <p>Be a part of the exciting badminton events!</p>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <img src="images/c.jpg" class="d-block w-100" style="height: 500px;" alt="Cricket">
                <div class="carousel-caption">
                    <h3>Cricket League</h3>
                    <p>Experience the best of women's cricket!</p>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <img src="images/v.jpg" class="d-block w-100" style="height: 500px;" alt="Volleyball">
                <div class="carousel-caption">
                    <h3>Volleyball Tournament</h3>
                    <p>Experience the best of women's volleyball tournament!</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div>
        <?php require 'include/top-alert.php';?>
    </div>
    
    <!-- About Us Section -->
<div class="container my-5">
  <h2 class="text-center mb-4 fw-bold">Our Sports</h2>
  <div class="row g-4">
    
    <!-- Badminton -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="row g-0 h-100">
          <div class="col p-4 d-flex flex-column">
            <h3 class="text-primary fw-bold">Badminton</h3>
            <p class="flex-grow-1">Be a part of the exciting badminton events!</p>
            <a href="badminton.php" class="btn btn-outline-primary mt-2">View Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img src="images/b.jpg" class="img-fluid rounded-end h-100" alt="Badminton" style="object-fit: cover; width:200px;">
          </div>
        </div>
      </div>
    </div>

    <!-- Cricket -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="row g-0 h-100">
          <div class="col p-4 d-flex flex-column">
            <h3 class="text-success fw-bold">Cricket</h3>
            <p class="flex-grow-1">Experience the best of women's cricket!</p>
            <a href="cricket.php" class="btn btn-outline-success mt-2">View Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img src="images/c.jpg" class="img-fluid rounded-end h-100" alt="Cricket" style="object-fit: cover; width:200px;">
          </div>
        </div>
      </div>
    </div>

    <!-- Table Tennis -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="row g-0 h-100">
          <div class="col p-4 d-flex flex-column">
            <h3 class="text-danger fw-bold">Table Tennis</h3>
            <p class="flex-grow-1">Join the thrilling table tennis matches!</p>
            <a href="tabletennis.php" class="btn btn-outline-danger mt-2">View Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img src="images/tt.jpg" class="img-fluid rounded-end h-100" alt="Table Tennis" style="object-fit: cover; width:200px;">
          </div>
        </div>
      </div>
    </div>

    <!-- Volleyball -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="row g-0 h-100">
          <div class="col p-4 d-flex flex-column">
            <h3 class="text-warning fw-bold">Volleyball</h3>
            <p class="flex-grow-1">Experience the best of women's volleyball tournament!</p>
            <a href="volleyball.php" class="btn btn-outline-warning mt-2">View Details</a>
          </div>
          <div class="col-auto d-none d-lg-block">
            <img src="images/v.jpg" class="img-fluid rounded-end h-100" alt="Volleyball" style="object-fit: cover; width:200px;">
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


    <!-- About Us Section -->
    <section id="how-it-works" style="padding:50px 0; background:#f9f9f9;">
  <div class="container text-center">
    <h2 class="section-title">How to Use Our System</h2>
    <div class="steps-row" style="display:flex; flex-wrap:wrap; justify-content:center; gap:20px; margin-top:30px;">
      
      <div class="step-box" style="flex:1 1 220px; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
        <div class="step-icon" style="font-size:40px; margin-bottom:15px;">📝</div>
        <h4>Create Account</h4>
        <p>Sign up by creating your account using the signup form.</p>
      </div>

      <div class="step-box" style="flex:1 1 220px; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
        <div class="step-icon" style="font-size:40px; margin-bottom:15px;">👥</div>
        <h4>Register</h4>
        <p>Register yourself or your team in any active sports event.</p>
      </div>

      <div class="step-box" style="flex:1 1 220px; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
        <div class="step-icon" style="font-size:40px; margin-bottom:15px;">📅</div>
        <h4>Get Schedule</h4>
        <p>Check the list of matches scheduled for your team or event.</p>
      </div>

      <div class="step-box" style="flex:1 1 220px; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
        <div class="step-icon" style="font-size:40px; margin-bottom:15px;">🏆</div>
        <h4>View Results</h4>
        <p>View final results of the matches.</p>
      </div>

    </div>
  </div>
</section>

  <?php require 'events-news.php';?>
    </div>
    </div>

    <!-- FAQs Section -->
    <section id="faqs" style="padding: 40px 0; background-color: #f8f9fa;">
        <div class="container">
            <h2 class="faq-title">FAQs</h2>
            <div class="faq-item">
                <button class="faq-question">
                    <span class="faq-icon">🏐</span> How can I register for an event?
                    <span class="faq-toggle">-</span>
                </button>
                <div class="faq-answer active">
                    You can register by logging in to your account, selecting the event from the list, filling in your details, and clicking "Register".
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span class="faq-icon">👤</span> Do I need to create an account to join?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Yes, creating an account is necessary to join an event and keep your information saved for organizers.
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span class="faq-icon">📅</span> How can I view match schedules?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Match schedules are available on the "Schedule" page and inside your dashboard after login.
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span class="faq-icon">☎</span> How can I contact the organizers?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    You can contact organizers using the "Contact Us" page or details given in the event description.
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script>
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqAnswer = button.nextElementSibling;
                const toggleIcon = button.querySelector('.faq-toggle');
                faqAnswer.classList.toggle('active');
                toggleIcon.textContent = faqAnswer.classList.contains('active') ? '-' : '+';
            });
        });
    </script>

    <!-- Footer -->
    <?php require 'include/footer.php'; ?>

    <?php $con->close(); ?>
</body>
</html>