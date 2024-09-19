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

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting Student: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: registration.php");
    exit();
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>LMS - Student List</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="../assets/css/ready.css">
    <link rel="stylesheet" href="../assets/css/demo.css">
</head>
<body>
    <div class="wrapper">
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title">Student List</h4>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="registration_creation.php" class="btn btn-dark btn-lg">Create Student</a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($message)) : ?>
                                <div id="messageAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                       
                            <?php if ($result->num_rows > 0) : ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl.No</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Class</th> 
                                            <th>Role</th>                                  
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = 1; 
                                        while($row = $result->fetch_assoc()) : ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                                <td>
                                                    <a href="registration_creation.php?id=<?php echo htmlspecialchars($row['user_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="registration.php?delete=<?php echo htmlspecialchars($row['user_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
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
