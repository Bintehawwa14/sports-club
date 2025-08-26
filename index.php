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
        .carousel-caption h3 { color: red; font-weight: bold; }
        .carousel-caption p { color: yellow; font-size: 18px; }
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
    
        
        </style>
</head>

<body style="background-color: white;" >
<?php require 'include/nav-bar.php'; ?>
      </div>
																					
</div>       

        <!-- Carousel -->
        <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel">
            
            <div class="carousel-indicators">
            
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="3"></button>
            </div>
              
            <div class="carousel-inner">
                
                <div class="carousel-item active">
                    <img src="images/tt.jpg" class="d-block w-100" style="height: 500px;">
                    <div class="carousel-caption">
                        <h3>Table tennis Championship</h3>
                        <p>Join the thrilling table tennis matches!</p>
                    </div>
                  <div class="text-overlay">
                    <h1> The Game Maker<h1>
                        <h2>FG KHARIAN WOMEN SPORTS CLUB<h2>
                            
                        </div>  
                </div>
                <div class="carousel-item">
                    <img src="images/b.jpg" class="d-block w-100" style="height: 500px;">
                    <div class="carousel-caption">
                        <h3>Badminton Tournament</h3>
                        <p>Be a part of the exciting badminton events!</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/c.jpg" class="d-block w-100" style="height: 500px;">
                    <div class="carousel-caption">
                        <h3>Cricket League</h3>
                        <p>Experience the best of women's cricket!</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/v.jpg" class="d-block w-100" style="height: 500px;">
                    <div class="carousel-caption">
                        <h3>Volleyball tournement</h3>
                        <p>Experience the best of Volleyball tournment!</p>
                    </div>
                </div>
                 
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
     
 <div><?php require 'include/top-alert.php'; ?></div>
    
    <!-- About Us Section -->
    <div class="container my-4">
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
                  
                  <h3 class="mb-0 d-inline-block mb-2 text-danger">Table tennis</h3>
                  <div class="mb-1 text-muted">FGKWSC</div>
                  <p class="card-text mb-auto">Join the thrilling Table tennis matches</p>
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
                  <p class="mb-auto">Experience the best of women's volleyball tournment!</p>
                  <a href="volleyball.php" class="stretched-link">Details</a>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <img class="bd-placeholder-img" width="200" height="250" src="images/v.jpg" alt="">

                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
        <!-- About Us Section -->
<section id="about" style="padding: 50px 0; background-color: #fff;">
  <div class="container">
    <h2 class="section-title">About Us</h2>
    <p class="about-text text-center">
      <strong>FG Kharian Women Sports Club</strong> is dedicated to empowering women through sports. 
      We organize various sporting events, provide fair match scheduling, and promote a healthy and active lifestyle.
    </p>

    <h3 class="values-title">Our Values</h3>
    <div class="values-row">
      
      <!-- Value 1 -->
      <div class="value-box">
        <div class="value-icon">🏆</div>
        <h4>Fair Play</h4>
        <p>We ensure equal opportunities and transparent competition for all participants.</p>
      </div>

      <!-- Value 2 -->
      <div class="value-box">
        <div class="value-icon">📅</div>
        <h4>Organized Events</h4>
        <p>All events are well-planned with fixed schedules for smooth execution.</p>
      </div>

      <!-- Value 3 -->
      <div class="value-box">
        <div class="value-icon">🤝</div>
        <h4>Community Spirit</h4>
        <p>We encourage teamwork, sportsmanship, and friendly competition.</p>
      </div>

      <!-- Value 4 -->
      <div class="value-box">
        <div class="value-icon">📢</div>
        <h4>Easy Communication</h4>
        <p>Stay updated with instant event notifications and announcements.</p>
      </div>

    </div>
  </div>
</section>

<!-- CSS -->
<style>
  .section-title {
    font-size: 28px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 22px;
    background: #f1f1f1;
    padding: 10px 20px;
    border-radius: 8px;
    display: inline-block;
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
</style>

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

<!-- CSS -->
<style>
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
        </body>
    <!-- Footer -->
    <?php require 'include/footer.php'; ?>
</html>
