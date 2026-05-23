<?php 
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  
  session_start();

  // Route unauthenticated lookup sessions away safely
  if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('index.php');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Gaarland - BOOKINGS</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container my-5 px-4" id="main-content">
    <div class="row">

      <div class="col-12 my-4 px-0">
        <h2 class="fw-bold h-font">BOOKINGS</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none fw-bold">HOME</a>
          <span class="text-secondary fw-bold"> > </span>
          <a href="javascript:void(0)" class="text-secondary text-decoration-none fw-bold text-dark">BOOKINGS</a>
        </div>
      </div>

      <div class="col-12 px-0">
        <?php
          // Query all historical transactions linked to the logged-in user profile
          $query = "SELECT bo.*, bd.* FROM `booking_order` bo 
                    INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                    WHERE bo.user_id = ? 
                    ORDER BY bo.id DESC";

          $res = select($query, [$_SESSION['uId']], 'i');

          if(mysqli_num_rows($res) == 0){
            echo "<div class='alert alert-info text-center shadow-sm fw-bold border-0 bg-white rounded-3 p-4 text-secondary'><i class='bi bi-folder-x fs-3 d-block mb-2'></i>You have not made any room reservations yet.</div>";
          }

          while($row = mysqli_fetch_assoc($res)){
            $date = date("d-m-Y", strtotime($row['datentime']));
            $checkin = date("d-m-Y", strtotime($row['check_in']));
            $checkout = date("d-m-Y", strtotime($row['check_out']));

            $status_badge = "";
            $btn_actions = "";

            // Evaluate states based on your database schema status strings
            if($row['booking_status'] == 'booked'){
              $status_badge = "<span class='badge bg-success p-2 rounded-2 text-uppercase tracking-wider small fw-bold shadow-none'>Confirmed</span>";
              
              if($row['arrival'] == 1){
                $btn_actions = "<span class='badge bg-dark p-2 rounded-2 small fw-bold d-block text-center mt-2'>Room Assigned: {$row['room_no']}</span>";
                
                // NEW ELEMENT: Add Rate & Review interactive button once checkout/arrival flow completes
                $btn_actions .= "<button onclick='open_review_modal({$row['booking_id']}, {$row['room_id']})' class='btn btn-sm btn-outline-dark shadow-none fw-bold rounded-2 px-3 w-100 mt-2 text-uppercase tracking-wide'><i class='bi bi-star-fill me-1 text-warning'></i> Rate & Review</button>";
              } else {
                // If reservation is confirmed but arrival isn't marked, offer a cancellation request button
                $btn_actions = "<button onclick='cancel_user_booking({$row['booking_id']})' class='btn btn-sm btn-danger shadow-none fw-bold rounded-2 px-3 mt-2 w-100 text-uppercase tracking-wide'>Cancel Booking</button>";
              }
            } else if($row['booking_status'] == 'cancelled'){
              $status_badge = "<span class='badge bg-danger p-2 rounded-2 text-uppercase tracking-wider small fw-bold shadow-none'>Cancelled</span>";
              if($row['refund'] == 1){
                $btn_actions = "<span class='d-block mt-2 text-success small fw-bold text-center'><i class='bi bi-shield-check me-1'></i> Refund Processed</span>";
              } else {
                $btn_actions = "<span class='d-block mt-2 text-warning small fw-bold text-center'><i class='bi bi-clock me-1'></i> Refund Pending</span>";
              }
            } else {
              $status_badge = "<span class='badge bg-warning text-dark p-2 rounded-2 text-uppercase tracking-wider small fw-bold shadow-none'>Pending / Failed</span>";
              $btn_actions = "<a href='rooms.php' class='btn btn-sm btn-dark shadow-none fw-bold rounded-2 px-3 mt-2 d-block text-center text-uppercase tracking-wide'>Try Again</a>";
            }

            echo "
              <div class='card border-0 shadow-sm rounded-3 mb-4 p-4 bg-white'>
                <div class='row align-items-center'>
                  
                  <div class='col-lg-4 col-md-6 mb-3 mb-lg-0'>
                    <h5 class='fw-bold text-dark mb-1'>{$row['room_name']}</h5>
                    <p class='text-secondary fw-bold mb-2 small'>₹{$row['price']} per night</p>
                    <span class='text-muted small bg-light p-1.5 px-2 rounded border'><i class='bi bi-calendar3 me-1'></i> Ordered on: $date</span>
                  </div>
                  
                  <div class='col-lg-5 col-md-6 mb-3 mb-lg-0 border-start ps-lg-4'>
                    <p class='mb-1 text-dark small'><strong>Check-in:</strong> <span class='text-secondary fw-bold'>$checkin</span></p>
                    <p class='mb-1 text-dark small'><strong>Check-out:</strong> <span class='text-secondary fw-bold'>$checkout</span></p>
                    <p class='mb-1 text-dark small'><strong>Amount Paid:</strong> <span class='text-primary fw-bold'>₹{$row['total_pay']}</span></p>
                    <p class='mb-0 text-dark small'><strong>Order ID:</strong> <span class='text-dark font-monospace fw-bold'>{$row['order_id']}</span></p>
                  </div>
                  
                  <div class='col-lg-3 text-lg-end text-start border-start ps-lg-4'>
                    <div class='mb-1'>$status_badge</div>
                    <div>$btn_actions</div>
                  </div>

                </div>
              </div>
            ";
          }
        ?>
      </div>

    </div>
  </div>

  <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow rounded-3">
        <form id="review-form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center fw-bold"><i class="bi bi-chat-square-heart-fill text-dark fs-3 me-2"></i> Submit Room Review</h5>
            <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Select Rating</label>
              <select class="form-select shadow-none border-2 fw-bold text-warning" name="rating" required>
                <option value="5" class="text-dark">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4" class="text-dark">⭐⭐⭐⭐ (Very Good)</option>
                <option value="3" class="text-dark">⭐⭐⭐ (Good)</option>
                <option value="2" class="text-dark">⭐⭐ (Fair)</option>
                <option value="1" class="text-dark">⭐ (Poor)</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Your Review Message</label>
              <textarea name="review" rows="4" class="form-control shadow-none border-2" placeholder="Tell us about your experience staying in this room..." required></textarea>
            </div>
            
            <input type="hidden" name="booking_id" id="review_booking_id">
            <input type="hidden" name="room_id" id="review_room_id">
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn btn-secondary shadow-none rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-dark shadow-none rounded-pill px-4 fw-bold text-white custom-bg">Submit Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('include/footer.php'); ?>

  <script>
    function cancel_user_booking(id) {
      if(confirm("Are you sure you want to cancel this booking?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/cancel_booking.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if(this.responseText == 1) {
            alert('success', 'Booking successfully cancelled! It is now pending a refund.');
            location.reload(); 
          } else {
            alert('error', 'Cancellation processing gateway pipeline error.');
          }
        }
        xhr.send('cancel_booking=&booking_id=' + id);
      }
    }

    // NEW JAVASCRIPT HANDLERS: Modal bindings and async AJAX validation streams integration
    let review_modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    let review_form = document.getElementById('review-form');

    function open_review_modal(booking_id, room_id) {
        document.getElementById('review_booking_id').value = booking_id;
        document.getElementById('review_room_id').value = room_id;
        review_form.reset();
        review_modal.show();
    }

    review_form.addEventListener('submit', function(e){
        e.preventDefault();
        
        let data = new FormData(review_form);
        data.append('submit_review', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/review_rating.php", true);

        xhr.onload = function(){
            if(this.responseText == 1){
                alert('success', 'Thank you! Your feedback review has been successfully documented!');
                review_modal.hide();
                location.reload();
            } else if(this.responseText == 'already_reviewed') {
                alert('error', 'You have already submitted a rating review for this room reservation!');
                review_modal.hide();
            } else {
                alert('error', 'Server Error. Review pipeline execution failure.');
            }
        }
        xhr.send(data);
    });
  </script>

</body>
</html>