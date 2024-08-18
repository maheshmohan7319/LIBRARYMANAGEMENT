<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 0) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Homepage</title>
</head>
<body>
    <h1>Welcome to the Homepage</h1>
    <a href="logout.php">Logout</a>
</body>
</html>