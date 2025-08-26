<?php
require_once __DIR__ . '/fpdf182/fpdf.php';
include('../include/db_connect.php');
// PDF class
class PDF extends FPDF {
    // Header
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Scheduled Matches Report',0,1,'C');
        $this->Ln(5);

        // Table header
        $this->SetFont('Arial','B',10);
        $this->Cell(10,10,'ID',1,0,'C');
        $this->Cell(35,10,'Event',1,0,'C');
        $this->Cell(32,10,'Team 1',1,0,'C');
        $this->Cell(32,10,'Team 2',1,0,'C');
        $this->Cell(25,10,'Game',1,0,'C');
        $this->Cell(15,10,'Round',1,0,'C');
        $this->Cell(25,10,'Match Date',1,0,'C');
        $this->Cell(25,10,'Status',1,1,'C');
    }
}

// PDF create
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);


// ✅ Fetch scheduled matches with event name
$sql = "SELECT m.id, e.event_name, m.team1_name, m.team2_name, m.game, m.round, m.match_date, m.match_status
        FROM matches m
        JOIN events e ON m.event_id = e.id
        WHERE m.match_status = 'scheduled'";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10,10,$row['id'],1,0,'C');
        $pdf->Cell(35,10,$row['event_name'],1,0,'C');
        $pdf->Cell(32,10,$row['team1_name'],1,0,'C');
        $pdf->Cell(32,10,$row['team2_name'],1,0,'C');
        $pdf->Cell(25,10,$row['game'],1,0,'C');
        $pdf->Cell(15,10,$row['round'],1,0,'C');
        $pdf->Cell(25,10,$row['match_date'],1,0,'C');
        $pdf->Cell(25,10,$row['match_status'],1,1,'C');
    }
} else {
    $pdf->Cell(0,10,'No scheduled matches found',1,1,'C');
}

$pdf->Output();
?>
