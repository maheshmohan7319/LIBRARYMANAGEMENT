<?php
include 'db_connect.php'; // Include the database connection

// Fetch all books from the database
$query = "SELECT * FROM books";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Available Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .no-items-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px; /* Adjust as needed */
        }

        .no-items-message {
            font-size: 1.25rem;
            color: #023C6E;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #f0e895;">
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
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card" style="width: 100%;">
                        <!-- Display book image if available -->
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Book Image" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="default-book-image.jpg" class="card-img-top" alt="Default Book Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text">Author: <?php echo htmlspecialchars($row['author']); ?></p>
                            <p class="card-text">Quantity: <?php echo htmlspecialchars($row['qty']); ?></p>
                            <p class="card-text">Status: <?php echo htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><small class="text-muted">Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-items-container">
            <img src="https://w7.pngwing.com/pngs/277/965/png-transparent-empty-cart-illustration.png" alt="No Items Animation" style="width: 250px; height: auto;"> <!-- Replace with your animated PNG -->
            <p class="no-items-message">No books available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<div><section class="">
  <!-- Footer -->
  <footer class="bg-body-tertiary text-center" style="background-color: #f0e895;">
    <!-- Grid container -->
    <div class="container p-4">
      <!--Grid row-->
      <div class="row">
        <!--Grid column-->
        <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
          <h5 class="text-uppercase text-white">Footer Content</h5>

          <p class="text-white">
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iste atque ea quis
            molestias. Fugiat pariatur maxime quis culpa corporis vitae repudiandae
            aliquam voluptatem veniam, est atque cumque eum delectus sint!
          </p>
        </div>
        <!--Grid column-->

        <!--Grid column-->
        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase text-white">Links</h5>

          <ul class="list-unstyled mb-0">
            <li>
              <a href="#!" class="text-white">Link 1</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 2</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 3</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 4</a>
            </li>
          </ul>
        </div>
        <!--Grid column-->

        <!--Grid column-->
        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase mb-0 text-white">Links</h5>

          <ul class="list-unstyled">
            <li>
              <a href="#!" class="text-white">Link 1</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 2</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 3</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 4</a>
            </li>
          </ul>
        </div>
        <!--Grid column-->
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
</body>
</html>
