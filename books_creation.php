<?php
include 'db_connect.php';
include 'header.php'; 
include 'nav.php';

// Handle form submission
if (isset($_POST['register_book'])) {
    $book_name = $_POST['book_name'];
    $book_description = $_POST['book_description'];
    $book_qty = $_POST['book_qty'];
    $book_createdAt = date('Y-m-d H:i:s');
    $book_createdBy = $_SESSION['user_id']; // Assuming you store user ID in the session
    $book_updatedAt = $book_createdAt;
    $book_updatedBy = $book_createdBy;
    $book_author = $_POST['book_author'];
    $book_status = $_POST['book_status'];

    $stmt = $conn->prepare("INSERT INTO books (book_name, book_description, book_qty, book_createdAt, book_createdBy, book_updatedAt, book_updatedBy, book_author, book_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssssi", $book_name, $book_description, $book_qty, $book_createdAt, $book_createdBy, $book_updatedAt, $book_updatedBy, $book_author, $book_status);

    if ($stmt->execute()) {
        $message = "Book registered successfully.";
    } else {
        $message = "Error registering book: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Book Registration - Admin Dashboard</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/css/ready.css">
    <link rel="stylesheet" href="assets/css/demo.css">
</head>
<body>
    <div class="wrapper">
        <div class="main-panel">
            <div class="content">
                <div class="container">
                    <h4 class="page-title">Book Registration</h4>
                    
                    <!-- Display success or error message -->
                    <?php if (isset($message)) : ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="book_registration.php">
                                <div class="form-group">
                                    <label for="book_name">Book Name</label>
                                    <input type="text" class="form-control" id="book_name" name="book_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="book_description">Book Description</label>
                                    <textarea class="form-control" id="book_description" name="book_description" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="book_qty">Quantity</label>
                                    <input type="number" class="form-control" id="book_qty" name="book_qty" required>
                                </div>
                                <div class="form-group">
                                    <label for="book_author">Author</label>
                                    <input type="text" class="form-control" id="book_author" name="book_author" required>
                                </div>
                                <div class="form-group">
                                    <label for="book_status">Status</label>
                                    <select class="form-control" id="book_status" name="book_status">
                                        <option value="1">Available</option>
                                        <option value="0">Not Available</option>
                                    </select>
                                </div>
                                <button type="submit" name="register_book" class="btn btn-primary">Register Book</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary scripts -->
    <script src="assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugin/chartist/chartist.min.js"></script>
    <script src="assets/js/plugin/chartist/plugin/chartist-plugin-tooltip.min.js"></script>
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
    <script src="assets/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="assets/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="assets/js/ready.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
