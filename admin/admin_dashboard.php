<?php
include 'header.php';
include 'nav.php';


$rp_count_query = "SELECT COUNT(*) AS rp_count FROM reservations WHERE status = 'pending'";
$rp_count_result = $conn->query($rp_count_query);
$rp_count = $rp_count_result->fetch_assoc()['rp_count'];

$rc_count_query = "SELECT COUNT(*) AS rc_count FROM reservations WHERE status = 'completed'";
$rc_count_result = $conn->query($rc_count_query);
$rc_count = $rc_count_result->fetch_assoc()['rc_count'];

$rca_count_query = "SELECT COUNT(*) AS rca_count FROM reservations WHERE status = 'cancelled'";
$rca_count_result = $conn->query($rca_count_query);
$rca_count = $rca_count_result->fetch_assoc()['rca_count'];


$rcp_count_query = "SELECT COUNT(*) AS rc_count FROM reservations WHERE status = 'picked'";
$rcp_count_result = $conn->query($rcp_count_query);
$rcp_count = $rcp_count_result->fetch_assoc()['rc_count'];


?>



<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="row">            
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-newspaper-o"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Pending Bookings</p>
                                        <h4 class="card-title"><?php echo $rp_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5  py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Completed Bookings</p>
                                        <h4 class="card-title"><?php echo $rc_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-users"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Cancelled Bookings</p>
                                        <h4 class="card-title"><?php echo $rca_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5" >
                                    <div class="icon-big text-center">
                                        <i class="la la-book"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Pickup Bookings</p>
                                        <h4 class="card-title"><?php echo $rcp_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="row">
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-users"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Students</p>
                                        <h4 class="card-title"><?php echo $user_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5" >
                                    <div class="icon-big text-center">
                                        <i class="la la-book"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Books</p>
                                        <h4 class="card-title"><?php echo $book_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-newspaper-o"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Pending Bookings</p>
                                        <h4 class="card-title"><?php echo $rp_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-white shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5  py-5">
                                    <div class="icon-big text-center">
                                        <i class="la la-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <div class="numbers">
                                        <p class="card-category">Completed Bookings</p>
                                        <h4 class="card-title"><?php echo $rc_count; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
<script src="../assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="../assets/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
<script src="../assets/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
<script src="../assets/js/plugin/chart-circle/circles.min.js"></script>
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/ready.min.js"></script>
<script src="../assets/js/demo.js"></script>
</body>
</html>