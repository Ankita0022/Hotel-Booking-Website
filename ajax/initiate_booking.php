<?php
  require_once('../admin/inc/db_config.php');
  require_once('../admin/inc/essentials.php');

  session_start();

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
      echo json_encode(['status' => 'not_logged_in']);
      exit;
  }

  if (isset($_POST['initiate_booking'])) {
      $frm_data = filteration($_POST);

      $uId = $_SESSION['uId'];
      $order_id = "ORD_" . $_SESSION['uId'] . "_" . random_int(11111, 99999);
      $booking_status = "pending";

      // Server-side safe date calculations
      $checkin = new DateTime($frm_data['checkin']);
      $checkout = new DateTime($frm_data['checkout']);
      $interval = $checkin->diff($checkout);
      $count_days = $interval->days;

      $room_res = select("SELECT * FROM `rooms` WHERE `id`=? LIMIT 1", [$frm_data['room_id']], 'i');
      $room_data = mysqli_fetch_assoc($room_res);
      
      // Strict Backend Availability Check
      $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order` 
          WHERE booking_status='booked' AND room_id=? 
          AND check_in < ? AND check_out > ?";
      $tb_values = [$frm_data['room_id'], $frm_data['checkout'], $frm_data['checkin']];
      $tb_fetch = mysqli_fetch_assoc(select($tb_query, $tb_values, 'iss'));

      if($tb_fetch['total_bookings'] >= $room_data['quantity']) {
          echo json_encode(['status' => 'unavailable']);
          exit;
      }

      $total_payable = $room_data['price'] * $count_days;

      // 1. Insert row into booking_order map using trans_amt
      $query1 = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `trans_amt`, `booking_status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $values1 = [$uId, $frm_data['room_id'], $frm_data['checkin'], $frm_data['checkout'], $order_id, $total_payable, $booking_status];
      insert($query1, $values1, 'iisssis');

      // FIX: Access the connection globally from your database configuration
      global $con;
      $booking_id = mysqli_insert_id($con);

      // 2. Insert tracking properties row into booking_details
      $query2 = "INSERT INTO `booking_details` (`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $values2 = [$booking_id, $room_data['name'], $room_data['price'], $total_payable, $frm_data['name'], $frm_data['phonenum'], "Hotel Guest"];
      insert($query2, $values2, 'issssss');

      // Return crucial handshake parameters back to front-end window context
      echo json_encode([
          'status' => 'success',
          'order_id' => $order_id,
          'amount' => ($total_payable * 100), // In Paisa
          'room_name' => $room_data['name'],
          'customer_name' => $frm_data['name'],
          'customer_phone' => $frm_data['phonenum'],
          'customer_email' => $_SESSION['uEmail'] ?? ''
      ]);
      exit;
  }
?>