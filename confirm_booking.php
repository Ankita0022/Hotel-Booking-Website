<?php
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  session_start();

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
      redirect('rooms.php');
  }

  if(!isset($_GET['id'])){
      redirect('rooms.php');
  }

  $data = filteration($_GET);

  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if(mysqli_num_rows($room_res) == 0){
      redirect('rooms.php');
  }

  $room_data = mysqli_fetch_assoc($room_res);

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
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container my-5 px-4" id="main-content">
    <div class="row">

      <div class="col-12 my-4 px-0">
        <h2 class="fw-bold h-font">CONFIRM BOOKING</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none fw-bold">HOME</a>
          <span class="text-secondary fw-bold"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none fw-bold">ROOMS</a>
          <span class="text-secondary fw-bold"> > </span>
          <a href="javascript:void(0)" class="text-secondary text-decoration-none fw-bold text-dark">CONFIRM</a>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 ps-0 pe-lg-4 pe-0 mb-4">
        <div id="roomCarousel" class="carousel slide shadow-sm rounded bg-white" data-bs-ride="carousel">
          <div class="carousel-inner p-2">
            <?php
              $img_q = select("SELECT * FROM `room_images` WHERE `room_id`=?", [$room_data['id']], 'i');

              if(mysqli_num_rows($img_q) > 0) {
                $active_class = "active";
                while($img_res = mysqli_fetch_assoc($img_q)) {
                  echo "
                    <div class='carousel-item $active_class'>
                      <img src='".ROOMS_IMG_PATH.$img_res['image']."' class='d-block w-100 rounded' style='height: 400px; object-fit: cover;'>
                    </div>
                  ";
                  $active_class = "";
                }
              } else {
                echo "
                  <div class='carousel-item active'>
                    <img src='".ROOMS_IMG_PATH."thumbnail.jpg' class='d-block w-100 rounded' style='height: 400px; object-fit: cover;'>
                  </div>
                ";
              }
            ?>
          </div>
        </div>

        <div class="mt-3 p-3 card border-0 shadow-sm rounded bg-white text-start">
          <h4 class="fw-bold text-dark mb-1 h-font"><?php echo $room_data['name']; ?></h4>
          <h5 class="text-secondary fw-bold mb-0">₹<?php echo $room_data['price']; ?> <span class="fs-6 fw-normal text-muted">/ per night</span></h5>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 px-0 mb-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
          <div class="card-body p-0">
            
            <form id="booking_form">
              <h5 class="mb-3 fw-bold text-dark text-uppercase tracking-wide">BOOKING DETAILS</h5>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold small text-secondary">Customer Name</label>
                  <input type="text" name="name" id="cust_name" value="<?php echo $user_data['name']; ?>" class="form-control shadow-none bg-light" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold small text-secondary">Phone Number</label>
                  <input type="number" name="phonenum" id="cust_phone" value="<?php echo $user_data['phonenum']; ?>" class="form-control shadow-none bg-light" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-dark">Check-in Date</label>
                  <input type="date" name="checkin" id="checkin_input" onchange="calculate_stay_bill()" class="form-control shadow-none border-2" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-dark">Check-out Date</label>
                  <input type="date" name="checkout" id="checkout_input" onchange="calculate_stay_bill()" class="form-control shadow-none border-2" required>
                </div>
                
                <div class="col-12 my-3">
                  <div class="p-3 rounded bg-white border border-2 text-start">
                    <h5 class="fw-bold text-dark mb-1"><?php echo $room_data['name']; ?></h5>
                    <h6 class="text-secondary fw-bold mb-0">₹<?php echo $room_data['price']; ?> per night</h6>
                  </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="p-3 bg-light rounded border border-dashed border-2 text-start">
                        <h6 class="fw-bold text-primary mb-1" id="days_count_label">Total Stay: 0 Night(s)</h6>
                        <h4 class="fw-bold text-dark mb-0" id="payable_amount_label">Total Payable: ₹0</h4>
                    </div>
                </div>

                <input type="hidden" name="room_id" id="room_id_input" value="<?php echo $room_data['id']; ?>">

                <div class="col-md-12">
                  <button type="submit" id="pay_now_btn" class="btn w-100 text-white btn-primary custom-bg shadow-none rounded-2 p-2.5 fw-bold text-uppercase tracking-wider" disabled>Proceed to Pay</button>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>

    </div>
  </div>

  <?php require('include/footer.php'); ?>

  <script>
    let booking_form = document.getElementById('booking_form');
    let pay_now_btn = document.getElementById('pay_now_btn');
    let checkin_input = document.getElementById('checkin_input');
    let checkout_input = document.getElementById('checkout_input');
    let days_count_label = document.getElementById('days_count_label');
    let payable_amount_label = document.getElementById('payable_amount_label');

    let room_price_per_night = <?php echo $room_data['price']; ?>;

    window.onload = function() {
      let today = new Date().toISOString().split('T')[0];
      checkin_input.min = today;
      checkout_input.min = today;
    }

    function calculate_stay_bill() {
      let checkin_val = checkin_input.value;
      let checkout_val = checkout_input.value;

      pay_now_btn.disabled = true;

      if(checkin_val !== '' && checkout_val !== '') {
        if(checkin_val >= checkout_val) {
          alert("Check-out date must be strictly after your chosen Check-in date!");
          checkout_input.value = '';
          return;
        }

        let d1 = new Date(checkin_val);
        let d2 = new Date(checkout_val);
        let time_difference = d2.getTime() - d1.getTime();
        let computed_nights = Math.ceil(time_difference / (1000 * 3600 * 24));

        let total_bill = computed_nights * room_price_per_night;

        days_count_label.innerText = `Total Stay: ${computed_nights} Night(s)`;
        payable_amount_label.innerText = `Total Payable: ₹${total_bill}`;
        
        pay_now_btn.disabled = false;
      }
    }

    booking_form.addEventListener('submit', function(e) {
      e.preventDefault();
      pay_now_btn.disabled = true;

      let data = new FormData();
      data.append('initiate_booking', '');
      data.append('room_id', document.getElementById('room_id_input').value);
      data.append('name', document.getElementById('cust_name').value);
      data.append('phonenum', document.getElementById('cust_phone').value);
      data.append('checkin', checkin_input.value);
      data.append('checkout', checkout_input.value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/initiate_booking.php", true);
      
      xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        
        if (res.status == 'unavailable') {
          alert('error', 'Room is fully booked for these dates! Please try different dates.');
          pay_now_btn.disabled = false;
        } else if (res.status == 'success') {
          var options = {
            "key": "rzp_test_SiPCJt11p5b6fC", 
            "amount": res.amount,
            "currency": "INR",
            "name": "Hotel Gaarland",
            "description": "Room Reservation - " + res.room_name,
            "image": "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/icons/hotel.svg",
            "handler": function (response){
              // Process the background DB entry modification prior to routing back to public grids
              let paymentData = new FormData();
              paymentData.append('payment_success', '');
              paymentData.append('order_id', res.order_id);
              paymentData.append('payment_id', response.razorpay_payment_id);

              let paymentXHR = new XMLHttpRequest();
              paymentXHR.open("POST", "ajax/confirm_booking_payment.php", true);

              paymentXHR.onload = function() {
                try {
                  let paymentRes = JSON.parse(this.responseText);
                  if (paymentRes.status == 'success') {
                    window.location.href = 'rooms.php?payment=success&order_id=' + res.order_id;
                  } else {
                    window.location.href = 'rooms.php?payment=failed';
                  }
                } catch (err) {
                  window.location.href = 'rooms.php?payment=failed';
                }
              };
              paymentXHR.send(paymentData);
            },
            "prefill": {
              "name": res.customer_name,
              "email": res.customer_email,
              "contact": res.customer_phone
            },
            "theme": { "color": "#2ec1ac" },
            "modal": {
              "ondismiss": function(){
                window.location.href = 'rooms.php?payment=failed';
              }
            }
          };
          var rzp1 = new Razorpay(options);
          rzp1.open();
        } else {
          alert('error', 'Could not create reservation framework. Please try again.');
          pay_now_btn.disabled = false;
        }
      };
      xhr.send(data);
    });
  </script>
</body>
</html>