<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has a valid user ID
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">You must be logged in to reserve a book.</div>';
    exit;
}

$user_id = $_SESSION['user_id']; // Fetch the user ID from the session

$search_term = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_term = trim($_POST['search_term']);
}

// Fetch books based on search term
$query = "SELECT * FROM books WHERE title LIKE ?"; 
$search_param = '%' . $search_term . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

$toast_message = '';
$toast_type = '';  // For success or error styles

// Handle book reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $book_id = $_POST['book_id'];
    $reserve_from = $_POST['reserve_from'];
    $reserve_to = $_POST['reserve_to'];
    $status = 'Reserved'; // Example status
    $created_at = date('Y-m-d H:i:s');

    // Check if book quantity is sufficient
    $checkQtyQuery = "SELECT qty FROM books WHERE book_id = ?";
    $checkStmt = $conn->prepare($checkQtyQuery);
    $checkStmt->bind_param("i", $book_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $book = $checkResult->fetch_assoc();
    
    if ($book['qty'] > 0) {
        // Check for existing reservation with the same book_id and user_id
        $checkReservationQuery = "SELECT * FROM reservations WHERE book_id = ? AND user_id = ? AND status = 'Reserved'";
        $checkReservationStmt = $conn->prepare($checkReservationQuery);
        $checkReservationStmt->bind_param("ii", $book_id, $user_id);
        $checkReservationStmt->execute();
        $existingReservation = $checkReservationStmt->get_result()->fetch_assoc();

        if (!$existingReservation) {
            // Insert reservation into the reservations table
            $reservationQuery = "INSERT INTO reservations (user_id, book_id, reserve_from, reserve_to, status, created_at) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($reservationQuery);
            $stmt->bind_param("iissss", $user_id, $book_id, $reserve_from, $reserve_to, $status, $created_at);

            if ($stmt->execute()) {
                // Update book quantity
                $updateQtyQuery = "UPDATE books SET qty = qty - 1 WHERE book_id = ?";
                $updateStmt = $conn->prepare($updateQtyQuery);
                $updateStmt->bind_param("i", $book_id);
                $updateStmt->execute();

                // Set success message
                $toast_message = 'Book reserved successfully!';
                $toast_type = 'success';
            } else {
                // Set error message
                $toast_message = 'Failed to reserve the book. Please try again.';
                $toast_type = 'danger';
            }

            $stmt->close();
            $updateStmt->close();
        } else {
            // Set error message for duplicate reservation
            $toast_message = 'You have already reserved this book.';
            $toast_type = 'danger';
        }

        $checkReservationStmt->close();
    } else {
        $toast_message = 'Book is out of stock.';
        $toast_type = 'danger';
    }

    $checkStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Available Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #000000;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://e7.pngegg.com/pngimages/142/76/png-clipart-white-and-orange-book-logo-symbol-yellow-orange-logo-ibooks-orange-logo-thumbnail.png" alt="Library Logo" width="30" height="24" class="me-2">
      <span class="navbar-heading fs-4 fw-bold" style="color: white;">LIBRARY</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="booking_history.php" style="color: white;">Booking</a>
        </li>
        <li class="nav-item ms-3">
          <a class="btn btn-dark" href="logout.php" style="color: #f0e895;">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Available Books</h2>
    
    <!-- Search Form -->
    <form method="POST" action="homepage.php" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Search by title" aria-label="Search by title">
            <button class="btn btn-primary" type="submit" name="search">Search</button>
        </div>
    </form>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <?php 
                        $imagePath = 'assets/uploads/' . htmlspecialchars($row['image']);
                        if (!empty($row['image']) && file_exists($imagePath)): ?>
                            <img src="<?php echo $imagePath; ?>" class="card-img-top img-fluid object-fit-cover" alt="Book Image">
                        <?php else: ?>
                            <img src="default-book-image.jpg" class="card-img-top img-fluid object-fit-cover" alt="Default Book Image">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text mb-2"><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                            <p class="card-text mb-2"><strong>Quantity:</strong> <span class="book-qty"><?php echo htmlspecialchars($row['qty']); ?></span></p>
                            <p class="card-text mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><small class="text-muted">Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#reserveModal" data-book-id="<?php echo $row['book_id']; ?>" data-book-title="<?php echo htmlspecialchars($row['title']); ?>">
                                Reserve Book
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center">
            <img src="https://w7.pngwing.com/pngs/277/965/png-transparent-empty-cart-illustration.png" alt="No Items Animation" class="img-fluid" style="width: 250px; height: auto;">
            <p class="mt-3 fs-4">No books available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Toast message -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center text-bg-<?php echo $toast_type; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?php echo $toast_message; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Reserve Modal -->
<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveModalLabel">Reserve Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_id" id="book-id">
                    <div class="mb-3">
                        <label for="reserve-from" class="form-label">Reserve From</label>
                        <input type="date" class="form-control" id="reserve-from" name="reserve_from" required>
                    </div>
                    <div class="mb-3">
                        <label for="reserve-to" class="form-label">Reserve To</label>
                        <input type="date" class="form-control" id="reserve-to" name="reserve_to" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="reserve">Reserve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handle modal data
    var reserveModal = document.getElementById('reserveModal');
    reserveModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var bookId = button.getAttribute('data-book-id');
        var bookTitle = button.getAttribute('data-book-title');
        var modalTitle = reserveModal.querySelector('.modal-title');
        var bookIdInput = reserveModal.querySelector('#book-id');
        modalTitle.textContent = 'Reserve ' + bookTitle;
        bookIdInput.value = bookId;
    });

    // Show toast message if there is one
    var toastLiveExample = document.getElementById('liveToast');
    if (toastLiveExample) {
        var toast = new bootstrap.Toast(toastLiveExample);
        toast.show();
    }
});
</script>

</body>
</html>
