<?php
// Fetch events
$eventsql = "SELECT event_name, start_date, status, is_closed, end_date, event_date, sport
             FROM events";

$eventresult = $con->query($eventsql);
?>
<style>
/* Active Events Section Styles */
.event-section {
    padding: 50px 0;
}
.event-section h2 {
    color: #1a73e8;
    font-weight: bold;
    margin-bottom: 30px;
    text-transform: uppercase;
    text-align: center;
}
.card {
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #ffffff 0%, #e8f0fe 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}
.card-title {
    color: #1a73e8;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}
.card-text {
    color: #333;
    font-size: 1rem;
    line-height: 1.6;
}
.sport-badge {
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.9rem;
    margin-bottom: 10px;
}
.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: bold;
}
.status-open {
    background-color: #28a745;
    color: #fff;
}
.status-closed {
    background-color: #d9534f;
    color: #fff;
}
@media (max-width: 768px) {
    .card {
        margin-bottom: 20px;
    }
    .carousel-inner img {
        height: 250px;
    }
}
</style>

<!-- Active Events Section -->
<div id='events-news' class="container event-section">
    <h2 class="text-center">Active Events</h2>
    <div class="row d-flex justify-content-center align-item-center">
        <?php
        if ($eventresult && $eventresult->num_rows > 0) {
            while ($eventRow = $eventresult->fetch_assoc()) {
                $isClosed = strtolower($eventRow['is_closed']) === 'yes';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($eventRow['event_name']); ?></h5>
                            <span class="sport-badge"><?php echo htmlspecialchars($eventRow['sport']); ?></span>
                            
                            <!-- Status Badge -->
                            <?php if ($isClosed): ?>
                                <span class="status-badge status-closed">CLOSED</span>
                            <?php else: ?>
                                <span class="status-badge status-open">OPEN</span>
                            <?php endif; ?>

                            <p class="card-text mt-2">
                                <strong>Start Date:</strong> <?php echo htmlspecialchars($eventRow['start_date']); ?><br>
                                <strong>Registration Last Date:</strong> <?php echo htmlspecialchars($eventRow['end_date']); ?><br>
                                <strong>Event Start:</strong> <?php echo htmlspecialchars($eventRow['event_date']); ?><br>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-center">No active events found.</p>';
        }
        ?>
    </div>
</div>
