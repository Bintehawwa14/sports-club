<?php
include '../include/db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['match_id']) && isset($_POST['winner_id']) && isset($_POST['loser_id'])) {
        $match_id = $_POST['match_id'];
        $winner_id = $_POST['winner_id'];
        $loser_id = $_POST['loser_id'];
        
        // Update the match result in the database
        $sql = "UPDATE matches SET winner_id='$winner_id', loser_id='$loser_id' WHERE id='$match_id'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Match result saved successfully.";
            // Redirect or show success message
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
