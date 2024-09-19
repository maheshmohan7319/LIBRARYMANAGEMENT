<?php
session_start(); // Start session to use session variables
include 'db_connect.php'; // Include the database connection

// Check if user is logged in and has a valid user ID
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">You must be logged in to reserve a book.</div>';
    exit;
}

$user_id = $_SESSION['user_id']; // Fetch the user ID from the session

// Fetch all books from the database
$query = "SELECT * FROM books";
$result = $conn->query($query);

// Handle book reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $book_id = $_POST['book_id'];
    $reserve_from = date('Y-m-d'); // Start reservation from today
    $reserve_to = date('Y-m-d', strtotime('+7 days')); // Reserve for 7 days
    $status = 'Reserved'; // Example status
    $created_at = date('Y-m-d H:i:s');

    // Insert reservation into the reservations table
    $reservationQuery = "INSERT INTO reservations (user_id, book_id, reserve_from, reserve_to, status, created_at) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($reservationQuery);
    $stmt->bind_param("iissss", $user_id, $book_id, $reserve_from, $reserve_to, $status, $created_at);

    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Book reserved successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to reserve the book. Please try again.</div>';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Available Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #000000;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://e7.pngegg.com/pngimages/142/76/png-clipart-white-and-orange-book-logo-symbol-yellow-orange-logo-ibooks-orange-logo-thumbnail.png" alt="Bootstrap" width="30" height="24" class="me-2">
      <span class="navbar-heading fs-4 fw-bold" style="color: #023C6E;">LIBRARY</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="status.php" style="color: #023C6E;">Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="booking_history.php" style="color: #023C6E;">Booking History</a>
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
                            <p class="card-text mb-2"><strong>Quantity:</strong> <?php echo htmlspecialchars($row['qty']); ?></p>
                            <p class="card-text mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><small class="text-muted">Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <form method="POST" action="">
                                <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                <button type="submit" name="reserve" class="btn btn-outline-primary w-100">Reserve Book</button>
                            </form>
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

<footer class="bg-body-tertiary text-center" style="background-color: #f0e895;">
    <div class="container p-4">
    </div>
    <div class="text-center p-3" style="background-color: #f0e895;">
     LIBRARY
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
