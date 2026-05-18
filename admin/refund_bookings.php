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
  <title>Admin Panel | Refund Bookings</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden"> 
        
        <h3 class="mb-4 text-center">REFUND BOOKINGS</h3>
        
        <div class="row justify-content-center">
          <div class="col-lg-11">

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">

                <div class="text-end mb-4">
                  <input type="text" oninput="get_refunds(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
                </div>

                <div class="table-responsive"> 
                  <table class="table table-hover border text-center" style="min-width: 1200px;">
                    <thead>
                      <tr class="bg-dark text-light sticky-top">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Refund Amount</th>
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

  <?php require('inc/script.php'); ?>

  <script>
    function get_refunds(search='') {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/refund_bookings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        document.getElementById('table-data').innerHTML = res.table_data;
      }
      xhr.send('get_refunds&search='+search);
    }

    function clear_refund(id) {
      if(confirm("Mark this refund transaction log entry as complete and archive it?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/refund_bookings_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if(this.responseText == 1) {
            alert('success', 'Refund log entry successfully closed and archived!');
            get_refunds();
          } else {
            alert('error', 'Operation pipeline execution failed.');
          }
        }
        xhr.send('clear_refund=&booking_id='+id);
      }
    }

    window.onload = function() {
      get_refunds();
    }
  </script>

</body>
</html>