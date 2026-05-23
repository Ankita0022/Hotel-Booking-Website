<?php
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  if(!isset($_GET['id'])){
    redirect('rooms.php');
  }
  
  $data = filteration($_GET);
  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if(mysqli_num_rows($room_res) == 0){
    redirect('rooms.php');
  }
  $room_data = mysqli_fetch_assoc($room_res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Gaarland - ROOM DETAILS</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container px-4">
    <div class="row">

      <div class="col-12 my-5">
        <h2 class="fw-bold h-font"><?php echo $room_data['name']; ?></h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none fw-bold">HOME</a>
          <span class="text-secondary fw-bold"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none fw-bold">ROOMS</a>
        </div>
      </div>

      <div class="col-lg-7 col-md-12 mb-4">
        <div id="roomCarousel" class="carousel slide shadow-sm rounded bg-white" data-bs-ride="carousel">
          <div class="carousel-inner p-2">
            <?php
              $img_q = select("SELECT * FROM `room_images` WHERE `room_id`=?", [$room_data['id']], 'i');

              if(mysqli_num_rows($img_q) > 0) {
                $active_class = "active";
                while($img_res = mysqli_fetch_assoc($img_q)) {
                  echo "
                    <div class='carousel-item $active_class'>
                      <img src='".ROOMS_IMG_PATH.$img_res['image']."' class='d-block w-100 rounded' style='object-fit: cover;'>
                    </div>
                  ";
                  $active_class = "";
                }
              } else {
                echo "
                  <div class='carousel-item active'>
                    <img src='".ROOMS_IMG_PATH."thumbnail.jpg' class='d-block w-100 rounded'>
                  </div>
                ";
              }
            ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>

      <div class="col-lg-5 col-md-12 mb-4">
        <div class="card border-0 shadow-sm rounded-3 p-3">
          <div class="card-body p-2">
            <h4 class="fw-bold mb-3">₹<?php echo $room_data['price']; ?> per night</h4>
            
            <div class="mb-3">
              <?php
                $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `room_id`='$room_data[id]'";
                $rating_res = mysqli_query($con, $rating_q);
                $rating_fetch = mysqli_fetch_assoc($rating_res);

                $rating_stars = "";
                if($rating_fetch['avg_rating'] != NULL) {
                  $avg_rating = round($rating_fetch['avg_rating']);
                  for($i=0; $i<$avg_rating; $i++) {
                    $rating_stars .= "<i class='bi bi-star-fill text-warning me-1'></i>";
                  }
                  echo '<div class="mb-2">'.$rating_stars.'</div>';
                } else {
                  echo '<div class="text-muted small mb-2"><i class="bi bi-star me-1"></i>No ratings yet</div>';
                }
              ?>
            </div>

            <div class="mb-3">
              <h6 class="mb-1 fw-bold">Features</h6>
              <?php
                $fea_q = "SELECT f.name FROM `features` f 
                          INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
                          WHERE rfea.room_id = ?";
                $fea_res = select($fea_q, [$room_data['id']], 'i');
                while($fea_row = mysqli_fetch_assoc($fea_res)) {
                  echo "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border'>{$fea_row['name']}</span>";
                }
              ?>
            </div>

            <div class="mb-3">
              <h6 class="mb-1 fw-bold">Facilities</h6>
              <?php
                $fac_q = "SELECT f.name FROM `facilities` f 
                          INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
                          WHERE rfac.room_id = ?";
                $fac_res = select($fac_q, [$room_data['id']], 'i');
                while($fac_row = mysqli_fetch_assoc($fac_res)) {
                  echo "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border'>{$fac_row['name']}</span>";
                }
              ?>
            </div>

            <div class="mb-3">
              <h6 class="mb-1 fw-bold">Guests</h6>
              <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border">Adults: <?php echo $room_data['adult']; ?></span>
              <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border">Children: <?php echo $room_data['children']; ?></span>
            </div>

            <div class="mb-4">
              <h6 class="mb-1 fw-bold">Area</h6>
              <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border"><?php echo $room_data['area']; ?> sq. ft.</span>
            </div>

            <?php
              $is_available = true;
              $today = date('Y-m-d');
              $tomorrow = date('Y-m-d', strtotime('+1 day'));
              $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order` 
                  WHERE booking_status='booked' AND room_id=? 
                  AND check_in < ? AND check_out > ?";
              $tb_res = select($tb_query, [$room_data['id'], $tomorrow, $today], 'iss');
              $tb_fetch = mysqli_fetch_assoc($tb_res);

              if($tb_fetch['total_bookings'] >= $room_data['quantity']) {
                  $is_available = false;
              }

              $book_btn = "";
              if($settings_r['shutdown']){
                  $book_btn = "<button class='btn w-100 btn-danger shadow-none rounded-pill p-2 fw-bold mb-2' disabled>Bookings Closed</button>";
              } else if(!$is_available) {
                  $book_btn = "<button class='btn w-100 btn-danger shadow-none rounded-pill p-2 fw-bold mb-2' disabled>Not Available</button>";
              } else {
                  $login = 0;
                  if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                    $login = 1;
                  }
                  $book_btn = "<button onclick='checkLoginToBook($login, {$room_data['id']})' class='btn w-100 text-white btn-primary custom-bg shadow-none rounded-pill p-2 fw-bold mb-2'>Book Now</button>";
              }
              echo $book_btn;
            ?>
          </div>
        </div>
      </div>

      <div class="col-12 mt-4 mb-4">
        <h5 class="fw-bold">Description</h5>
        <p class="bg-white p-4 rounded shadow-sm text-secondary border-start border-4 border-dark" style="line-height: 1.7;">
          <?php echo $room_data['description']; ?>
        </p>
      </div>

      <div class="col-12 mt-3 mb-5">
        <h5 class="fw-bold mb-3">Reviews & Ratings</h5>
        
        <div class="bg-white p-4 rounded shadow-sm">
          <?php
            $review_q = "SELECT rr.*, uc.name AS user_name, uc.profile AS user_pic FROM `rating_review` rr 
                         INNER JOIN `user_cred` uc ON rr.user_id = uc.id 
                         WHERE rr.room_id = ? ORDER BY rr.sr_no DESC";
            
            $review_res = select($review_q, [$room_data['id']], 'i');

            if(mysqli_num_rows($review_res) == 0) {
              echo '<div class="text-secondary py-2"><i class="bi bi-chat-square-text me-2"></i>No reviews submitted for this room choice yet.</div>';
            } else {
              while($rev_row = mysqli_fetch_assoc($review_res)) {
                // Build matching dynamic star layouts per item row entry
                $stars = "";
                for($i=0; $i<$rev_row['rating']; $i++) {
                  $stars .= "<i class='bi bi-star-fill text-warning small me-1'></i>";
                }

                // Fallback avatar handling path matching your user account properties
                $user_avatar = ($rev_row['user_pic'] != '') ? USERS_IMG_PATH.$rev_row['user_pic'] : "images/users/thumbnail.jpg";
                $rev_date = date("d-m-Y", strtotime($rev_row['datentime']));

                echo "
                  <div class='review-item mb-4 border-bottom pb-3'>
                    <div class='d-flex align-items-center mb-2'>
                      <img src='$user_avatar' class='rounded-circle me-2 border' style='width: 35px; height: 35px; object-fit: cover;'>
                      <h6 class='m-0 fw-bold small text-dark'>{$rev_row['user_name']}</h6>
                      <span class='text-muted ms-auto super-small bg-light px-2 py-0.5 border rounded' style='font-size: 11px;'>$rev_date</span>
                    </div>
                    <div class='rating mb-2'>$stars</div>
                    <p class='text-secondary small m-0' style='line-height: 1.6;'>{$rev_row['review']}</p>
                  </div>
                ";
              }
            }
          ?>
        </div>
      </div>

    </div>
  </div>

  <?php require('include/footer.php'); ?>
</body>
</html>