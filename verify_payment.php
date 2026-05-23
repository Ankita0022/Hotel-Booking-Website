<?php
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  session_start();

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
      redirect('rooms.php');
  }

  if (isset($_POST['razorpay_payment_id']) && isset($_POST['shopping_order_id'])) {
    $frm_data = filteration($_POST);

    $razorpay_payment_id = $frm_data['razorpay_payment_id'];
    $order_id = $frm_data['shopping_order_id'];

    // Fetch the total price to migrate it into trans_amt tracking
    $order_q = select("SELECT `total_pay` FROM `booking_order` WHERE `order_id`=? LIMIT 1", [$order_id], 's');
    $order_fetch = mysqli_fetch_assoc($order_q);
    $final_amt = $order_fetch['total_pay'];

    // Update status to 'booked' and register the payment amount
    $query = "UPDATE `booking_order` SET `booking_status`='booked', `trans_id`=?, `trans_amt`=? WHERE `order_id`=?";
    $values = [$razorpay_payment_id, $final_amt, $order_id];
    
    if (update($query, $values, 'sis')) {
        redirect('pay_status.php?order=' . $order_id);
    } else {
        redirect('pay_status.php?order=' . $order_id);
    }
} else {
    redirect('rooms.php');
}
?>