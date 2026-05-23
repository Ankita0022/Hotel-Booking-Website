<?php
  require_once('../admin/inc/db_config.php');
  require_once('../admin/inc/essentials.php');
  
  session_start();

  if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
      echo 0;
      exit;
  }

  if(isset($_POST['submit_review'])) {
      $frm_data = filteration($_POST);

      // Duplication Interceptor Check: Enforce a single review block mapping constraint per room order
      $check_q = "SELECT * FROM `rating_review` WHERE `booking_id`=? AND `user_id`=? LIMIT 1";
      $check_res = select($check_q, [$frm_data['booking_id'], $_SESSION['uId']], 'ii');

      if(mysqli_num_rows($check_res) > 0) {
          echo 'already_reviewed';
          exit;
      }

      $query = "INSERT INTO `rating_review` (`booking_id`, `room_id`, `user_id`, `rating`, `review`) VALUES (?, ?, ?, ?, ?)";
      $values = [$frm_data['booking_id'], $frm_data['room_id'], $_SESSION['uId'], $frm_data['rating'], $frm_data['review']];

      if(insert($query, $values, 'iiiis')) {
          echo 1;
      } else {
          echo 0;
      }
  }
?>