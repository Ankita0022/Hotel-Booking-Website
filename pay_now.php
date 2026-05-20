<?php
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  session_start();

  // Redirect to rooms page if user is not logged in
  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
      redirect('rooms.php');
  }

  // Safety block: check if explicit room request context exists
  if(!isset($_POST['room_id'])){
      redirect('rooms.php');
  }

  $frm_data = filteration($_POST);

  // Fetch target room details to ensure correct price computation
  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$frm_data['room_id'], 1, 0], 'iii');

  if(mysqli_num_rows($room_res) == 0){
      redirect('rooms.php');
  }

  $room_data = mysqli_fetch_assoc($room_res);

  // Cache target room parameters inside session array to carry into the processing step
  $_SESSION['room'] = [
    'id' => $room_data['id'],
    'name' => $room_data['name'],
    'price' => $room_data['price'],
    'payment' => $room_data['price']
  ];

  // Fetch active user details from database to pre-fill checkout parameters
  $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], 'i');
  $user_data = mysqli_fetch_assoc($user_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Gaarland - CONFIRM BOOKING</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container my-5 px-4" id="main-content">
    <div class="row justify-content-center">

      <div class="col-12 my-4 mb-4">
        <h2 class="fw-bold h-font text-center">CONFIRM BOOKING</h2>
        <div class="h-line bg-dark"></div>
      </div>

      <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
          <div class="row g-0">
            
            <div class="col-md-6 p-4 bg-white border-end">
              <h5 class="mb-3 fw-bold border-bottom pb-2">Room Summary</h5>
              <h4 class="text-dark fw-bold mb-1"><?php echo $room_data['name']; ?></h4>
              <h5 class="text-secondary mb-4">₹<?php echo $room_data['price']; ?> per night</h5>
              
              <div class="mb-3">
                <h6 class="fw-bold mb-1"><i class="bi bi-person-fill text-muted me-1"></i> Guest Capacity</h6>
                <span class="badge bg-light text-dark border"><?php echo $room_data['adult']; ?> Adults</span>
                <span class="badge bg-light text-dark border"><?php echo $room_data['children']; ?> Children</span>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-1"><i class="bi bi-arrows-fullscreen text-muted me-1"></i> Room Area</h6>
                <span class="badge bg-light text-dark border"><?php echo $room_data['area']; ?> sq. ft.</span>
              </div>
              
              <div class="p-3 bg-light rounded border border-dashed">
                <h5 class="fw-bold text-primary mb-1" id="total_days_label">Total Stay: 0 Nights</h5>
                <h3 class="fw-bold text-dark mb-0" id="total_amount_label">Total Payable: ₹0</h3>
              </div>
            </div>

            <div class="col-md-6 p-4 bg-white">
              <h5 class="mb-3 fw-bold border-bottom pb-2">Booking & Contact Details</h5>
              
              <form action="confirm_booking.php" method="POST" id="booking_form">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="name" value="<?php echo $user_data['name']; ?>" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="number" name="phonenum" value="<?php echo $user_data['phonenum']; ?>" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-in Date</label>
                    <input type="date" name="checkin" id="checkin_input" onchange="check_availability_dates()" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-out Date</label>
                    <input type="date" name="checkout" id="checkout_input" onchange="check_availability_dates()" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">Permanent Address</label>
                    <textarea name="address" rows="3" class="form-control shadow-none" required><?php echo $user_data['address']; ?></textarea>
                  </div>
                  <div class="col-md-12">
                    <button type="submit" name="pay_now" id="pay_btn" class="btn btn-primary btn-primary-custom w-100 rounded-pill shadow-none fw-bold p-2.5" disabled>Proceed to Pay with Razorpay</button>
                  </div>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  <?php require('include/footer.php'); ?>

  <script>
    let booking_form = document.getElementById('booking_form');
    let pay_btn = document.getElementById('pay_btn');
    let checkin_input = document.getElementById('checkin_input');
    let checkout_input = document.getElementById('checkout_input');
    let total_days_label = document.getElementById('total_days_label');
    let total_amount_label = document.getElementById('total_amount_label');

    let room_price = <?php echo $room_data['price']; ?>;

    // Set minimum baseline input selection constraints dynamically to today's date
    window.onload = function() {
      let today = new Date().toISOString().split('T')[0];
      checkin_input.min = today;
      checkout_input.min = today;
    }

    function check_availability_dates() {
      let checkin_val = checkin_input.value;
      let checkout_val = checkout_input.value;

      pay_btn.disabled = true;

      if(checkin_val !== '' && checkout_val !== '') {
        if(checkin_val >= checkout_val) {
          alert("Check-out date must be strictly after your selected Check-in date!");
          checkout_input.value = '';
          return;
        }

        // Compute localized client stay days delta parameter
        let date1 = new Date(checkin_val);
        let date2 = new Date(checkout_val);
        let time_diff = date2.getTime() - date1.getTime();
        let days_diff = Math.ceil(time_diff / (1000 * 3600 * 24));

        let final_payable = days_diff * room_price;

        total_days_label.innerText = `Total Stay: ${days_diff} Night(s)`;
        total_amount_label.innerText = `Total Payable: ₹${final_payable}`;
        
        pay_btn.disabled = false;
      }
    }
  </script>

</body>
</html>