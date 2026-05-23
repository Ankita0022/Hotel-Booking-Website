<?php
  require_once('../inc/db_config.php');
  require_once('../inc/essentials.php');
  adminLogin();

  if (isset($_POST['get_analytics'])) {
      $frm_data = filteration($_POST);
      $period = (int)$frm_data['period'];

      // Set date criteria intervals
      $condition = "";
      if ($period == 1) { $condition = "WHERE `datentime` >= NOW() - INTERVAL 30 DAY"; }
      else if ($period == 2) { $condition = "WHERE `datentime` >= NOW() - INTERVAL 90 DAY"; }
      else if ($period == 3) { $condition = "WHERE `datentime` >= NOW() - INTERVAL 1 YEAR"; }

      // 1. Fetch upper global overview data metrics tallies
      $new_bookings = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `booking_order` WHERE `booking_status`='booked' AND `arrival`=0"));
      $refund_bookings = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `booking_order` WHERE `booking_status`='cancelled' AND `refund`=0"));
      $user_queries = mysqli_num_rows(mysqli_query($con, "SELECT `sr_no` FROM `user_queries` WHERE `seen`=0"));
      $reviews_count = mysqli_num_rows(mysqli_query($con, "SELECT `sr_no` FROM `rating_review` WHERE `seen`=0")); 

      // 2. Compute dynamic operational metrics counts over period criteria
      $total_bookings = 0; $total_revenue = 0;
      $active_bookings = 0; $active_revenue = 0;
      $cancelled_bookings = 0; $cancelled_revenue = 0;

      $res = mysqli_query($con, "SELECT bo.booking_status, bd.total_pay FROM `booking_order` bo INNER JOIN `booking_details` bd ON bo.id = bd.booking_id $condition");
      while ($row = mysqli_fetch_assoc($res)) {
          $total_bookings++;
          $total_revenue += $row['total_pay'];

          if ($row['booking_status'] == 'booked') {
              $active_bookings++;
              $active_revenue += $row['total_pay'];
          } else if ($row['booking_status'] == 'cancelled') {
              $cancelled_bookings++;
              $cancelled_revenue += $row['total_pay'];
          }
      }

      // 3. Fetch chronological updates for system analytics
      $new_reg = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `user_cred` $condition"));
      $queries = mysqli_num_rows(mysqli_query($con, "SELECT `sr_no` FROM `user_queries` $condition"));
      $period_reviews = mysqli_num_rows(mysqli_query($con, "SELECT `sr_no` FROM `rating_review` $condition"));

      // 4. Gather status values matching global user parameters
      $total_users = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `user_cred`"));
      $active_users = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `user_cred` WHERE `status`=1"));
      $inactive_users = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `user_cred` WHERE `status`=0"));
      $unverified_users = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `user_cred` WHERE `is_verified`=0"));

      echo json_encode([
          "new_bookings" => $new_bookings,
          "refund_bookings" => $refund_bookings,
          "user_queries" => $user_queries,
          "reviews_count" => $reviews_count,
          "total_bookings" => $total_bookings,
          "total_revenue" => $total_revenue,
          "active_bookings" => $active_bookings,
          "active_revenue" => $active_revenue,
          "cancelled_bookings" => $cancelled_bookings,
          "cancelled_revenue" => $cancelled_revenue,
          "new_reg" => $new_reg,
          "queries" => $queries,
          "period_reviews" => $period_reviews,
          "total_users" => $total_users,
          "active_users" => $active_users,
          "inactive_users" => $inactive_users,
          "unverified_users" => $unverified_users
      ]);
      exit;
  }
?>