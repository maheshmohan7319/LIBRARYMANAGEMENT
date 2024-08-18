<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 0) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: homepage.php");
        exit();
    }
}

include 'login.php';
?>