<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  if(isset($_POST['add_room']))
  {
    $features = json_decode($_POST['features']);
    $facilities = json_decode($_POST['facilities']);

    $frm_data = filteration($_POST);
    $flag = 0;

    // Insert room data into 'rooms' table
    $q1 = "INSERT INTO `rooms` (`name`, `area`, `price`, `quantity`, `adult`, `children`, `description`) VALUES (?,?,?,?,?,?,?)";
    $values = [$frm_data['name'],$frm_data['area'],$frm_data['price'],$frm_data['quantity'],$frm_data['adult'],$frm_data['children'],$frm_data['desc']];

    if(insert($q1,$values,'siiiiis')){
      $flag = 1;
    }

    $room_id = mysqli_insert_id($con);

    // Insert facilities into 'room_facilities' table
    $q2 = "INSERT INTO `room_facilities` (`room_id`, `facilities_id`) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q2))
    {
      foreach($facilities as $f){
        mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
        mysqli_stmt_execute($stmt);
      }
      mysqli_stmt_close($stmt);
    }
    else {
      $flag = 0;
    }

    // Insert features into 'room_features' table
    $q3 = "INSERT INTO `room_features` (`room_id`, `features_id`) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q3))
    {
      foreach($features as $f){
        mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
        mysqli_stmt_execute($stmt);
      }
      mysqli_stmt_close($stmt);
    }
    else {
      $flag = 0;
    }

    if($flag){
      echo 1;
    }
    else {
      echo 0;
    }

  }

  if(isset($_POST['get_all_rooms']))
  {
    $frm_data = filteration($_POST);

    $limit = 5; 
    $page = isset($frm_data['page']) ? $frm_data['page'] : 1;
    $start = ($page - 1) * $limit;

    $total_q = mysqli_query($con, "SELECT COUNT(*) as total FROM `rooms` WHERE `removed`=0");
    $total_res = mysqli_fetch_assoc($total_q);
    $total_pages = ceil($total_res['total'] / $limit);

    $res = select("SELECT * FROM `rooms` WHERE `removed`=? ORDER BY `id` DESC LIMIT $start, $limit", [0], 'i');
    
    $i = $start + 1;
    $table_data = "";

    while($row = mysqli_fetch_assoc($res))
    {
      $status = ($row['status']==1) 
        ? "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>"
        : "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>inactive</button>";

      $table_data.="
        <tr class='align-middle'>
          <td>$i</td>
          <td>$row[name]</td>
          <td>$row[area] sq. ft.</td>
          <td>Adult: $row[adult]<br>Children: $row[children]</td>
          <td>₹$row[price]</td>
          <td>$row[quantity]</td>
          <td>$status</td>
          <td>
            <button onclick=\"room_images($row[id],'$row[name]')\" class='btn btn-info shadow-none btn-sm text-white' data-bs-toggle='modal' data-bs-target='#room-images'>
              <i class='bi bi-images'></i>
            </button>
            <button onclick='remove_room($row[id])' class='btn btn-danger shadow-none btn-sm'>
              <i class='bi bi-trash'></i>
            </button>
          </td>
        </tr>
      ";
      $i++;
    }
    
    $pagination = "";
    if($total_pages > 1) {
        $disabled = ($page <= 1) ? "disabled" : "";
        $prev = $page - 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='get_all_rooms($prev)' class='page-link shadow-none'>&laquo;</button></li>";

        for($p=1; $p<=$total_pages; $p++) {
            $active = ($p == $page) ? "active" : "";
            $pagination .= "<li class='page-item $active'><button onclick='get_all_rooms($p)' class='page-link shadow-none'>$p</button></li>";
        }

        $disabled = ($page >= $total_pages) ? "disabled" : "";
        $next = $page + 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='get_all_rooms($next)' class='page-link shadow-none'>&raquo;</button></li>";
    }

    echo json_encode(["table_data" => $table_data, "pagination" => $pagination]);
  }

  if(isset($_POST['toggle_status']))
  {
    $frm_data = filteration($_POST);
    $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'],$frm_data['toggle_status']];

    if(update($q,$v,'ii')){
      echo 1;
    }
    else{
      echo 0;
    }
  }

  // --- NEW ADDITIONS BELOW ---

  if(isset($_POST['add_image']))
  {
    $frm_data = filteration($_POST);
    $img_r = uploadImage($_FILES['image'], ROOMS_FOLDER);

    if($img_r == 'inv_img'){
      echo $img_r;
    } else if($img_r == 'inv_size'){
      echo $img_r;
    } else if($img_r == 'upd_failed'){
      echo $img_r;
    } else {
      $q = "INSERT INTO `room_images`(`room_id`, `image`) VALUES (?,?)";
      $values = [$frm_data['room_id'], $img_r];
      $res = insert($q, $values, 'is');
      echo $res;
    }
  }

  if(isset($_POST['get_room_images']))
  {
    $frm_data = filteration($_POST);
    $res = select("SELECT * FROM `room_images` WHERE `room_id`=?", [$frm_data['get_room_images']], 'i');
    
    $path = ROOMS_IMG_PATH;

    while($row = mysqli_fetch_assoc($res))
    {
      $thumb_btn = "<button onclick='thumb_image($row[sr_no], $row[room_id])' class='btn btn-secondary shadow-none btn-sm'>
        <i class='bi bi-check-lg'></i>
      </button>";

      if($row['thumb'] == 1){
        $thumb_btn = "<button class='btn btn-success shadow-none btn-sm' disabled>
          <i class='bi bi-check-lg'></i>
        </button>";
      }

      echo<<<data
        <tr class='align-middle'>
          <td><img src='$path$row[image]' class='img-fluid'></td>
          <td>$thumb_btn</td>
          <td>
            <button onclick='rem_image($row[sr_no], $row[room_id])' class='btn btn-danger shadow-none btn-sm'>
              <i class='bi bi-trash'></i>
            </button>
          </td>
        </tr>
      data;
    }
  }

  if(isset($_POST['rem_image']))
  {
    $frm_data = filteration($_POST);
    $values = [$frm_data['image_id'], $frm_data['room_id']];

    $pre_q = "SELECT * FROM `room_images` WHERE `sr_no`=? AND `room_id`=?";
    $res = select($pre_q, $values, 'ii');
    $img = mysqli_fetch_assoc($res);

    if(deleteImage($img['image'], ROOMS_FOLDER)){
      $q = "DELETE FROM `room_images` WHERE `sr_no`=? AND `room_id`=?";
      $res = delete($q, $values, 'ii');
      echo $res;
    } else {
      echo 0;
    }
  }

  if(isset($_POST['thumb_image']))
  {
    $frm_data = filteration($_POST);

    $pre_q = "UPDATE `room_images` SET `thumb`=? WHERE `room_id`=?";
    $pre_v = [0, $frm_data['room_id']];
    $pre_res = update($pre_q, $pre_v, 'ii');

    $q = "UPDATE `room_images` SET `thumb`=? WHERE `sr_no`=? AND `room_id`=?";
    $v = [1, $frm_data['image_id'], $frm_data['room_id']];
    $res = update($q, $v, 'iii');

    echo $res;
  }

  if(isset($_POST['remove_room']))
  {
    $frm_data = filteration($_POST);

    $res1 = select("SELECT * FROM `room_images` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    while($row = mysqli_fetch_assoc($res1)){
      deleteImage($row['image'], ROOMS_FOLDER);
    }

    $res2 = delete("DELETE FROM `room_images` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res3 = delete("DELETE FROM `room_features` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res4 = delete("DELETE FROM `room_facilities` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res5 = update("UPDATE `rooms` SET `removed`=? WHERE `id`=?", [1, $frm_data['room_id']], 'ii');

    if($res2 || $res3 || $res4 || $res5){
      echo 1;
    } else {
      echo 0;
    }
  }

?>