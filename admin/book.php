<?php
include '../db_connect.php';
include 'header.php'; 
include 'nav.php';

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); 
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Book deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting Book: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: book.php");
    exit();
}

$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>LMS - Books</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="../assets/css/ready.css">
    <link rel="stylesheet" href="../assets/css/demo.css">
    <style>
    .book-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
</style>
</head>
<body>
    <div class="wrapper">
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title">Books</h4>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="book_creation.php" class="btn btn-dark btn-lg">Add Book</a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if ($result->num_rows > 0) : ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sl.No</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Availability</th>
                                                <th>Image</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                              $counter = 1;  
                                            while($row = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['qty']); ?></td>
                                                    <td>
                                                        <?php if (!empty($row['image'])) : ?>
                                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" class="book-thumbnail" alt="Book Image">
                                                        <?php else : ?>
                                                            No Image
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo ($row['status'] == 'available') ? 'Available' : 'Not Available'; ?></td>
                                                    <td>
                                                        <a href="book_creation.php?id=<?php echo htmlspecialchars($row['book_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="book.php?delete=<?php echo urlencode($row['book_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php else : ?>
                            <p>No Books found.</p>
                        <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/chartist/chartist.min.js"></script>
    <script src="../assets/js/plugin/chartist/plugin/chartist-plugin-tooltip.min.js"></script>
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="../assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
    <script src="../assets/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="../assets/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/ready.min.js"></script>

    <script>
        // Hide message after 2 seconds
        $(document).ready(function() {
            var messageAlert = $('#messageAlert');
            if (messageAlert.length) {
                setTimeout(function() {
                    messageAlert.alert('close');
                }, 2000); // 2000 milliseconds = 2 seconds
            }
        });
    </script>
</body>
</html>
