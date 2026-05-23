<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  // Mark specific reviews as read/seen dynamically
  if(isset($_GET['seen'])) {
      $frm_data = filteration($_GET);
      
      if($frm_data['seen'] == 'all') {
          $q = "UPDATE `rating_review` SET `seen`=?";
          update($q, [1], 'i');
          alert('success', 'All feedback items marked read.');
      } else {
          $q = "UPDATE `rating_review` SET `seen`=? WHERE `sr_no`=?";
          update($q, [1, $frm_data['seen']], 'ii');
          alert('success', 'Feedback item marked read.');
      }
  }

  // Handle review deletion requests securely
  if(isset($_GET['del'])) {
      $frm_data = filteration($_GET);
      
      if($frm_data['del'] == 'all') {
          $q = "TRUNCATE TABLE `rating_review`";
          mysqli_query($con, $q);
          alert('success', 'All logs deleted from database.');
      } else {
          $q = "DELETE FROM `rating_review` WHERE `sr_no`=?";
          delete($q, [$frm_data['del']], 'i');
          alert('success', 'Selected entry dropped.');
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Ratings & Reviews</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid px-4" id="main-content">
    <div class="row">
      <div class="col-lg-12 p-4 overflow-hidden">
        
        <h3 class="fw-bold h-font mb-4">RATINGS & REVIEWS</h3>

        <div class="card border-0 shadow-sm rounded-3 bg-white">
          <div class="card-body p-3">
            
            <div class="text-end mb-4">
              <a href="?seen=all" class="btn btn-sm btn-dark shadow-none rounded-pill px-3"><i class="bi bi-check-all me-1"></i> Mark all read</a>
              <a href="?del=all" class="btn btn-sm btn-danger shadow-none rounded-pill px-3"><i class="bi bi-trash3 me-1"></i> Delete all logs</a>
            </div>

            <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
              <table class="table table-hover border text-center">
                <thead class="sticky-top bg-dark text-white">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Room Name</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Rating Scale</th>
                    <th scope="col" width="30%">Review Comment</th>
                    <th scope="col">Date Logged</th>
                    <th scope="col">Action Management</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $q = "SELECT rr.*, r.name AS room_name, uc.name AS user_name FROM `rating_review` rr 
                          INNER JOIN `rooms` r ON rr.room_id = r.id 
                          INNER JOIN `user_cred` uc ON rr.user_id = uc.id 
                          ORDER BY rr.sr_no DESC";
                    
                    $res = mysqli_query($con, $q);
                    $i = 1;

                    while($row = mysqli_fetch_assoc($res)) {
                        $date = date("d-m-Y", strtotime($row['datentime']));
                        
                        $stars = "";
                        for($x=0; $x<$row['rating']; $x++) {
                            $stars .= "⭐";
                        }

                        $seen_btn = "";
                        if($row['seen'] == 0) {
                            $seen_btn = "<a href='?seen={$row['sr_no']}' class='btn btn-sm btn-primary shadow-none rounded-pill px-2.5 me-2'>Mark Read</a>";
                        }

                        echo "
                          <tr class='align-middle'>
                            <td>$i</td>
                            <td class='fw-bold'>{$row['room_name']}</td>
                            <td>{$row['user_name']}</td>
                            <td class='text-warning fw-bold text-nowrap'>$stars</td>
                            <td class='text-start text-wrap small'>{$row['review']}</td>
                            <td>$date</td>
                            <td class='text-nowrap'>
                              $seen_btn
                              <a href='?del={$row['sr_no']}' class='btn btn-sm btn-danger shadow-none rounded-pill px-2.5'>Delete</a>
                            </td>
                          </tr>
                        ";
                        $i++;
                    }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/script.php'); ?>
</body>
</html>