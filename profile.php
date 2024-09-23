<?php
include 'db_connect.php';
session_start();
$user_id = $_SESSION['user_id'];

$query = "SELECT username, full_name, role, password, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if current password is correct
    if (!password_verify($current_password, $user['password'])) {
        $error = "Incorrect current password!";
    }
    // Check if new passwords match
    elseif ($new_password !== $confirm_password) {
        $error = "New password and confirmation do not match!";
    } else {
        // If all checks pass, hash the new password and update it in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows > 0) {
            $message = "Password updated successfully!";
        } else {
            $error = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <style>
        body {
           
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .profile-card {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            color: #333;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            animation: fadeInUp 1s ease;
        }
        h2, h4 {
            text-align: center;
            font-weight: bold;
        }
        .profile-card p {
            font-size: 16px;
        }
        .form-group input {
            border: 2px solid #2980b9;
            border-radius: 8px;
            padding: 10px;
            background-color: #f4f4f9;
        }
        .form-group label {
            font-weight: bold;
            color: #2980b9;
        }
        .btn-update {
            background-color: #2980b9;
            color: #fff;
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
        }
        .btn-update:hover {
            background-color: #3498db;
            transition: 0.3s;
        }
        #changePasswordForm {
            display: none;
            margin-top: 20px;
        }
        .toggle-icon {
            cursor: pointer;
            font-size: 24px;
            color: #2980b9;
            transition: transform 0.3s ease;
        }
        .toggle-icon:hover {
            transform: rotate(90deg);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<header>
    <?php include 'header_home.php'; ?>
</header>
    <div class="container">
        <div class="profile-card animate__animated animate__fadeInUp">
            <h2>User Profile</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Joined:</strong> <?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>

            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mt-4">Change Password <span class="toggle-icon" id="toggleIcon">
                    <i class="fas fa-lock"></i>
                </span></h4>
            </div>

            <div id="changePasswordForm">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-update">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast" id="successToast" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body text-success">
            <?php echo $message; ?>
        </div>
    </div>

    <div class="toast" id="errorToast" style="position: fixed; bottom: 80px; right: 20px; z-index: 1050;" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body text-danger">
            <?php echo $error; ?>
        </div>
    </div>

    <script>
        document.getElementById('toggleIcon').addEventListener('click', function() {
            var form = document.getElementById('changePasswordForm');
            form.style.display = form.style.display === "none" ? "block" : "none";
        });

        // Show success toast if there's a success message
        <?php if (!empty($message)): ?>
            var successToast = document.getElementById('successToast');
            var successToastInstance = new bootstrap.Toast(successToast);
            successToastInstance.show();
        <?php endif; ?>

        // Show error toast if there's an error message
        <?php if (!empty($error)): ?>
            var errorToast = document.getElementById('errorToast');
            var errorToastInstance = new bootstrap.Toast(errorToast);
            errorToastInstance.show();
        <?php endif; ?>
    </script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
