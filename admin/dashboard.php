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
  <style>
    .card-pulse {
        transition: transform 0.22s ease-in-out, box-shadow 0.22s ease-in-out !important;
    }
    .card-pulse:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
    .text-light-50 {
        color: rgba(255, 255, 255, 0.65) !important;
    }
  </style>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="mb-0 fw-bold h-font">DASHBOARD</h3>
            <?php
              $setting_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
              $setting_res = mysqli_fetch_assoc(select($setting_q, [1], 'i'));
              if($setting_res['shutdown']){
                echo "<h6 class='badge bg-danger py-2 px-3 rounded shadow-sm mb-0'><i class='bi bi-exclamation-triangle-fill me-1'></i> Shutdown Mode is Active!</h6>";
              }
            ?>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <a href="new_bookings.php" class="text-decoration-none">
                    <div class="card text-center text-success p-3 shadow-sm border-top border-4 border-success h-100 card-pulse bg-white">
                        <h6 class="fw-bold text-uppercase small text-secondary mb-2">New Bookings</h6>
                        <h1 class="mt-2 mb-0 fw-bold" id="new_bookings_count">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="refund_bookings.php" class="text-decoration-none">
                    <div class="card text-center text-warning p-3 shadow-sm border-top border-4 border-warning h-100 card-pulse bg-white">
                        <h6 class="fw-bold text-uppercase small text-secondary mb-2">Refund Bookings</h6>
                        <h1 class="mt-2 mb-0 fw-bold" id="refund_bookings_count">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="user_queries.php" class="text-decoration-none">
                    <div class="card text-center text-info p-3 shadow-sm border-top border-4 border-info h-100 card-pulse bg-white">
                        <h6 class="fw-bold text-uppercase small text-secondary mb-2">User Queries</h6>
                        <h1 class="mt-2 mb-0 fw-bold" id="user_queries_count">0</h1>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="rate_review.php" class="text-decoration-none">
                    <div class="card text-center text-dark p-3 shadow-sm border-top border-4 border-dark h-100 card-pulse bg-white">
                        <h6 class="fw-bold text-uppercase small text-secondary mb-2">Rating & Review</h6>
                        <h1 class="mt-2 mb-0 fw-bold" id="reviews_count">0</h1>
                    </div>
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3 mt-2">
            <h5 class="fw-bold text-secondary"><i class="bi bi-graph-up-arrow me-2"></i>Booking Analytics</h5>
            <select class="form-select shadow-none bg-white w-auto border-2 fw-bold text-dark" onchange="get_analytics(this.value)">
                <option value="1">Past 30 Days</option>
                <option value="2">Past 90 Days</option>
                <option value="3">Past 1 Year</option>
                <option value="4">All Time</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 border-top border-4 border-primary bg-white h-100">
                    <h6 class="text-secondary fw-bold small text-uppercase">Total Bookings</h6>
                    <h1 class="fw-bold mt-2 mb-0 text-dark" id="total_bookings">0</h1>
                    <h4 class="fw-bold mt-2 text-primary" id="total_amt">₹0</h4>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 border-top border-4 border-success bg-white h-100">
                    <h6 class="text-secondary fw-bold small text-uppercase">Active Bookings</h6>
                    <h1 class="fw-bold mt-2 mb-0 text-success" id="active_bookings">0</h1>
                    <h4 class="fw-bold mt-2 text-success" id="active_amt">₹0</h4>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3 border-top border-4 border-danger bg-white h-100">
                    <h6 class="text-secondary fw-bold small text-uppercase">Cancelled Bookings</h6>
                    <h1 class="fw-bold mt-2 mb-0 text-danger" id="cancelled_bookings">0</h1>
                    <h4 class="fw-bold mt-2 text-danger" id="cancelled_amt">₹0</h4>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
            <h5 class="fw-bold text-secondary"><i class="bi bi-pie-chart me-2"></i>User, Queries, Reviews Analytics</h5>
            <select class="form-select shadow-none bg-white w-auto border-2 fw-bold text-dark" onchange="get_analytics(document.querySelector('select').value)">
                <option value="1">Past 30 Days</option>
                <option value="2">Past 90 Days</option>
                <option value="3">Past 1 Year</option>
                <option value="4">All Time</option>
            </select>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-4 bg-dark text-white text-center h-100 card-pulse">
                    <div class="fs-1 mb-2 text-success"><i class="bi bi-person-plus-fill"></i></div>
                    <h6 class="text-light-50 small text-uppercase fw-bold">New Registration</h6>
                    <h1 class="mt-2 mb-0 fw-bold text-success" id="new_reg_count">0</h1>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-4 bg-dark text-white text-center h-100 card-pulse">
                    <div class="fs-1 mb-2 text-primary"><i class="bi bi-chat-left-text-fill"></i></div>
                    <h6 class="text-light-50 small text-uppercase fw-bold">Queries</h6>
                    <h1 class="mt-2 mb-0 fw-bold text-primary" id="queries_count">0</h1>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-4 bg-dark text-white text-center h-100 card-pulse">
                    <div class="fs-1 mb-2 text-warning"><i class="bi bi-chat-square-heart-fill"></i></div>
                    <h6 class="text-light-50 small text-uppercase fw-bold">Reviews</h6>
                    <h1 class="mt-2 mb-0 fw-bold text-warning" id="system_reviews_count">0</h1>
                </div>
            </div>
        </div>

        <h5 class="mb-3 fw-bold text-secondary mt-2"><i class="bi bi-people-fill me-2"></i>Users Overview</h5>
        <div class="row mb-3">
            <div class="col-md-3 mb-4">
                <div class="card text-center text-info p-3 shadow-sm border-0 border-top border-4 border-info bg-white card-pulse">
                    <h6 class="fw-bold small text-secondary text-uppercase mb-1">Total Users</h6>
                    <h1 class="mt-2 mb-0 fw-bold" id="total_users">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-success p-3 shadow-sm border-0 border-top border-4 border-success bg-white card-pulse">
                    <h6 class="fw-bold small text-secondary text-uppercase mb-1">Active Users</h6>
                    <h1 class="mt-2 mb-0 fw-bold" id="active_users">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-warning p-3 shadow-sm border-0 border-top border-4 border-warning bg-white card-pulse">
                    <h6 class="fw-bold small text-secondary text-uppercase mb-1">Inactive Users</h6>
                    <h1 class="mt-2 mb-0 fw-bold" id="inactive_users">0</h1>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center text-danger p-3 shadow-sm border-0 border-top border-4 border-danger bg-white card-pulse">
                    <h6 class="fw-bold small text-secondary text-uppercase mb-1">Unverified Users</h6>
                    <h1 class="mt-2 mb-0 fw-bold" id="unverified_users">0</h1>
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
        try {
          let data = JSON.parse(this.responseText);
          
          // Map global card overview metrics
          document.getElementById('new_bookings_count').innerText = data.new_bookings;
          document.getElementById('refund_bookings_count').innerText = data.refund_bookings;
          document.getElementById('user_queries_count').innerText = data.user_queries;
          document.getElementById('reviews_count').innerText = data.reviews_count;
          
          // Map interactive time-filtered analytics counts
          document.getElementById('total_bookings').innerText = data.total_bookings;
          document.getElementById('total_amt').innerText = '₹' + data.total_revenue;
          document.getElementById('active_bookings').innerText = data.active_bookings;
          document.getElementById('active_amt').innerText = '₹' + data.active_revenue;
          document.getElementById('cancelled_bookings').innerText = data.cancelled_bookings;
          document.getElementById('cancelled_amt').innerText = '₹' + data.cancelled_revenue;

          document.getElementById('new_reg_count').innerText = data.new_reg;
          document.getElementById('queries_count').innerText = data.queries;
          document.getElementById('system_reviews_count').innerText = data.period_reviews;

          // Map global static user metrics counters
          document.getElementById('total_users').innerText = data.total_users;
          document.getElementById('active_users').innerText = data.active_users;
          document.getElementById('inactive_users').innerText = data.inactive_users;
          document.getElementById('unverified_users').innerText = data.unverified_users;
          
        } catch(e) {
          console.error("Error parsing metrics response payload streams: ", e);
        }
      }
      xhr.send('get_analytics=&period=' + period);
    }

    // Load dynamic summary on mount
    window.onload = function() {
      get_analytics(1);
    }
  </script>
</body>
</html>