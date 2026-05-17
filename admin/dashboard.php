<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
  session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | Dashboard</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="mb-0">DASHBOARD</h3>
            <h6 class="badge bg-danger py-2 px-3 rounded shadow-sm">Shutdown Mode is Active!</h6>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <a href="new_bookings.php" class="text-decoration-none">
                    <div class="card text-center text-success p-3 shadow-sm border-0">
                        <h6>New Bookings</h6>
                        <h1 class="mt-2 mb-0">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="refund_bookings.php" class="text-decoration-none">
                    <div class="card text-center text-warning p-3 shadow-sm border-0">
                        <h6>Refund Bookings</h6>
                        <h1 class="mt-2 mb-0">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="user_queries.php" class="text-decoration-none">
                    <div class="card text-center text-info p-3 shadow-sm border-0">
                        <h6>User Queries</h6>
                        <h1 class="mt-2 mb-0">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="rate_review.php" class="text-decoration-none">
                    <div class="card text-center text-dark p-3 shadow-sm border-0">
                        <h6>Rating & Review</h6>
                        <h1 class="mt-2 mb-0">0</h1>
                    </div>
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5>Booking Analytics</h5>
            <select class="form-select shadow-none bg-light w-auto border-0">
                <option value="1">Past 30 Days</option>
                <option value="2">Past 90 Days</option>
                <option value="3">Past 1 Year</option>
                <option value="4">All Time</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 mb-4">
                <div class="card text-center text-primary p-3 shadow-sm border-0">
                    <h6>Total Bookings</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                    <h4 class="mt-2">₹0</h4>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-success p-3 shadow-sm border-0">
                    <h6>Active Bookings</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                    <h4 class="mt-2">₹0</h4>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-danger p-3 shadow-sm border-0">
                    <h6>Cancelled Bookings</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                    <h4 class="mt-2">₹0</h4>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5>User, Queries, Reviews Analytics</h5>
            <select class="form-select shadow-none bg-light w-auto border-0">
                <option value="1">Past 30 Days</option>
                <option value="2">Past 90 Days</option>
                <option value="3">Past 1 Year</option>
                <option value="4">All Time</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 mb-4">
                <div class="card text-center text-success p-3 shadow-sm border-0">
                    <h6>New Registration</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-primary p-3 shadow-sm border-0">
                    <h6>Queries</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-dark p-3 shadow-sm border-0">
                    <h6>Reviews</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
        </div>

        <h5 class="mb-3">Users</h5>
        <div class="row mb-3">
            <div class="col-md-3 mb-4">
                <div class="card text-center text-info p-3 shadow-sm border-0">
                    <h6>Total Users</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-success p-3 shadow-sm border-0">
                    <h6>Active Users</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-warning p-3 shadow-sm border-0">
                    <h6>Inactive Users</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-danger p-3 shadow-sm border-0">
                    <h6>Unverified Users</h6>
                    <h1 class="mt-2 mb-0">0</h1>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
  
  <?php require('inc/script.php'); ?>
</body>
</html>