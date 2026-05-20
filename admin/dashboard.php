<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
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

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden">
        
        <h3 class="mb-4 text-center">ANALYTICS DASHBOARD</h3>

        <div class="row justify-content-center mb-4">
          <div class="col-lg-11 d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 fw-bold text-secondary">Booking & Revenue Statistics</h5>
            <select class="form-select shadow-none w-25" onchange="get_analytics(this.value)">
              <option value="1">Past 30 Days</option>
              <option value="2">Past 90 Days</option>
              <option value="3">Past 1 Year</option>
              <option value="4">All Time</option>
            </select>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-11 px-0">
            <div class="row text-center mb-4">
              
              <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary">
                  <h6 class="text-secondary fw-bold small">TOTAL BOOKINGS</h6>
                  <h1 class="fw-bold mt-2 mb-0" id="total_bookings">0</h1>
                </div>
              </div>

              <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm p-3 border-start border-4 border-success">
                  <h6 class="text-secondary fw-bold small">TOTAL REVENUE</h6>
                  <h1 class="fw-bold mt-2 mb-0" id="total_revenue">₹0</h1>
                </div>
              </div>

              <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger">
                  <h6 class="text-secondary fw-bold small">CANCELLED BOOKINGS</h6>
                  <h1 class="fw-bold mt-2 mb-0" id="cancelled_bookings">0</h1>
                </div>
              </div>

              <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning">
                  <h6 class="text-secondary fw-bold small">REFUND DENIED / FAILED</h6>
                  <h1 class="fw-bold mt-2 mb-0" id="failed_bookings">0</h1>
                </div>
              </div>

            </div>

            <h5 class="text-secondary fw-bold mb-3 mt-2 text-center">System Registration Summary</h5>
            <div class="row text-center">
              
              <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 bg-dark text-white">
                  <h6>User Queries</h6>
                  <h2 class="fw-bold mt-2 mb-0"><?php echo mysqli_num_rows(selectAll('user_queries')); ?></h2>
                </div>
              </div>

              <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 bg-dark text-white">
                  <h6>Reviews & Ratings</h6>
                  <h2 class="fw-bold mt-2 mb-0">0</h2>
                </div>
              </div>

              <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 bg-dark text-white">
                  <h6>Registered Clients</h6>
                  <h2 class="fw-bold mt-2 mb-0"><?php echo mysqli_num_rows(selectAll('user_cred')); ?></h2>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/script.php'); ?>

  <script>
    function get_analytics(period=1) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/dashboard_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        document.getElementById('total_bookings').innerText = data.total_bookings;
        document.getElementById('total_revenue').innerText = '₹' + data.total_revenue;
        document.getElementById('cancelled_bookings').innerText = data.cancelled_bookings;
        document.getElementById('failed_bookings').innerText = data.failed_bookings;
      }
      xhr.send('get_analytics=&period='+period);
    }

    window.onload = function() {
      get_analytics();
    }
  </script>

</body>
</html>