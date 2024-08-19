<?php
include 'db_connect.php';
include 'header.php'; 
include 'nav.php';

// Fetch users from the database
$sql = "SELECT reg_id, role, class_id FROM users";
$result = $conn->query($sql);

// Handle deletion of a user
if (isset($_GET['delete'])) {
    $reg_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE reg_id = ?");
    $stmt->bind_param("s", $reg_id);
    if ($stmt->execute()) {
        $message = "User deleted successfully.";
    } else {
        $message = "Error deleting user.";
    }
    $stmt->close();
}

// Handle updating of user
if (isset($_POST['update'])) {
    $reg_id = $_POST['reg_id'];
    $new_role = ($_POST['role'] === 'Admin') ? 0 : 1;
    $new_class = $_POST['class'];
    
    $stmt = $conn->prepare("UPDATE users SET role = ?, class_id = ? WHERE reg_id = ?");
    $stmt->bind_param("iis", $new_role, $new_class, $reg_id);
    if ($stmt->execute()) {
        $message = "User updated successfully.";
    } else {
        $message = "Error updating user.";
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>User Management - Admin Dashboard</title>
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
                    <h4 class="page-title">User Management</h4>
                    
                    <!-- Display success or error message -->
                    <?php if (isset($message)) : ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <?php if ($result->num_rows > 0) : ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Registration ID</th>
                                            <th>Role</th>
                                            <th>Class ID</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['reg_id']); ?></td>
                                                <td><?php echo ($row['role'] === '0') ? 'Admin' : 'Student'; ?></td>
                                                <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                                                <td>
                                                    <a href="user_list.php?edit=<?php echo urlencode($row['reg_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="user_list.php?delete=<?php echo urlencode($row['reg_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <p>No users found.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($_GET['edit'])) : ?>
                        <?php
                        // Fetch the user details for editing
                        $edit_id = $_GET['edit'];
                        $stmt = $conn->prepare("SELECT reg_id, role, class_id FROM users WHERE reg_id = ?");
                        $stmt->bind_param("s", $edit_id);
                        $stmt->execute();
                        $user_result = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        ?>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Edit User</h5>
                                <form method="POST" action="user_list.php">
                                    <input type="hidden" name="reg_id" value="<?php echo htmlspecialchars($user_result['reg_id']); ?>">
                                    <div class="form-group">
                                        <label for="class">Select Class</label>
                                        <select class="form-control" id="class" name="class">
                                            <option value="1" <?php echo ($user_result['class_id'] == 1) ? 'selected' : ''; ?>>1</option>
                                            <option value="2" <?php echo ($user_result['class_id'] == 2) ? 'selected' : ''; ?>>2</option>
                                            <option value="3" <?php echo ($user_result['class_id'] == 3) ? 'selected' : ''; ?>>3</option>
                                            <option value="4" <?php echo ($user_result['class_id'] == 4) ? 'selected' : ''; ?>>4</option>
                                            <option value="5" <?php echo ($user_result['class_id'] == 5) ? 'selected' : ''; ?>>5</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Select Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="Student" <?php echo ($user_result['role'] == 1) ? 'selected' : ''; ?>>Student</option>
                                            <option value="Admin" <?php echo ($user_result['role'] == 0) ? 'selected' : ''; ?>>Admin</option>
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
</body>
</html>

<?php
$conn->close();
?>
