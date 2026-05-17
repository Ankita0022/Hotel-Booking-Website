<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Users</title>
    <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">
    <?php require('inc/header.php'); ?>
    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-12 p-4 overflow-hidden">
                <h3 class="mb-4">USERS</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <nav id="table-pagination"></nav>
                            
                            <input type="text" oninput="search_user(this.value)" class="form-control shadow-none w-25" placeholder="Type to search name...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover border text-center" style="min-width: 1300px;">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <th scope="col">UUID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone no.</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">DOB</th>
                                        <th scope="col">Verified</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="users-data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/script.php'); ?>

    <script>
    // Global variable to keep track of search string
    let search_val = "";

    function get_users(page=1) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/users_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            try {
                let data = JSON.parse(this.responseText);
                document.getElementById('users-data').innerHTML = data.table_data;
                document.getElementById('table-pagination').innerHTML = data.pagination;
            } catch (e) {
                console.error("Error parsing JSON:", this.responseText);
            }
        }
        // Send both page and search value to the server
        xhr.send('get_users&page=' + page + '&search=' + search_val);
    }

    function search_user(username) {
        search_val = username;
        get_users(1); // Always go back to page 1 when searching
    }

    function toggle_status(id, val) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/users_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if(this.responseText == 1) {
                alert('success', 'User status changed!');
                get_users(); // Refresh data
            } else {
                alert('error', 'Server Down!');
            }
        }
        xhr.send('toggle_status=' + id + '&value=' + val);
    }

    // New function if you want to add delete functionality
    function remove_user(id) {
        if(confirm("Are you sure you want to remove this user?")) {
            let data = new FormData();
            data.append('user_id', id);
            data.append('remove_user', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/users_crud.php", true);
            xhr.onload = function() {
                if(this.responseText == 1) {
                    alert('success', 'User removed!');
                    get_users();
                } else {
                    alert('error', 'User removal failed!');
                }
            }
            xhr.send(data);
        }
    }

    window.onload = function() {
        get_users();
    }
    </script>
</body>
</html>