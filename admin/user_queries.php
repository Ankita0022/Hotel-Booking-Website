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
  <title>Admin Panel | User Queries</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden">
        <h3 class="mb-4">USER QUERIES</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="text-end mb-4">
              <button onclick="bulk_action('seen','all')" class="btn btn-dark rounded-pill shadow-none btn-sm">
                <i class="bi bi-check-all"></i> Mark all read</button>
              <button onclick="bulk_action('del','all')" class="btn btn-danger rounded-pill shadow-none btn-sm">
                <i class="bi bi-trash"></i> Delete all</button>
            </div>

            <div class="table-responsive-md">
              <table class="table table-hover border text-center">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">UUID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col" width="20%">Subject</th>
                    <th scope="col" width="25%">Message</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody id="queries-data">
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

  <?php require('inc/script.php'); ?>
  
  <script>
    function get_queries(page = 1) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/user_queries_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let res = JSON.parse(this.responseText);
        document.getElementById('queries-data').innerHTML = res.table_data;
        document.getElementById('table-pagination').innerHTML = res.pagination;
      }
      xhr.send('get_queries&page=' + page);
    }

    function update_status(id, val) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/user_queries_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        if (this.responseText == 1) {
          alert('success', 'Marked as read');
          get_queries();
        } else {
          alert('error', 'Operation failed');
        }
      }
      xhr.send('update_status=' + id + '&val=' + val);
    }

    function rem_query(id) {
      if (confirm("Are you sure you want to delete this query?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/user_queries_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if (this.responseText == 1) {
            alert('success', 'Data deleted');
            get_queries();
          } else {
            alert('error', 'Deletion failed');
          }
        }
        xhr.send('rem_query=' + id);
      }
    }

    function bulk_action(type, val) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/user_queries_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        if (this.responseText == 1) {
          alert('success', 'Operation successful');
          get_queries();
        } else {
          alert('error', 'Operation failed');
        }
      }
      xhr.send('bulk_action&type=' + type + '&val=' + val);
    }

    window.onload = function() {
      get_queries();
    }
  </script>
</body>
</html> 