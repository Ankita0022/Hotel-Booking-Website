<?php 
  require_once('../admin/inc/db_config.php');
  require_once('../admin/inc/essentials.php');

  if(isset($_GET['check_availability']))
  {
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
    $values = [1];
    $settings_r = mysqli_fetch_assoc(select($settings_q, $values, 'i'));
    
    $chk_data = filteration($_GET);
    $facility_list = json_decode($_GET['facility_list']);

    $query = "SELECT * FROM `rooms` WHERE `status`=? AND `removed`=?";
    $values = [1, 0];
    $types = "ii";

    if($chk_data['adults'] > 0){
        $query .= " AND `adult` >= ?";
        array_push($values, $chk_data['adults']);
        $types .= "i";
    }
    if($chk_data['children'] > 0){
        $query .= " AND `children` >= ?";
        array_push($values, $chk_data['children']);
        $types .= "i";
    }

    $res = select($query, $values, $types);
    $output = "";

    while($room_data = mysqli_fetch_assoc($res))
    {
      if(count($facility_list) > 0){
          $fac_q = select("SELECT COUNT(*) AS 'count' FROM `room_facilities` 
            WHERE `room_id`=? AND `facilities_id` IN (".implode(',',$facility_list).")", 
            [$room_data['id']], 'i');
          $fac_fetch = mysqli_fetch_assoc($fac_q);
          if($fac_fetch['count'] != count($facility_list)) continue;
      }

      $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
        INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
        WHERE rfea.room_id = '$room_data[id]'");
      $features_data = "";
      while($fea_row = mysqli_fetch_assoc($fea_q)){
        $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$fea_row[name]</span>";
      }

      $thumb_res = select("SELECT * FROM `room_images` WHERE `room_id`=? AND `thumb`=?", [$room_data['id'], 1], 'ii');
      $img_path = ROOMS_IMG_PATH . (mysqli_num_rows($thumb_res) > 0 ? mysqli_fetch_assoc($thumb_res)['image'] : "thumbnail.jpg");

      $book_btn = "";
      if($settings_r['shutdown']){
          $book_btn = "<button class='btn btn-sm w-100 btn-danger shadow-none mb-2' disabled>Bookings Closed</button>";
      } else {
          $login = (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 1 : 0;
          $book_btn = "<button onclick='checkLoginToBook($login, $room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Book Now</button>";
      }

      $output .= "
        <div class='card mb-4 shadow border-0'>
          <div class='row g-0 p-3 align-items-center'>
            <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
              <img src='$img_path' class='img-fluid rounded-start'>
            </div>
            <div class='col-md-5 px-lg-3 px-md-3 px-0'>
              <h5 class='mb-3'>$room_data[name]</h5>
              <div class='features mb-3'><h6 class='mb-1'>Features</h6>$features_data</div>
              <div class='guests'>
                <h6 class='mb-1'>Guests</h6>
                <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[adult] Adults</span>
                <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[children] Children</span>
              </div>
            </div>
            <div class='col-md-2 text-center'>
              <h6 class='mb-4'>₹$room_data[price] per night</h6>
              $book_btn
              <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>More details</a>
            </div>
          </div>
        </div>";
    }

    echo $output == "" ? "<h3 class='text-center text-danger'>No rooms found!</h3>" : $output;
  }
?>