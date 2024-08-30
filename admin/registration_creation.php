<?php
include '../db_connect.php';
include 'header.php'; 
include 'nav.php';

$message = '';
$is_edit = false;
$user_id = null;

$class_query = "SELECT class_id, class_name FROM classes";
$class_result = $conn->query($class_query);

// Check if it's an edit operation
if (isset($_GET['edit'])) {
    $user_id = intval($_GET['edit']);
    $is_edit = true;

    $user_query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
    } else {
        $message = "User not found.";
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $class_id = $_POST['class'];
    $role = $_POST['role'];
    $user_id = $_POST['user_id'];


    $check_query = "SELECT * FROM users WHERE username = ? AND user_id != ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("si", $full_name, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Full name already exists!";
    } else {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $role_value = ($role === "Student") ? 'student' : 'admin';

            if ($is_edit) {
                // Update existing user
                if (!empty($new_password)) {
                    $update_query = "UPDATE users SET username = ?, full_name = ?, password = ?, class_id = ?, role = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssisi", $username, $full_name, $hashed_password, $class_id, $role_value, $user_id);
                } else {
                    $update_query = "UPDATE users SET username = ?, full_name = ?, class_id = ?, role = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssii", $username, $full_name, $class_id, $role_value, $user_id);
                }
            } else {
                // Insert new user
                $insert_query = "INSERT INTO users (username, full_name, password, class_id, role) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sssis", $username, $full_name, $hashed_password, $class_id, $role_value);
            }

            if ($stmt->execute()) {
                $_SESSION['message'] = "User added successfully.";
                header("Location: registration.php");
            } else {
                $message = "Failed to added user!";
            }
        } else {
            $message = "Passwords do not match!";
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
    <title>User Edit</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/css/ready.css">
    <link rel="stylesheet" href="assets/css/demo.css">
    <style>
        .eye-icon {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title"><?php echo $is_edit ? 'Edit User' : 'Create User'; ?></h4>

                    <!-- Display success or error message -->
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="registration_creation.php">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_data['user_id'] ?? ''); ?>">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="class">Class</label>
                                    <select class="form-control" id="class" name="class">
                                        <?php while ($class_row = $class_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $class_row['class_id']; ?>" <?php echo (isset($user_data['class_id']) && $user_data['class_id'] == $class_row['class_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($class_row['class_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="Student" <?php echo (isset($user_data['role']) && $user_data['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                                        <option value="Admin" <?php echo (isset($user_data['role']) && $user_data['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                        <div class="input-group-append">
                                            <span class="input-group-text eye-icon" onclick="togglePasswordVisibility('password')">👁️</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password">
                                        <div class="input-group-append">
                                            <span class="input-group-text eye-icon" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
                                        </div>
                                    </div>
                                </div>
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

    <script>
        function togglePasswordVisibility(fieldId) {
            var passwordField = document.getElementById(fieldId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
    