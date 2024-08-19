<?php
include 'db_connect.php';
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
    error_log("Attempting to delete class with ID: " . $id);

    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting class: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect after displaying the message
    echo "<script>
    window.location.href = 'class.php';
</script>";
    exit();
}

$sql = "SELECT * FROM classes";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Class Management - Admin Dashboard</title>
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
                <div class="container-fluid">
                    <h4 class="page-title">Class List</h4>

                    <!-- Display success or error message -->
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Button to create a new class -->
                    <div class="d-flex justify-content-end mb-3">
                        <a href="class_creation.php" class="btn btn-primary">Create Class</a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if ($result->num_rows > 0) : ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Class ID</th>
                                                <th>Class Name</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                                    <td><?php echo ($row['status'] == 1) ? 'Active' : 'Inactive'; ?></td>
                                                    <td>
                                                        <a href="class.php?edit=<?php echo urlencode($row['class_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="class.php?delete=<?php echo urlencode($row['class_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p>No classes found.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($_GET['edit'])) : ?>
                        <?php
                        // Fetch the class details for editing
                        $edit_id = intval($_GET['edit']); // Ensure the ID is an integer
                        $stmt = $conn->prepare("SELECT class_id, class_name, status FROM classes WHERE class_id = ?");
                        $stmt->bind_param("i", $edit_id);
                        $stmt->execute();
                        $class_result = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        ?>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Edit Class</h5>
                                <form method="POST" action="class_update.php">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($class_result['class_id']); ?>">
                                    <div class="form-group">
                                        <label for="class_name">Class Name</label>
                                        <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo htmlspecialchars($class_result['class_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?php echo ($class_result['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                            <option value="0" <?php echo ($class_result['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
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
