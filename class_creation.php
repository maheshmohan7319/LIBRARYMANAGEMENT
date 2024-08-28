<?php
include 'db_connect.php';
include 'header.php'; 
include 'nav.php';

$message = '';
$class_name = '';
$class_id = null;
$is_edit = false;

// Check if an ID is provided for editing an existing class
if (isset($_GET['id'])) {
    $class_id = intval($_GET['id']);
    $is_edit = true;

    // Fetch existing class data for editing
    $stmt = $conn->prepare("SELECT class_name FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->bind_result($class_name);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $class_id = $_POST['class_id'] ?? null;
    $is_edit = isset($_POST['is_edit']) && $_POST['is_edit'] === '1';

    // Check for existing class with the same name
    $stmt = $conn->prepare("SELECT COUNT(*) FROM classes WHERE class_name = ? AND (class_id != ? OR ? IS NULL)");
    $stmt->bind_param("ssi", $class_name, $class_id, $class_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $message = "Class name already exists. Please choose a different name.";
    } else {
        if ($is_edit) {
            // Update existing class
            $stmt = $conn->prepare("UPDATE classes SET class_name = ? WHERE class_id = ?");
            $stmt->bind_param("si", $class_name, $class_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Class updated successfully.";
                header("Location: class.php");
                exit();
            } else {
                $message = "Error updating class: " . $conn->error;
            }

            $stmt->close();
        } else {
            // Insert new class
            $stmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
            $stmt->bind_param("s", $class_name);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Class added successfully.";
                header("Location: class.php");
                exit();
            } else {
                $message = "Error adding class: " . $conn->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>LMS - Class Creation</title>
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
                            <form action="class_creation.php" method="POST">
                                <div class="form-group">
                                    <label for="class_name">Class Name</label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>" required>
                                </div>
                                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
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
