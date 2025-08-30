<?php require 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Netball - Sport Details</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url(images/tt.jpg); /* Replace with your netball image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        .images {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 30px;
            flex-wrap: wrap;
        }
        .images img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .details {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .details h2 {
            margin-bottom: 15px;
            color: #34495e;
        }
        .details p {
            font-size: 18px;
            margin: 10px 0;
        }
        .requirements {
            margin-top: 20px;
        }
        .requirements ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .requirements li {
            margin-bottom: 8px;
            font-size: 17px;
        }
        .back-button {
            display: block;
            width: 170px;
            margin: 30px auto 0;
            text-align: center;
            padding: 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <h1>Table tennis</h1>

   

   <div class="details">
    <h2>Game Details</h2>
    <p><strong>Members Required:</strong> 2 players per team (Doubles) or 1 player per team (Singles)</p>
    <p><strong>Type of Game:</strong> Indoor Individual or Doubles Sport</p>
    <p><strong>Equipment Needed:</strong> Table Tennis Table, Paddles, Table Tennis Balls, Sports Attire</p>
    <p><strong>Description:</strong> Table Tennis is a fast-paced indoor game that can be played as singles or doubles. Players use paddles to hit a lightweight ball across a table divided by a net. The sport enhances hand-eye coordination, quick reflexes, and strategic thinking.</p>   
        <div><a href="index.php" class="back-button">← Back to Home</a></div>
    </div>

</body>
</html>
