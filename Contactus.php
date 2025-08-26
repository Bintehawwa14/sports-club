<?php require 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        :root {
            --primary: #2c3e50;    /* Dark blue */
            --secondary: #e74c3c;  /* Red */
            --accent: #3498db;     /* Blue */
            --light: #f5f5f5;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        h2 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            text-align: center;
        }
        
        p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }
        
        /* Contact Card (Single & Centered) */
        .contact-card {
            background: var(--white);
            background-image: url(images/contactusform1bg.jpg);
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            margin: 50px auto;
            max-width: 500px;
            transition: transform 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .contact-card h3 {
            color: var(--secondary);
            margin-top: 0;
            font-size: 1.5rem;
        }
        
        .contact-card .icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 20px;
        }
        
        /* Contact Form */
        .contact-form-section {
            background: var(--white);
            background-image: url(images/contactusform2bg.jpg);
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            padding: 40px;
            margin: 20px auto 60px;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .contact-form-section h2 {
            color: var(--primary);
            margin-top: 0;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #c0392b;
        }
        address {
            color: black;
        }
        
        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2.2rem;
            }
            
            .contact-header p {
                font-size: 1rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
        <div class="container">
            <h2>Contact Us</h2>
            <h2>Get In Touch</h2>
            <p>Have questions about our sports event management platform? Our team is here to help you participates in the events as a player,teams and build stronger sports communities.</p>
        </div>
    <div class="container">
        <!-- Single Centered Contact Card -->
        <div class="contact-card">
            <div class="icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Support Center</h3>
            <p>Need help with our platform? Our support team is available 24/7.</p>
            <p><strong>Email:</strong> areej.fatimaa418@gmail.com</p>
            <p><strong>Phone:</strong> 0357-7789309</p>
            <p><strong>Whatsapp:</strong> 0310-6787809</p>
        </div>
        
        <!-- Contact Form -->
        <section class="contact-form-section">
            <h2>Send Us a Message</h2>
            <form>
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject">
                        <option value="">Select a topic</option>
                        <option value="support">Technical Support</option>
                        <option value="feedback">Product Feedback</option>
                        <option value="partnership">Partnership Inquiry</option>
                        <option value="media">Media Request</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </section>
    </div>
     <?php require 'include/footer.php';?>
</body>
</html>

