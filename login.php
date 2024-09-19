<?php
include 'db_connect.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

  
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();


        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id']; 
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            if ($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit();
            } else {
                header("Location: homepage.php");
                exit();
            }
        } else {
            $error = "Password Incorrect!";
        }
    } else {
        $error = "User not found!";
    }

  
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <section class="vh-100" style="background-image: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bGlicmFyeXxlbnwwfHwwfHx8MA%3D%3D'); background-size: cover; background-position: center;">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col col-xl-6">
            <div class="card" style="border-radius: 1rem;">      
                  <div class="card-body p-4 p-lg-5 text-black">
                    <form method="POST" action="index.php">
                    <div class="d-flex align-items-center mb-3 pb-1">
                      <i class="fas fa-star fa-2x me-3" style="color: #ff6219;"></i> 
                      <img src="assets/img/logo.png" alt="Logo" style="height: 40px;">
                    </div>

                      <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your account</h5>

                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example17">Registration ID</label>
                        <input type="text" id="form2Example17" name="email" class="form-control form-control-lg" required />
                      </div>

                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example27">Password</label>
                        <input type="password" id="form2Example27" name="password" class="form-control form-control-lg" required />
                      </div>

                      <?php if (isset($error)) : ?>
                        <p style="color: red;"><?php echo $error; ?></p>
                      <?php endif; ?>

                      <div class="pt-1 mb-4 d-flex justify-content-center">
                        <button class="btn btn-dark btn-lg" type="submit" style="width: 60%;">Login</button>
                      </div>

                    </form>

                  </div>
                </div>
            </div>        
        </div>
      </div>
    </section>
</body>
</html>