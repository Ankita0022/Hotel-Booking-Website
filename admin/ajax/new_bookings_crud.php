<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // Load all pending check-in bookings
  if (isset($_POST['get_new_bookings'])) {
      $frm_data = filteration($_POST);
      $search = $frm_data['search'];

      // Pull matches where status is active 'booked' but the user hasn't arrived (arrival = 0)
      $query = "SELECT bo.*, bd.* FROM `booking_order` bo 
                INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                WHERE bo.booking_status = 'booked' AND bo.arrival = 0 
                AND (bo.order_id LIKE ? OR bd.user_name LIKE ? OR bd.phonenum LIKE ?)
                ORDER BY bo.id DESC";
                
      $res = select($query, ["%$search%", "%$search%", "%$search%"], 'sss');
      $i = 1;
      $table_data = "";

      if (mysqli_num_rows($res) == 0) {
          echo json_encode(["table_data" => "<tr><td colspan='5' class='text-center'>No active arrivals found matching criteria.</td></tr>"]);
          exit;
      }

      while ($row = mysqli_fetch_assoc($res)) {
          $table_data .= "
          <tr class='align-middle'>
              <td>$i</td>
              <td>
                  <span class='badge bg-primary mb-1'>{$row['order_id']}</span><br>
                  <strong>Name:</strong> {$row['user_name']}<br>
                  <strong>Phone:</strong> {$row['phonenum']}
              </td>
              <td>
                  <strong>Room:</strong> {$row['room_name']}<br>
                  <strong>Price:</strong> ₹{$row['price']}
              </td>
              <td>
                  <strong>Check-in:</strong> {$row['check_in']}<br>
                  <strong>Check-out:</strong> {$row['check_out']}<br>
                  <strong>Paid Amount:</strong> ₹{$row['total_pay']}<br>
                  <strong>Txn Ref:</strong> <small class='text-secondary'>{$row['trans_id']}</small>
              </td>
              <td>
                  <button type='button' onclick='assign_room_modal({$row['booking_id']})' class='btn btn-sm btn-primary text-white shadow-none' data-bs-toggle='modal' data-bs-target='#assign-room'>
                      <i class='bi bi-check2-square'></i> Assign Room
                  </button>
                  <button type='button' onclick='cancel_booking({$row['booking_id']})' class='btn btn-sm btn-danger shadow-none ms-1'>
                      <i class='bi bi-trash'></i> Cancel
                  </button>
              </td>
          </tr>";
          $i++;
      }
      echo json_encode(["table_data" => $table_data]);
  }

  // Allocate physical room numbers and mark customer as arrived
  if (isset($_POST['assign_room'])) {
      $frm_data = filteration($_POST);
      
      $query = "UPDATE `booking_order` bo 
                INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                SET bo.arrival = 1, bd.room_no = ? 
                WHERE bo.id = ?";
                
      $res = update($query, [$frm_data['room_no'], $frm_data['booking_id']], 'si');
      echo $res ? 1 : 0;
  }

  // Handle manual dashboard cancellation accompanied by automated cURL Razorpay refunds
  if (isset($_POST['cancel_booking'])) {
      $frm_data = filteration($_POST);

      $q = "SELECT `trans_id`, `trans_amt` FROM `booking_order` WHERE `id`=?";
      $res = select($q, [$frm_data['booking_id']], 'i');
      $data = mysqli_fetch_assoc($res);

      $payment_id = $data['trans_id'];
      $refund_amount = $data['trans_amt'] * 100; // Transform into raw Paise units

      // Call Razorpay Payments API Refund endpoint handler
      $url = "https://api.razorpay.com/v1/payments/" . $payment_id . "/refund";
      $payload = json_encode(["amount" => $refund_amount]);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($http_code == 200 || $http_code == 201) {
          // Update the localized system order status tracking fields
          $query = "UPDATE `booking_order` SET `booking_status`='cancelled', `refund`=1 WHERE `id`=?";
          if (update($query, [$frm_data['booking_id']], 'i')) {
              echo 1;
          } else {
              echo 0;
          }
      } else {
          echo 0;
      }
  }
?>