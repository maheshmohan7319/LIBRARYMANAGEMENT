<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 0) {
    header("Location: index.php"); // Redirect to login if not logged in as admin
    exit();
}

// Get admin ID from session
$admin_id = $_SESSION['reg_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
	<link rel="stylesheet" href="assets/css/ready.css">
	<link rel="stylesheet" href="assets/css/demo.css">
</head>
<body>
<div class="main-header">
			<div class="logo-header">
				<a href="admin_dashboard.php" class="logo">
				 Dashboard
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
			</div>
			<nav class="navbar navbar-header navbar-expand-lg">
				<div class="container-fluid">			
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
				
						<li class="nav-item dropdown">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false"><span ><?php echo $admin_id; ?></span></span> </a>
							<ul class="dropdown-menu dropdown-user">
							<li>
								<div class="user-box">
									<div class="u-text">
										<h4><?php echo $admin_id; ?></h4> <!-- Display the admin ID -->
										<p class="text-muted">Admin</p>
										<a href="profile.html" class="btn btn-rounded btn-danger btn-sm">View Profile</a>
									</div>
								</div>
							</li>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
							</ul>
								<!-- /.dropdown-user -->
							</li>
						</ul>
					</div>
				</nav>
			</div>
</body>
</html>