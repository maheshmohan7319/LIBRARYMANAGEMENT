<?php
include '../db_connect.php';
include 'header.php'; 
include 'nav.php';

// Initialize message
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying it
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Ensure the ID is an integer

    // Debugging: Output ID being deleted
    error_log("Attempting to delete book with ID: " . $id);

    $stmt = $conn->prepare("DELETE FROM Books WHERE book_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Book deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting book: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect after displaying the message
    echo "<script>
    window.location.href = 'book.php';
</script>";
    exit();
}

$sql = "SELECT * FROM Books";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Book Management - Admin Dashboard</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/css/ready.css">
    <link rel="stylesheet" href="assets/css/demo.css">
    <style>
        .book-thumbnail {
            width: 50px; /* Adjust size as needed */
            height: auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title">Books List</h4>

                    <!-- Display success or error message -->
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Button to create a new book -->
                    <div class="d-flex justify-content-end mb-3">
                        <a href="book_creation.php" class="btn btn-primary">Create Books</a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if ($result->num_rows > 0) : ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Book ID</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Availability</th>
                                                <th>Image</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['availability']); ?></td>
                                                    <td>
                                                        <?php if (!empty($row['image'])) : ?>
                                                            <img src="<?php echo htmlspecialchars($row['image']); ?>" class="book-thumbnail" alt="Book Image">
                                                        <?php else : ?>
                                                            No Image
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo ($row['status'] == 1) ? 'Available' : 'Not Available'; ?></td>
                                                    <td>
                                                        <a href="book.php?edit=<?php echo urlencode($row['book_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="book.php?delete=<?php echo urlencode($row['book_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p>No books found.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($_GET['edit'])) : ?>
                        <?php
                        // Fetch the book details for editing
                        $edit_id = intval($_GET['edit']); // Ensure the ID is an integer
                        $stmt = $conn->prepare("SELECT book_id, title, author, availability, image, status FROM Books WHERE book_id = ?");
                        $stmt->bind_param("i", $edit_id);
                        $stmt->execute();
                        $book_result = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        ?>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Edit Book</h5>
                                <form method="POST" action="book_update.php">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($book_result['book_id']); ?>">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book_result['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author">Author</label>
                                        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book_result['author']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="availability">Availability</label>
                                        <input type="number" class="form-control" id="availability" name="availability" value="<?php echo htmlspecialchars($book_result['availability']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                        <?php if (!empty($book_result['image'])) : ?>
                                            <img src="<?php echo htmlspecialchars($book_result['image']); ?>" class="book-thumbnail mt-2" alt="Book Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?php echo ($book_result['status'] == 1) ? 'selected' : ''; ?>>Available</option>
                                            <option value="0" <?php echo ($book_result['status'] == 0) ? 'selected' : ''; ?>>Not Available</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if there is any alert with the "show" class
            var alertElement = document.querySelector('.alert.show');
            if (alertElement) {
                // Set a timeout to remove the "show" class and add the "fade" class after 1 second
                setTimeout(function() {
                    alertElement.classList.remove('show');
                    alertElement.classList.add('fade');
                }, 1000); // 1 second delay before starting to fade out
            }
        });
    </script>
</body>
</html>
