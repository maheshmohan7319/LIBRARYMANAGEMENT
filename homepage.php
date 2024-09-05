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
    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://e7.pngegg.com/pngimages/142/76/png-clipart-white-and-orange-book-logo-symbol-yellow-orange-logo-ibooks-orange-logo-thumbnail.png" alt="Bootstrap" width="30" height="24" class="me-2">
      <!-- Nav Heading -->
      <span class="navbar-heading fs-4 fw-bold" style="color: #023C6E;">LIBRARY</span>
    </a>

    <!-- Toggler for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="status.php" style="color: #023C6E;">Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="booking_history.php" style="color: #023C6E;">Booking History</a>
        </li>
        <!-- Logout Button -->
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
                        <!-- Display book image if available -->
                        <?php 
                        $imagePath = '../assets/uploads/' . htmlspecialchars($row['image']);
                        if (!empty($row['image']) && file_exists($imagePath)): ?>
                            <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="Book Image" style="height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <!-- Display a default image if book image is not available -->
                            <img src="default-book-image.jpg" class="card-img-top" alt="Default Book Image" style="height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text mb-2"><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                            <p class="card-text mb-2"><strong>Quantity:</strong> <?php echo htmlspecialchars($row['availability']); ?></p>
                            <p class="card-text mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><small class="text-muted">Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <!-- Reserve Book Button -->
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
        <div class="no-items-container">
            <img src="https://w7.pngwing.com/pngs/277/965/png-transparent-empty-cart-illustration.png" alt="No Items Animation" style="width: 250px; height: auto;">
            <p class="no-items-message">No books available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<section class="">
  <!-- Footer -->
  <footer class="bg-body-tertiary text-center" style="background-color: #f0e895;">
    <!-- Grid container -->
    <div class="container p-4">
      <!--Grid row-->
      <!--Grid row-->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: #f0e895;">
     LIBRARY
    </div>
    <!-- Copyright -->
  </footer>
  <!-- Footer -->
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
