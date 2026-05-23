<?php
  require_once('../admin/inc/db_config.php');
  require_once('../admin/inc/essentials.php');

  session_start();

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
      echo json_encode(['status' => 'failed', 'message' => 'Not logged in']);
      exit;
  }

  if (isset($_POST['payment_success'])) {
      $frm_data = filteration($_POST);

      $order_id = $frm_data['order_id'];
      $payment_id = $frm_data['payment_id'];

      // Update order status criteria to 'booked' and append the official gateway payment token reference
      $query = "UPDATE `booking_order` SET `booking_status`='booked', `trans_id`=? WHERE `order_id`=? AND `user_id`=?";
      $values = [$payment_id, $order_id, $_SESSION['uId']];
      
      if (update($query, $values, 'ssi')) {
          echo json_encode(['status' => 'success']);
      } else {
          echo json_encode(['status' => 'failed', 'message' => 'Database update failed']);
      }
      exit;
  }
?>