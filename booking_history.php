<?php
include 'db_connect.php'; // Include the database connection

// Fetch all borrowing details from the database
$query = "SELECT * FROM borrowings";
$result = $conn->query($query);

if (!$result) {
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
    </style>
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
          <a class="nav-link" href="index.php" style="color: #023C6E;">Home</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="status.php" style="color: #023C6E;">Status</a>
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
    <h2 class="mb-4">Booking History</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Borrowing ID</th>
                    <th>User ID</th>
                    <th>Book ID</th>
                    <th>Borrowed From</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['borrowing_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['borrowed_from']); ?></td>
                        <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-bookings-container">
            <div id="no-bookings-animation" style="width: 250px; height: auto; margin: 0 auto;"></div>
            <p>No booking history available.</p>
        </div>
    <?php endif; ?>
</div>

<div>
  <section>
    <!-- Footer -->
    <footer class="bg-body-tertiary text-center" style="background-color: #000000;">
        </div>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.5/lottie.min.js"></script>
<script>
    var animation = lottie.loadAnimation({
        container: document.getElementById('no-bookings-animation'), // the DOM element that will contain the animation
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'admin/assets/img/Animation - 1725346560143.json' // the path to the animation json
    });
</script>
</body>
</html>
