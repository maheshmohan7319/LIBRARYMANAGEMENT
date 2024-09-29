<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 

// Check if a cancellation request was made
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];

    // Get the reservation details
    $query = "SELECT reserve_from FROM reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();
        $reserve_from = $reservation['reserve_from'];

        // Check if the current date is before reserve_from
        if (strtotime($reserve_from) > time()) {
            // Update the reservation status to 'cancelled'
            $updateQuery = "UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $reservation_id);

            if ($updateStmt->execute()) {
                $_SESSION['toast_message'] = 'Reservation cancelled successfully.';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'Failed to cancel reservation. Please try again.';
                $_SESSION['toast_type'] = 'danger';
            }
        } else {
            $_SESSION['toast_message'] = 'You can only cancel reservations before the reserved date.';
            $_SESSION['toast_type'] = 'danger';
        }
    } else {
        $_SESSION['toast_message'] = 'Reservation not found.';
        $_SESSION['toast_type'] = 'danger';
    }

    $stmt->close();
}

// Fetch ongoing and history reservations
$queryOngoing = "
    SELECT reservations.*, Books.title 
    FROM reservations 
    JOIN Books ON reservations.book_id = Books.book_id 
    WHERE reservations.user_id = ? AND reservations.status IN ('approved', 'pending','picked')";
$stmtOngoing = $conn->prepare($queryOngoing);
$stmtOngoing->bind_param("i", $user_id);
$stmtOngoing->execute();
$resultOngoing = $stmtOngoing->get_result();

$queryHistory = "
    SELECT reservations.*, Books.title 
    FROM reservations 
    JOIN Books ON reservations.book_id = Books.book_id 
    WHERE reservations.user_id = ? AND reservations.status NOT IN ('approved', 'pending','picked')";
$stmtHistory = $conn->prepare($queryHistory);
$stmtHistory->bind_param("i", $user_id);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <?php include 'header_home.php'; ?>
</header>

<div class="container mt-5 d-flex justify-content-center">
    <div class="col-md-12">
        <h2 class="mb-4 text-center">Bookings</h2>

        <?php if (isset($_SESSION['toast_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['toast_type']); ?> alert-dismissible fade show" role="alert" id="toast-message">
                <?php echo htmlspecialchars($_SESSION['toast_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['toast_message']); // Clear the message ?>
        <?php endif; ?>

        <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="true">Ongoing Bookings</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">Booking History</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="myTabContent">
            <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                <?php if ($resultOngoing->num_rows > 0): ?>
                    <div class="row">
                    <?php while ($row = $resultOngoing->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Reservation ID: <?php echo htmlspecialchars($row['reservation_id']); ?></h5>
                <p class="card-text"><strong>Book Name:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                <p class="card-text"><strong>Reserved From:</strong> <?php echo htmlspecialchars($row['reserve_from']); ?></p>
                <p class="card-text"><strong>Reserved To:</strong> <?php echo htmlspecialchars($row['reserve_to']); ?></p>
                <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                <p class="card-text"><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                
                <!-- Show Cancel button only if status is 'pending' -->
                <?php if ($row['status'] === 'pending'): ?>
                    <form method="POST" action="" class="mt-2">
                        <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($row['reservation_id']); ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this reservation?');">
                            Cancel Reservation
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p>No ongoing bookings available.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <?php if ($resultHistory->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($row = $resultHistory->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Reservation ID: <?php echo htmlspecialchars($row['reservation_id']); ?></h5>
                                        <p class="card-text"><strong>Book Name:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                                        <p class="card-text"><strong>Reserved From:</strong> <?php echo htmlspecialchars($row['reserve_from']); ?></p>
                                        <p class="card-text"><strong>Reserved To:</strong> <?php echo htmlspecialchars($row['reserve_to']); ?></p>
                                        <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                        <p class="card-text"><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p>No booking history available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Automatically hide the toast message after 5 seconds
    setTimeout(() => {
        const toast = document.getElementById('toast-message');
        if (toast) {
            const bsToast = new bootstrap.Alert(toast);
            bsToast.close();
        }
    }, 2000); // Change 5000 to your desired duration in milliseconds
</script>
</body>
</html>