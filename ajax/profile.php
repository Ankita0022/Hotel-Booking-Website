<?php
  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  
  if(session_status() == PHP_SESSION_NONE) session_start();

  // Handle Text Info Profile updates
  if(isset($_POST['info_form'])) {
      $frm_data = filteration($_POST);

      // Verify that the chosen phone number isn't taken by an alternate client profile entry
      $phone_chk = select("SELECT * FROM `user_cred` WHERE `phonenum`=? AND `id` != ? LIMIT 1", [$frm_data['phonenum'], $_SESSION['uId']], "si");
      
      if(mysqli_num_rows($phone_chk) > 0) {
          echo 'phone_already';
          exit;
      }

      $query = "UPDATE `user_cred` SET `name`=?, `phonenum`=?, `dob`=?, `pincode`=?, `address`=? WHERE `id`=?";
      $values = [$frm_data['name'], $frm_data['phonenum'], $frm_data['dob'], $frm_data['pincode'], $frm_data['address'], $_SESSION['uId']];

      if(update($query, $values, 'sssssi')) {
          $_SESSION['uName'] = $frm_data['name'];
          echo 1;
      } else {
          echo 0;
      }
  }

  // Handle Binary Upload Profile Avatar image updates
  if(isset($_POST['profile_form'])) {
      // Execute the native user upload utility function defined inside essentials.php
      $img = uploadUserImage($_FILES['profile']);

      if($img == 'inv_img') {
          echo 'inv_img';
          exit;
      } else if($img == 'upd_failed') {
          echo 'upd_failed';
          exit;
      }

      // Drop old image file to save disk space
      $u_exist = select("SELECT `profile` FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
      $u_data = mysqli_fetch_assoc($u_exist);
      deleteImage($u_data['profile'], USERS_FOLDER);

      // Save the fresh filename reference string index inside the database 
      $query = "UPDATE `user_cred` SET `profile`=? WHERE `id`=?";
      if(update($query, [$img, $_SESSION['uId']], 'si')) {
          $_SESSION['uPic'] = $img;
          echo 1;
      } else {
          echo 0;
      }
  }
?>