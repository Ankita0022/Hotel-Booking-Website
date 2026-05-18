<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

// Feature Validation: Verify active login session state
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('rooms.php');
}

if (!isset($_POST['pay_now'])) {
    redirect('rooms.php');
}

$frm_data = filteration($_POST);

// Date Validation feature logic matching the playlist criteria
$checkin_date = new DateTime($frm_data['checkin']);
$checkout_date = new DateTime($frm_data['checkout']);

if ($checkin_date >= $checkout_date) {
    alert('error', 'Check-out date must be after check-in date!');
    exit;
}

$days = $checkin_date->diff($checkout_date)->days;
$TXN_AMOUNT = $_SESSION['room']['price'] * $days;

// Feature: Check if room is available during selected dates to prevent overbooking
$rq = "SELECT COUNT(*) AS `total_booked` FROM `booking_order` 
       WHERE `room_id`=? AND `booking_status`='booked' AND `refund`=0
       AND `check_out` > ? AND `check_in` < ?";
$res_availability = select($rq, [$_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout']], 'iss');
$availability_data = mysqli_fetch_assoc($res_availability);

// Fetch maximum quantity available for the target room
$room_q = select("SELECT `quantity` FROM `rooms` WHERE `id`=?", [$_SESSION['room']['id']], 'i');
$room_data = mysqli_fetch_assoc($room_q);

if ($availability_data['total_booked'] >= $room_data['quantity']) {
    echo "<script>alert('Sorry, this room is fully booked for your selected dates!'); window.location.href='rooms.php';</script>";
    exit;
}

// Generate sequential booking reference credentials
$ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 99999);
$CUST_ID = $_SESSION['uId'];

// Create provisional database placeholders 
$query1 = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `trans_amt`, `booking_status`) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
insert($query1, [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout'], $ORDER_ID, $TXN_AMOUNT], 'iissss');

$booking_id = mysqli_insert_id($con);

$query2 = "INSERT INTO `booking_details` (`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?, ?, ?, ?, ?, ?, ?)";
insert($query2, [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']], 'isiisss');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connecting to Secure Gateway...</title>
    <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

    <div class="container text-center mt-5 pt-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading Secure Gateway...</span>
        </div>
        <h3 class="mt-3">Please wait, launching payment gateway window...</h3>
        <p>Do not refresh this tab or hit the back navigation button.</p>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "<?php echo RAZORPAY_KEY_ID; ?>",
            "amount": "<?php echo ($TXN_AMOUNT * 100); ?>", // Amount transformed to lowest unit denomination (Paise)
            "currency": "INR",
            "name": "Hotel Gaarland",
            "description": "Room Stay Reservation Downpayment Charge",
            "handler": function (response){
                // Capture gateway success transaction identifiers and pass via secure POST
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'verify_payment.php';

                var fields = {
                    'order_id': '<?php echo $ORDER_ID; ?>',
                    'booking_id': '<?php echo $booking_id; ?>',
                    'razorpay_payment_id': response.razorpay_payment_id
                };

                for (var key in fields) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "<?php echo $frm_data['name']; ?>",
                "contact": "<?php echo $frm_data['phonenum']; ?>"
            },
            "theme": {
                "color": "#2ec1ac"
            },
            "modal": {
                "ondismiss": function(){
                    window.location.href = 'pay_status.php?order=<?php echo $ORDER_ID; ?>&status=failed';
                }
            }
        };
        var rzp1 = new Razorpay(options);
        window.onload = function(){
            rzp1.open();
        };
    </script>
</body>
</html>