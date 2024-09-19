<?php
session_start(); 
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 

// Fetch ongoing reservations from the database
$queryOngoing = "SELECT * FROM reservations WHERE user_id = ? AND status = 'Ongoing'";
$stmtOngoing = $conn->prepare($queryOngoing);
$stmtOngoing->bind_param("i", $user_id);
$stmtOngoing->execute();
$resultOngoing = $stmtOngoing->get_result();

if (!$resultOngoing) {
    die('Query failed: ' . $conn->error);
}

// Fetch past reservations from the database
$queryHistory = "SELECT * FROM reservations WHERE user_id = ? AND status != 'Ongoing'";
$stmtHistory = $conn->prepare($queryHistory);
$stmtHistory->bind_param("i", $user_id);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

if (!$resultHistory) {
    die('Query failed: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .no-bookings-container {
            text-align: center;
            margin-top: 50px;
        }
        .no-bookings-container p {
            font-size: 1.2rem;
            color: #023C6E;
        }
        .reservation-card {
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .reservation-card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .reservation-card h5 {
            margin-bottom: 15px;
            font-size: 1.25rem;
            color: #023C6E;
        }
        .reservation-card p {
            margin: 0;
            font-size: 1rem;
            color: #555;
        }
        .reservation-card.ticket {
            background-color: #f8f9fa;
            border: 2px solid #023C6E;
            border-radius: 15px;
            padding: 20px;
            position: relative;
        }
        .reservation-card.ticket::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            width: 100%;
            height: 15px;
            background-color: #023C6E;
            border-radius: 0 0 15px 15px;
            transform: translateX(-50%);
        }
        .modal-header {
            background-color: #023C6E;
            color: white;
        }
        .footer {
            background-color: #000000;
            color: #f0e895;
            padding: 20px 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #000000;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="https://e7.pngegg.com/pngimages/142/76/png-clipart-white-and-orange-book-logo-symbol-yellow-orange-logo-ibooks-orange-logo-thumbnail.png" alt="Library Logo" width="30" height="24" class="me-2">
      <span class="navbar-heading fs-4 fw-bold" style="color: white;">LIBRARY</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php" style="color: white;">Home</a>
        </li>
      
        <li class="nav-item ms-3">
          <a class="btn btn-dark" href="logout.php" style="color: white;">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
<h2  class="mb-4 text-center">Bookings</h2>
    <!-- Tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="true">Ongoing Bookings</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">Booking History</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <!-- Ongoing Bookings Tab -->
      <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
        <?php if ($resultOngoing->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $resultOngoing->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="reservation-card ticket" data-bs-toggle="modal" data-bs-target="#reservationModal" data-reservation-id="<?php echo htmlspecialchars($row['reservation_id']); ?>" data-book-id="<?php echo htmlspecialchars($row['book_id']); ?>" data-reserve-from="<?php echo htmlspecialchars($row['reserve_from']); ?>" data-reserve-to="<?php echo htmlspecialchars($row['reserve_to']); ?>" data-status="<?php echo htmlspecialchars($row['status']); ?>" data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>">
                            <h5>Reservation ID: <?php echo htmlspecialchars($row['reservation_id']); ?></h5>
                            <p><strong>Book ID:</strong> <?php echo htmlspecialchars($row['book_id']); ?></p>
                            <p><strong>Reserved From:</strong> <?php echo htmlspecialchars($row['reserve_from']); ?></p>
                            <p><strong>Reserved To:</strong> <?php echo htmlspecialchars($row['reserve_to']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-bookings-container">
                <div id="no-bookings-animation" style="width: 250px; height: auto; margin: 0 auto;"></div>
                <p>No ongoing bookings available.</p>
            </div>
        <?php endif; ?>
      </div>
      <!-- Booking History Tab -->
      <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <?php if ($resultHistory->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $resultHistory->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="reservation-card ticket" data-bs-toggle="modal" data-bs-target="#reservationModal" data-reservation-id="<?php echo htmlspecialchars($row['reservation_id']); ?>" data-book-id="<?php echo htmlspecialchars($row['book_id']); ?>" data-reserve-from="<?php echo htmlspecialchars($row['reserve_from']); ?>" data-reserve-to="<?php echo htmlspecialchars($row['reserve_to']); ?>" data-status="<?php echo htmlspecialchars($row['status']); ?>" data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>">
                            <h5>Reservation ID: <?php echo htmlspecialchars($row['reservation_id']); ?></h5>
                            <p><strong>Book ID:</strong> <?php echo htmlspecialchars($row['book_id']); ?></p>
                            <p><strong>Reserved From:</strong> <?php echo htmlspecialchars($row['reserve_from']); ?></p>
                            <p><strong>Reserved To:</strong> <?php echo htmlspecialchars($row['reserve_to']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-bookings-container">
                <div id="no-bookings-animation" style="width: 250px; height: auto; margin: 0 auto;"></div>
                <p>No booking history available.</p>
            </div>
        <?php endif; ?>
      </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reservationModalLabel">Reservation Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Reservation ID:</dt>
          <dd class="col-sm-8" id="modalReservationId"></dd>
          <dt class="col-sm-4">Book ID:</dt>
          <dd class="col-sm-8" id="modalBookId"></dd>
          <dt class="col-sm-4">Reserved From:</dt>
          <dd class="col-sm-8" id="modalReserveFrom"></dd>
          <dt class="col-sm-4">Reserved To:</dt>
          <dd class="col-sm-8" id="modalReserveTo"></dd>
          <dt class="col-sm-4">Status:</dt>
          <dd class="col-sm-8" id="modalStatus"></dd>
          <dt class="col-sm-4">Created At:</dt>
          <dd class="col-sm-8" id="modalCreatedAt"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="footer text-center">
    <div class="text-center">
        <p>LIBRARY</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.5/lottie.min.js"></script>
<script>
    // Lottie animation for no bookings
    var animation = lottie.loadAnimation({
        container: document.getElementById('no-bookings-animation'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'admin/assets/img/Animation - 1725346560143.json'
    });

    // Event listener for reservation card click
    document.querySelectorAll('.reservation-card').forEach(card => {
        card.addEventListener('click', function() {
            var reservationId = this.getAttribute('data-reservation-id');
            var bookId = this.getAttribute('data-book-id');
            var reserveFrom = this.getAttribute('data-reserve-from');
            var reserveTo = this.getAttribute('data-reserve-to');
            var status = this.getAttribute('data-status');
            var createdAt = this.getAttribute('data-created-at');
            
            document.getElementById('modalReservationId').textContent = reservationId;
            document.getElementById('modalBookId').textContent = bookId;
            document.getElementById('modalReserveFrom').textContent = reserveFrom;
            document.getElementById('modalReserveTo').textContent = reserveTo;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalCreatedAt').textContent = createdAt;
        });
    });
</script>
</body>
</html>
