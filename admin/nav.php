<?php
include '../db_connect.php';
ob_start();
session_start();
$user_id = $_SESSION['user_id'];

$student_count_query = "SELECT COUNT(*) AS student_count FROM users WHERE user_id != ? AND role = 'student'";
$stmt = $conn->prepare($student_count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_count_result = $stmt->get_result();
$student_count = $student_count_result->fetch_assoc()['student_count'];
$stmt->close();

$teacher_count_query = "SELECT COUNT(*) AS teacher_count FROM users WHERE user_id != ? AND role = 'teacher'";
$stmt = $conn->prepare($teacher_count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$teacher_count_result = $stmt->get_result();
$teacher_count = $teacher_count_result->fetch_assoc()['teacher_count'];
$stmt->close();

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
    <a href="admin_dashboard.php" style="display: flex; align-items: center;">
        <i class="la la-home"></i>
        <p style="font-size: 18px; margin: 0 10px;">Home</p>
    </a>
</li>
            <li class="nav-item <?php echo $current_page == 'student.php' ? 'active' : ''; ?>">
                <a href="student.php" style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="la la-user"></i>
                    <p style="font-size: 18px; margin: 0;">Students</p>
                    <span class="badge badge-count" style="font-size: 18px;"><?php echo $student_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'teacher.php' ? 'active' : ''; ?>">
                <a href="teacher.php" style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="la la-user"></i>
                    <p style="font-size: 18px; margin: 0;">Teachers</p>
                    <span class="badge badge-count" style="font-size: 18px;"><?php echo $teacher_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'class.php' ? 'active' : ''; ?>">
                <a href="class.php" style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="la la-comment"></i>
                    <p style="font-size: 18px; margin: 0;">Class</p>
                    <span class="badge badge-count" style="font-size: 18px;"><?php echo $class_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'book.php' ? 'active' : ''; ?>">
                <a href="book.php" style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="la la-book"></i>
                    <p style="font-size: 18px; margin: 0;">Books</p>
                    <span class="badge badge-count" style="font-size: 18px;"><?php echo $book_count; ?></span>
                </a>
            </li>
            <li class="nav-item <?php echo $current_page == 'reservation.php' ? 'active' : ''; ?>">
                <a href="reservation.php" style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="la la-calendar"></i>
                    <p style="font-size: 18px; margin: 0;">Reservation</p>
                    <span class="badge badge-count" style="font-size: 18px;"><?php echo $reservation_count; ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>