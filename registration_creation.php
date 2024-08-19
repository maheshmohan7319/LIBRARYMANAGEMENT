<?php
include 'db_connect.php';
include 'header.php'; 
include 'nav.php';

// Fetch all classes from the database
$class_query = "SELECT class_id, class_name FROM classes";
$result = $conn->query($class_query);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_id = $_POST['reg_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $class_id = $_POST['class'];
    $role = $_POST['role'];

    // Check if reg_id already exists
    $check_query = "SELECT * FROM users WHERE reg_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $reg_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Registration ID already exists!'); window.location.href='register_form.php';</script>";
    } else {
        if ($password === $confirm_password) {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Determine role value (0 for Student, 1 for Admin)
            $role_value = ($role === "Student") ? 1 : 0;

            // Insert the new user record into the database
            $insert_query = "INSERT INTO users (reg_id, password, class_id, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssii", $reg_id, $hashed_password, $class_id, $role_value);

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
    <title>User Registration - Ready Bootstrap Dashboard</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/css/ready.css">
    <link rel="stylesheet" href="assets/css/demo.css">
</head>

<body>
    <br><br><br><br>
    <div class="wrapper">
        <div class="main-panel d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="page-title">User Registration</h4>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="reg_id">Registration ID</label>
                                <input type="text" class="form-control" id="reg_id" name="reg_id" placeholder="Registration ID" required>
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
                                        // Output data of each row
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
                            <div class="card-action text-center">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="reset" class="btn btn-danger">Cancel</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdatePro" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h6 class="modal-title"><i class="la la-frown-o"></i> Under Development</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Currently the pro version of the <b>Ready Dashboard</b> Bootstrap is in progress development</p>
                    <p>
                        <b>We'll let you know when it's done</b>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
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
    $(function() {
        $("#slider").slider({
            range: "min",
            max: 100,
            value: 40,
        });
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 500,
            values: [75, 300]
        });
    });
</script>

</html>
