<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | New Bookings</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden"> 
        
        <h3 class="mb-4 text-center">NEW BOOKINGS</h3>
        
        <div class="row justify-content-center">
          <div class="col-lg-11">

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">

                <div class="text-end mb-4">
                  <input type="text" id="search_input" oninput="get_bookings(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
                </div>

                <div class="table-responsive"> 
                  <table class="table table-hover border text-center" style="min-width: 1200px;">
                    <thead>
                      <tr class="bg-dark text-light sticky-top">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Booking Details</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="table-data">
                      </tbody>
                  </table>
                </div>

              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="assign-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
    <div class="modal-dialog">
      <form id="assign_room_form">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Assign Physical Room</h5>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Room Number</label>
              <input type="text" name="room_no" class="form-control shadow-none" required>
            </div>
            <input type="hidden" name="booking_id">
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary text-white shadow-none">Assign</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php require('inc/script.php'); ?>

  <script>
    function get_bookings(search='') {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/new_bookings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        document.getElementById('table-data').innerHTML = res.table_data;
      }
      xhr.send('get_new_bookings&search='+search);
    }

    function assign_room_modal(id) {
      assign_room_form.elements['booking_id'].value = id;
    }

    let assign_room_form = document.getElementById('assign_room_form');
    assign_room_form.addEventListener('submit', function(e) {
      e.preventDefault();

      let data = new FormData();
      data.append('booking_id', assign_room_form.elements['booking_id'].value);
      data.append('room_no', assign_room_form.elements['room_no'].value);
      data.append('assign_room', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/new_bookings_crud.php", true);

      xhr.onload = function() {
        let modal = bootstrap.Modal.getInstance(document.getElementById('assign-room'));
        modal.hide();

        if(this.responseText == 1) {
          alert('success', 'Physical Room allocated successfully!');
          assign_room_form.reset();
          get_bookings();
        } else {
          alert('error', 'Server Error. Allocation failed!');
        }
      }
      xhr.send(data);
    });

    function cancel_booking(id) {
      if(confirm("Are you sure you want to cancel this booking? This will trigger an automatic Razorpay refund.")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/new_bookings_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if(this.responseText == 1) {
            alert('success', 'Booking cancelled. Refund transaction processed!');
            get_bookings();
          } else {
            alert('error', 'Automatic refund processing execution failure.');
          }
        }
        xhr.send('cancel_booking=&booking_id='+id);
      }
    }

    window.onload = function() {
      get_bookings();
    }
  </script>

</body>
</html>