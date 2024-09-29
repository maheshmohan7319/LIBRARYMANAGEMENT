<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">You must be logged in to reserve a book.</div>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; 

$search_term = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_term = trim($_POST['search_term']);
}


$query = "
    SELECT b.*, 
           COALESCE(SUM(CASE WHEN r.status IN ('picked', 'pending', 'approved') THEN 1 ELSE 0 END), 0) AS total_reserved,
           (b.qty - COALESCE(SUM(CASE WHEN r.status IN ('picked', 'pending', 'approved') THEN 1 ELSE 0 END), 0)) AS available_qty
    FROM books b
    LEFT JOIN reservations r ON b.book_id = r.book_id
    WHERE b.title LIKE ?
    GROUP BY b.book_id
"; 

$search_param = '%' . $search_term . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

$toast_message = '';
$toast_type = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $book_id = $_POST['book_id'];
    $reserve_from = $_POST['reserve_from'];
    $reserve_to = $_POST['reserve_to'];
    $status = 'pending'; 
    $created_at = date('Y-m-d H:i:s');

    $checkReservationQuery = "SELECT * FROM reservations WHERE book_id = ? AND user_id = ? AND status IN ('picked', 'pending')";
    $checkReservationStmt = $conn->prepare($checkReservationQuery);
    $checkReservationStmt->bind_param("ii", $book_id, $user_id);
    $checkReservationStmt->execute();
    $existingReservation = $checkReservationStmt->get_result()->fetch_assoc();

    if (!$existingReservation) {
        $reservationQuery = "INSERT INTO reservations (user_id, book_id, reserve_from, reserve_to, status, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($reservationQuery);
        $stmt->bind_param("iissss", $user_id, $book_id, $reserve_from, $reserve_to, $status, $created_at);

        if ($stmt->execute()) {
            $toast_message = 'Book reserved successfully!';
            $toast_type = 'success';
        } else {
            $toast_message = 'Failed to reserve the book. Please try again.';
            $toast_type = 'danger';
        }

        $stmt->close();
    } else {
        $toast_message = 'You have already reserved this book with status "picked" or "pending".';
        $toast_type = 'danger';
    }

    $checkReservationStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Available Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<header>
    <?php include 'header_home.php'; ?>
</header>

<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="d-block w-100" src="https://images.unsplash.com/photo-1529007196863-d07650a3f0ea?q=80&w=1470&auto=format&fit=crop" alt="First slide" style="height: 450px; object-fit: cover;">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="https://images.unsplash.com/photo-1607823477653-e2c3980acb86?q=80&w=1468&auto=format&fit=crop" alt="Second slide" style="height: 450px; object-fit: cover;">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="https://images.unsplash.com/photo-1722182877533-7378b60bf1e8?q=80&w=1407&auto=format&fit=crop" alt="Third slide" style="height: 450px; object-fit: cover;">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </a>
</div>

<div class="container-fluid mt-5 p-5">
    <h2 class="mb-4">Books</h2>

    <form method="POST" action="homepage.php" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Search by title" aria-label="Search by title">
            <button class="btn btn-dark btn-sm ml-auto" type="submit" name="search">Search</button>
        </div>
    </form>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-60 shadow-lg p-3 mb-5 bg-body rounded">
                        <?php if (!empty($row['image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" class="card-img-top" alt="Book Image" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="default-book-image.jpg" class="card-img-top" alt="Default Book Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                            <p class="card-text"><strong>Available Quantity:</strong> <?php echo htmlspecialchars($row['available_qty']); ?></p>
                            <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><small class="text-muted">Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#reserveModal" data-book-id="<?php echo $row['book_id']; ?>" data-book-title="<?php echo htmlspecialchars($row['title']); ?>">
                                Reserve Book
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No books found.</div>
    <?php endif; ?>

</div>


<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center text-bg-<?php echo $toast_type; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true" style="display: <?php echo $toast_message ? 'block' : 'none'; ?>">
        <div class="d-flex">
            <div class="toast-body">
                <?php echo $toast_message; ?>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('liveToast');
    if (toastEl.style.display === 'block') {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>


<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveModalLabel">Reserve Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="homepage.php">
                <div class="modal-body">
                    <input type="hidden" name="book_id" id="book_id" value="">

                    <!-- Conditional message for students -->
                    <?php if ($_SESSION['role'] === 'student'): ?>
                    <div class="alert alert-info" id="studentReserveInfo">
                        Only students can reserve a book for up to 15 days.
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="reserve_from" class="col-form-label">Reserve From:</label>
                        <input type="date" class="form-control" name="reserve_from" id="reserve_from" required>
                    </div>
                    <div class="mb-3">
                        <label for="reserve_to" class="col-form-label">Reserve To:</label>
                        <input type="date" class="form-control" name="reserve_to" id="reserve_to" required>
                        <small id="dateWarning" class="text-danger" style="display: none;">
                            Reservation end date must be within 15 days from the start date.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark" name="reserve">Reserve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const userRole = '<?php echo $_SESSION['role']; ?>'; 

document.addEventListener('DOMContentLoaded', function() {
    const reserveModal = document.getElementById('reserveModal');
    reserveModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const bookId = button.getAttribute('data-book-id');
        const bookTitle = button.getAttribute('data-book-title');

        const modalTitle = reserveModal.querySelector('.modal-title');
        const modalBodyInput = reserveModal.querySelector('#book_id');

        modalTitle.textContent = 'Reserve ' + bookTitle;
        modalBodyInput.value = bookId;

      
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];

        const reserveFrom = reserveModal.querySelector('#reserve_from');
        const reserveTo = reserveModal.querySelector('#reserve_to');
        const dateWarning = reserveModal.querySelector('#dateWarning');

        reserveFrom.setAttribute('min', minDate);
        reserveTo.setAttribute('min', minDate);
        reserveTo.disabled = true; 

        reserveFrom.addEventListener('change', function() {
            const fromDate = new Date(reserveFrom.value);
            reserveTo.disabled = false; 
            reserveTo.value = ''; 

            const minReserveToDate = fromDate.toISOString().split('T')[0];
            reserveTo.setAttribute('min', minReserveToDate);

            if (userRole === 'student') {
                const maxDate = new Date(fromDate);
                maxDate.setDate(maxDate.getDate() + 15);
                const maxDateStr = maxDate.toISOString().split('T')[0];
                reserveTo.setAttribute('max', maxDateStr);
            } else {
                reserveTo.removeAttribute('max');
            }

            reserveTo.addEventListener('change', function() {
                const selectedToDate = new Date(reserveTo.value);
                const maxDate = new Date(fromDate);
                maxDate.setDate(maxDate.getDate() + 15);

                if (userRole === 'student' && selectedToDate > maxDate) {
                    dateWarning.style.display = 'block';
                } else {
                    dateWarning.style.display = 'none'; 
                }
            });
        });
    });
});
</script>

</body>
</html>