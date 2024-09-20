<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="../assets/css/ready.css">
    <link rel="stylesheet" href="../assets/css/demo.css">
</head>
<body>
<div class="main-header -dark bg-dark">
    <div class="logo-header">
        <a href="admin_dashboard.php" class="logo"  style="color: white;">DASHBOARD</a>
    </div>
    <nav class="navbar navbar-header navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">            
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li class="nav-item dropdown">
                <a class="dropdown-toggle profile-pic text-white" data-toggle="dropdown" href="#" aria-expanded="false">
                    <span class="h5"  style="color: white;"><?php echo $admin_id; ?></span> 
                </a>

                    <ul class="dropdown-menu dropdown-user">
                    <a class="dropdown-item" href="../logout.php"><i class="fa fa-power-off"></i> View Profile</a>    
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../logout.php"><i class="fa fa-power-off"></i> Logout</a>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
