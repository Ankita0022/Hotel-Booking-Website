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
  <title>Admin Panel | Booking Records</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden"> 
        
        <h3 class="mb-4 text-center">BOOKING RECORDS</h3>
        
        <div class="row justify-content-center">
          <div class="col-lg-11">

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">

                <div class="text-end mb-4">
                  <input type="text" oninput="get_records(1, this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
                </div>

                <div class="table-responsive"> 
                  <table class="table table-hover border text-center" style="min-width: 1200px;">
                    <thead>
                      <tr class="bg-dark text-light sticky-top">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Booking Details</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="table-data">
                      </tbody>
                  </table>
                </div>

                <div class="d-flex align-items-center justify-content-end mt-3">
                  <nav aria-label="Page navigation">
                    <ul class="pagination mb-0" id="table-pagination">
                      </ul>
                  </nav>
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
    function get_records(page=1, search='') {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/booking_records_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        document.getElementById('table-data').innerHTML = res.table_data;
        document.getElementById('table-pagination').innerHTML = res.pagination;
      }
      xhr.send('get_records&page='+page+'&search='+search);
    }

    window.onload = function() {
      get_records();
    }
  </script>

</body>
</html>