<?php
include '../db_connect.php';

$user_count_query = "SELECT COUNT(*) AS user_count FROM users";
$user_count_result = $conn->query($user_count_query);
$user_count = $user_count_result->fetch_assoc()['user_count'];

$class_count_query = "SELECT COUNT(*) AS class_count FROM classes";
$class_count_result = $conn->query($class_count_query);
$class_count = $class_count_result->fetch_assoc()['class_count'];

$book_count_query = "SELECT COUNT(*) AS book_count FROM books";
$book_count_result = $conn->query($book_count_query);
$book_count = $book_count_result->fetch_assoc()['book_count'];

$reservation_count_query = "SELECT COUNT(*) AS reservation_count FROM reservations";
$reservation_count_result = $conn->query($reservation_count_query);
$reservation_count = $reservation_count_result->fetch_assoc()['reservation_count'];


$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="scrollbar-inner sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item <?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">
                <a href="admin_dashboard.php">
                    <i class="la la-home"></i>
                    <p>Home</p>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'registration.php' ? 'active' : ''; ?>">
                <a href="registration.php">
                    <i class="la la-user"></i>
                    <p>Registration</p>
                    <span class="badge badge-count"><?php echo $user_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'class.php' ? 'active' : ''; ?>">
                <a href="class.php">
                    <i class="la la-comment"></i>
                    <p>Class</p>
                    <span class="badge badge-count"><?php echo $class_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'book.php' ? 'active' : ''; ?>">
                <a href="book.php">
                    <i class="la la-book"></i>
                    <p>Books</p>
                    <span class="badge badge-count"><?php echo $book_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'reservation.php' ? 'active' : ''; ?>">
                <a href="reservation.php">
                    <i class="la la-calendar"></i>
                    <p>Reservation</p>
                    <span class="badge badge-count"><?php echo $reservation_count; ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>
