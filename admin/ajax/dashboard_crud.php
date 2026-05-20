<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  if (isset($_POST['get_analytics'])) {
      $frm_data = filteration($_POST);
      $period = (int)$frm_data['period'];

      // Setup clean time interval parameters matching database layout tracking metrics
      $condition = "";
      if ($period == 1) {
          $condition = "WHERE `datentime` >= NOW() - INTERVAL 30 DAY";
      } else if ($period == 2) {
          $condition = "WHERE `datentime` >= NOW() - INTERVAL 90 DAY";
      } else if ($period == 3) {
          $condition = "WHERE `datentime` >= NOW() - INTERVAL 1 YEAR";
      }

      // Initialize response metrics tracker layout maps
      $total_bookings = 0;
      $total_revenue = 0;
      $cancelled_bookings = 0;
      $failed_bookings = 0;

      // Extract records securely within specified range criteria using your native select helper function
      $query = "SELECT `booking_status`, `trans_amt` FROM `booking_order` $condition";
      $res = select($query, [], ''); // Using project's standard select wrapper without needing a $con variable parameter

      while ($row = mysqli_fetch_assoc($res)) {
          $total_bookings++;
          if ($row['booking_status'] == 'booked') {
              $total_revenue += $row['trans_amt'];
          } else if ($row['booking_status'] == 'cancelled') {
              $cancelled_bookings++;
          } else if ($row['booking_status'] == 'payment_failed') {
              $failed_bookings++;
          }
      }

      echo json_encode([
          "total_bookings" => $total_bookings,
          "total_revenue" => $total_revenue,
          "cancelled_bookings" => $cancelled_bookings,
          "failed_bookings" => $failed_bookings
      ]);
  }
?>