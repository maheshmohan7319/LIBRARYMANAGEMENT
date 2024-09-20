<?php
include '../db_connect.php'; 
include 'header.php'; 
include 'nav.php';

$message = '';
$editMode = false;
$book = null;

// Check if we're in edit mode
if (isset($_GET['id'])) {
    $editMode = true;
    $bookId = intval($_GET['id']);
    
    // Fetch the book data
    $stmt = $conn->prepare("SELECT * FROM Books WHERE book_id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        $message = "Book not found.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = $_POST['author'];
    $qty = $_POST['availability'];
    $status = $_POST['status'];

    // Check for duplicate titles
    $check_query = "SELECT * FROM Books WHERE LOWER(title) = LOWER(?)";
    if ($editMode) {
        $check_query .= " AND book_id != ?";
    }
    $stmt = $conn->prepare($check_query);
    if ($editMode) {
        $stmt->bind_param("si", $title, $bookId);
    } else {
        $stmt->bind_param("s", $title);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "A book with this title already exists. Please choose a different title.";
    } else {
        $target_dir = "../assets/uploads/";
        $uploadOk = 1;
        $uniqueImageName = '';

        // Handle file upload if a new image is provided
        if ($_FILES["image"]["size"] > 0) {
            $imageName = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
            $imageExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $uniqueImageName = $imageName . '_' . time() . '_' . rand(1000, 9999) . '.' . $imageExtension;
            $target_file = $target_dir . $uniqueImageName;

            // Validate file upload (add your validation checks here)
            // Example: check file type, size, etc.

            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
            } else {
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $message = "Sorry, there was an error uploading your file.";
                    $uploadOk = 0;
                }
            }
        } elseif ($editMode) {
            // If no new image is uploaded in edit mode, keep the existing image
            $uniqueImageName = $book['image'];
        }

        if ($uploadOk != 0) {
            if ($editMode) {
                // Update existing book
                $update_query = "UPDATE Books SET title = ?, author = ?, qty = ?, image = ?, status = ? WHERE book_id = ?";
                if ($stmt = $conn->prepare($update_query)) {
                    $stmt->bind_param("ssissi", $title, $author, $qty, $uniqueImageName, $status, $bookId);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Book updated successfully.";
                        header("Location: book.php");
                        exit();
                    } else {
                        $message = "Failed to update book! Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                // Insert new book
                $insert_query = "INSERT INTO Books (title, author, qty, image, status) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = $conn->prepare($insert_query)) {
                    $stmt->bind_param("ssiss", $title, $author, $qty, $uniqueImageName, $status);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Book added successfully.";
                        header("Location: book.php");
                        exit();
                    } else {
                        $message = "Failed to add book! Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?php echo $editMode ? 'Edit' : 'Create'; ?> Book</title>
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
                    <h4 class="page-title"><?php echo $editMode ? 'Edit' : 'Create'; ?> Book</h4>

                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="book_creation.php<?php echo $editMode ? '?id=' . $bookId : ''; ?>" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $editMode ? htmlspecialchars($book['title']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="author">Author</label>
                                    <input type="text" class="form-control" id="author" name="author" value="<?php echo $editMode ? htmlspecialchars($book['author']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="availability">Availability</label>
                                    <input type="number" class="form-control" id="availability" name="availability" value="<?php echo $editMode ? htmlspecialchars($book['qty']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="image">Book Cover Image</label>
                                    <input type="file" class="form-control" id="image" name="image" <?php echo $editMode ? '' : 'required'; ?>>
                                    <?php if ($editMode && !empty($book['image'])) : ?>
                                        <img src="../assets/uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="Current Image" style="max-width: 100px; margin-top: 10px;">
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="available" <?php echo ($editMode && $book['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="reserved" <?php echo ($editMode && $book['status'] == 'reserved') ? 'selected' : ''; ?>>Reserved</option>
                                        <option value="borrowed" <?php echo ($editMode && $book['status'] == 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>                      
                                    </select>
                                </div>
                                <div class="pt-1 mb-4 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-dark btn-lg"><?php echo $editMode ? 'Update' : 'Create'; ?></button>
                                </div>
                            </form>
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
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/ready.min.js"></script>
</body>
</html>
