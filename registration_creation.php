<?php
// Database connection
include 'db_connect.php';
include 'header.php'; 
include 'nav.php';


$message = '';
$is_edit = false;

$class_query = "SELECT class_id, class_name FROM classes";
$result = $conn->query($class_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $class_id = $_POST['class'];
    $role = $_POST['role'];

    // Check if registration ID already exists
    $check_query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_query);
    if ($stmt === false) {
        die('Error preparing statement: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Student RegID already exists !";
    } else {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role_value = ($role === "Student") ? 'student' : 'admin';

            $insert_query = "INSERT INTO users (username,full_name, password, class_id, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            if ($stmt === false) {
                die('Error preparing statement: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ssii", $username,$full_name, $hashed_password, $class_id, $role_value);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='registration.php';</script>";
            } else {
                echo "<script>alert('Error occurred during registration. Please try again.'); window.location.href='register_form.php';</script>";
            }
        } else {
            echo "<script>alert('Passwords do not match!'); window.location.href='register_form.php';</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>LMS - Class User</title>
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
                    <h4 class="page-title"><?php echo $is_edit ? 'Edit' : 'Create'; ?> Class</h4>
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($message)) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <form action="registration_creation.php" method="POST">
                            <div class="form-group">
                                <label for="username">Registration ID</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Registration ID" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="FullName" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                            </div>
                            <div class="form-group">
                                <label for="class">Select Class</label>
                                <select class="form-control" id="class" name="class" required>
                                    <option value="">Select a Class</option>
                                    <?php
                                    if ($result->num_rows > 0) {                            
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No classes available</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role">Select Role</label>
                                <select class="form-control" id="role" name="role" required>
								<option value="">Select a Role</option>
                                    <option value="Student">Student</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($class_id); ?>">
                                <input type="hidden" name="is_edit" value="<?php echo $is_edit ? '1' : '0'; ?>">
                                <div class="pt-1 mb-4 d-flex justify-content-center">                                    
                                  <button type="submit" class="btn btn-dark btn-lg"><?php echo $is_edit ? 'Update' : 'Create'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
