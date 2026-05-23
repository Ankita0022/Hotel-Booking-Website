<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // Load all records flagged as cancelled where refund has NOT been issued yet
  if (isset($_POST['get_refunds'])) {
      $frm_data = filteration($_POST);
      $search = $frm_data['search'];

      $query = "SELECT bo.*, bd.* FROM `booking_order` bo 
                INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                WHERE bo.booking_status = 'cancelled' AND bo.refund = 0
                AND (bo.order_id LIKE ? OR bd.user_name LIKE ? OR bd.phonenum LIKE ?)
                ORDER BY bo.id DESC";
                
      $res = select($query, ["%$search%", "%$search%", "%$search%"], 'sss');
      $i = 1;
      $table_data = "";

      if (mysqli_num_rows($res) == 0) {
          echo json_encode(["table_data" => "<tr><td colspan='5' class='text-center'>No refund queue matches found.</td></tr>"]);
          exit;
      }

      while ($row = mysqli_fetch_assoc($res)) {
          $table_data .= "
          <tr class='align-middle'>
              <td>$i</td>
              <td>
                  <span class='badge bg-danger mb-1'>{$row['order_id']}</span><br>
                  <strong>Name:</strong> {$row['user_name']}<br>
                  <strong>Phone:</strong> {$row['phonenum']}
              </td>
              <td>
                  <strong>Room:</strong> {$row['room_name']}<br>
                  <strong>Check-in:</strong> {$row['check_in']}<br>
                  <strong>Check-out:</strong> {$row['check_out']}
              </td>
              <td>
                  <strong>Amount Reverted:</strong> ₹{$row['total_pay']}<br>
                  <strong>Razorpay Txn ID:</strong> <br><small class='text-secondary'>{$row['trans_id']}</small>
              </td>
              <td>
                  <button type='button' onclick='refund_booking({$row['booking_id']})' class='btn btn-sm btn-success shadow-none'>
                      <i class='bi bi-cash-stack'></i> Refund
                  </button>
              </td>
          </tr>";
          $i++;
      }
      echo json_encode(["table_data" => $table_data]);
  }

  // Process refund and mark as completed
  if (isset($_POST['refund_booking'])) {
      $frm_data = filteration($_POST);
      
      $query = "UPDATE `booking_order` SET `refund` = 1 WHERE `id` = ?";
      $res = update($query, [$frm_data['booking_id']], 'i');
      echo $res ? 1 : 0;
  }
?>