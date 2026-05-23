<?php
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  session_start();

  if(!isset($_GET['order'])){
    redirect('index.php');
  }

  $frm_data = filteration($_GET);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Gaarland - BOOKING STATUS</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container my-5 px-4" id="main-content">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10 col-12">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white text-center">
          <div class="card-body">

            <?php
              $booking_q = "SELECT bo.*, bd.* FROM `booking_order` bo 
                            INNER JOIN `booking_details` bd ON bo.id = bd.booking_id 
                            WHERE bo.order_id = ? AND bo.user_id = ? LIMIT 1";
                            
              $booking_res = select($booking_q, [$frm_data['order'], $_SESSION['uId']], 'si');

              if(mysqli_num_rows($booking_res) == 0) {
                echo "
                  <i class='bi bi-exclamation-triangle-fill text-danger display-1 mb-3'></i>
                  <h2 class='fw-bold text-dark mb-3'>Invalid Reference Token</h2>
                  <a href='index.php' class='btn btn-dark shadow-none rounded-pill px-4 fw-bold'>Go to Home</a>
                ";
              } else {
                $booking_data = mysqli_fetch_assoc($booking_res);

                if($booking_data['booking_status'] == 'booked') {
                  echo "
                    <i class='bi bi-check-circle-fill text-success display-1 mb-3'></i>
                    <h2 class='fw-bold text-dark mb-2'>Booking Successful!</h2>
                    <h5 class='text-secondary mb-4'>Thank you for choosing Hotel Gaarland</h5>
                    
                    <div class='p-3 bg-light rounded border border-dashed border-2 text-start mb-4 mx-auto' style='max-width: 500px;'>
                      <p class='mb-1'><strong>Order ID:</strong> <span class='text-primary fw-bold'>{$booking_data['order_id']}</span></p>
                      <p class='mb-1'><strong>Room Reserved:</strong> {$booking_data['room_name']}</p>
                      <p class='mb-1'><strong>Total Amount Paid:</strong> ₹{$booking_data['total_pay']}</p>
                    </div>

                    <div class='d-flex justify-content-center gap-3'>
                      <a href='index.php' class='btn btn-outline-dark shadow-none rounded-pill px-4 fw-bold'>Back to Home</a>
                    </div>
                  ";
                } else {
                  echo "
                    <i class='bi bi-x-circle-fill text-danger display-1 mb-3'></i>
                    <h2 class='fw-bold text-dark mb-2'>Payment Authorization Failed</h2>
                    <a href='rooms.php' class='btn btn-dark shadow-none rounded-pill px-4 fw-bold'>Try Again</a>
                  ";
                }
              }
            ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('include/footer.php'); ?>
</body>
</html>