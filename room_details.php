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
              $login = 0;
              if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                $login = 1;
              }
              echo "<button onclick='checkLoginToBook($login, {$room_data['id']})' class='btn w-100 text-white btn-primary custom-bg shadow-none rounded-pill p-2 fw-bold mb-2'>Book Now</button>";
            ?>
          </div>
        </div>
      </div>

      <div class="col-12 mt-4 mb-5">
        <h5 class="fw-bold">Description</h5>
        <p class="bg-white p-4 rounded shadow-sm text-secondary border-start border-4 border-dark" style="line-height: 1.7;">
          <?php echo $room_data['desc']; ?>
        </p>
      </div>

    </div>
  </div>

  <?php require('include/footer.php'); ?>
</body>
</html>