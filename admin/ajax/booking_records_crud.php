<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  if (isset($_POST['get_records'])) {
      $frm_data = filteration($_POST);
      
      $page = isset($frm_data['page']) ? (int)$frm_data['page'] : 1;
      $search = $frm_data['search'];
      $limit = 10; // Limits rows per paginated view
      $start = ($page - 1) * $limit;

      $query = "SELECT bo.*, bd.* FROM `booking_order` bo 
                INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                WHERE (bo.booking_status='booked' OR bo.booking_status='cancelled' OR bo.booking_status='payment_failed')
                AND (bo.order_id LIKE ? OR bd.user_name LIKE ? OR bd.phonenum LIKE ?)
                ORDER BY bo.id DESC LIMIT $start, $limit";

      $count_query = "SELECT COUNT(*) AS `total` FROM `booking_order` bo 
                      INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                      WHERE (bo.booking_status='booked' OR bo.booking_status='cancelled' OR bo.booking_status='payment_failed')
                      AND (bo.order_id LIKE ? OR bd.user_name LIKE ? OR bd.phonenum LIKE ?)";

      $res = select($query, ["%$search%", "%$search%", "%$search%"], 'sss');
      $count_res = select($count_query, ["%$search%", "%$search%", "%$search%"], 'sss');
      
      $count_data = mysqli_fetch_assoc($count_res);
      $total_rows = $count_data['total'];
      $total_pages = ceil($total_rows / $limit);

      $i = $start + 1;
      $table_data = "";

      if (mysqli_num_rows($res) == 0) {
          echo json_encode(["table_data" => "<tr><td colspan='6' class='text-center'>No records present inside system logs.</td></tr>", "pagination" => ""]);
          exit;
      }

      while ($row = mysqli_fetch_assoc($res)) {
          $status_badge = "";
          if ($row['booking_status'] == 'booked') {
              $status_badge = "<span class='badge bg-success'>Success</span>";
          } else if ($row['booking_status'] == 'cancelled') {
              $status_badge = "<span class='badge bg-danger'>Cancelled & Refunded</span>";
          } else {
              $status_badge = "<span class='badge bg-warning text-dark'>Failed</span>";
          }

          $table_data .= "
          <tr class='align-middle'>
              <td>$i</td>
              <td>
                  <span class='badge bg-secondary mb-1'>{$row['order_id']}</span><br>
                  <strong>Name:</strong> {$row['user_name']}<br>
                  <strong>Phone:</strong> {$row['phonenum']}
              </td>
              <td>
                  <strong>Room:</strong> {$row['room_name']}<br>
                  <strong>Paid Amount:</strong> ₹{$row['total_pay']}
              </td>
              <td>
                  <strong>Check-in:</strong> {$row['check_in']}<br>
                  <strong>Check-out:</strong> {$row['check_out']}<br>
                  <strong>Room Allocated:</strong> " . ($row['room_no'] ? "<span class='badge bg-dark'>".$row['room_no']."</span>" : "<small class='text-muted'>None</small>") . "
              </td>
              <td>$status_badge</td>
              <td>
                  <a href='generate_receipt.php?id={$row['booking_id']}' target='_blank' class='btn btn-sm btn-outline-danger shadow-none'>
                      <i class='bi bi-file-earmark-pdf-fill'></i> Receipt
                  </a>
              </td>
          </tr>";
          $i++;
      }

      // Pagination Builder perfectly structured for display inside bottom right corners
      $pagination = "";
      if ($total_pages > 1) {
          for ($p = 1; $p <= $total_pages; $p++) {
              $active_btn = ($p == $page) ? "btn-dark" : "btn-outline-dark";
              $pagination .= "<li class='page-item'><button onclick='get_records($p, \"$search\")' class='btn btn-sm $active_btn shadow-none m-1'>$p</button></li>";
          }
      }

      echo json_encode(["table_data" => $table_data, "pagination" => $pagination]);
  }
?>