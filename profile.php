<?php 
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  
  session_start();

  // Route unauthenticated profile view lookups away safely
  if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('index.php');
  }

  // Fetch current user details
  $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
  if(mysqli_num_rows($u_exist) == 0){
    redirect('logout.php');
  }
  $u_data = mysqli_fetch_assoc($u_exist);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Gaarland - PROFILE</title>
  <?php require('include/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('include/header.php'); ?>

  <div class="container my-5 px-4" id="main-content">
    <div class="row">

      <div class="col-12 my-4 pt-4 mb-4">
        <h2 class="fw-bold h-font">PROFILE</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none fw-bold">HOME</a>
          <span class="text-secondary fw-bold"> > </span>
          <a href="javascript:void(0)" class="text-secondary text-decoration-none fw-bold">PROFILE</a>
        </div>
      </div>

      <!-- Left Column: Profile Pic & Password -->
      <div class="col-lg-4 col-md-5 mb-5 px-4">
        <div class="card border-0 shadow rounded-3 mb-4 bg-white">
          <div class="card-body text-center p-4">
            <h5 class="mb-4 fw-bold h-font"><i class="bi bi-person-circle me-2"></i>PROFILE PICTURE</h5>
            <img src="<?php echo USERS_IMG_PATH.$u_data['profile']; ?>" class="rounded-circle img-fluid mb-4 shadow-sm border border-3 border-light" style="width: 170px; height: 170px; object-fit: cover;">
            
            <form id="profile-form">
              <div class="mb-3 text-start">
                <label class="form-label fw-bold text-secondary">Select New Image</label>
                <input type="file" name="profile" accept=".jpg, .jpeg, .png, .webp" class="form-control shadow-none border-2" required>
              </div>
              <button type="submit" class="btn custom-bg text-white shadow-none rounded-pill w-100 fw-bold"><i class="bi bi-camera-fill me-2"></i>Update Picture</button>
            </form>
          </div>
        </div>

        <div class="card border-0 shadow rounded-3 bg-white">
          <div class="card-body p-4">
            <h5 class="mb-4 fw-bold h-font"><i class="bi bi-shield-lock-fill me-2"></i>CHANGE PASSWORD</h5>
            
            <form id="pass-form">
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary">New Password</label>
                <input type="password" name="new_pass" class="form-control shadow-none border-2" placeholder="Enter new password" required>
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Confirm Password</label>
                <input type="password" name="confirm_pass" class="form-control shadow-none border-2" placeholder="Confirm new password" required>
              </div>
              <button type="submit" class="btn custom-bg text-white shadow-none rounded-pill w-100 fw-bold"><i class="bi bi-lock-fill me-2"></i>Update Password</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Right Column: Basic Info -->
      <div class="col-lg-8 col-md-7 mb-5 px-4">
        <div class="card border-0 shadow rounded-3 p-4 bg-white h-100">
          <div class="card-body">
            <h5 class="mb-4 fw-bold h-font"><i class="bi bi-card-list me-2"></i>BASIC INFORMATION</h5>
            
            <form id="info-form">
              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="form-label fw-bold text-secondary"><i class="bi bi-person-fill me-1"></i> Name</label>
                  <input type="text" name="name" value="<?php echo $u_data['name']; ?>" class="form-control shadow-none border-2" required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label fw-bold text-secondary"><i class="bi bi-telephone-fill me-1"></i> Phone Number</label>
                  <input type="number" name="phonenum" value="<?php echo $u_data['phonenum']; ?>" class="form-control shadow-none border-2" required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label fw-bold text-secondary"><i class="bi bi-calendar-date-fill me-1"></i> Date of Birth</label>
                  <input type="date" name="dob" value="<?php echo $u_data['dob']; ?>" class="form-control shadow-none border-2" required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label fw-bold text-secondary"><i class="bi bi-geo-alt-fill me-1"></i> Pincode</label>
                  <input type="number" name="pincode" value="<?php echo $u_data['pincode']; ?>" class="form-control shadow-none border-2" required>
                </div>
                <div class="col-md-12 mb-4">
                  <label class="form-label fw-bold text-secondary"><i class="bi bi-house-door-fill me-1"></i> Address</label>
                  <textarea name="address" rows="5" class="form-control shadow-none border-2" required><?php echo $u_data['address']; ?></textarea>
                </div>
              </div>
              <div class="text-end mt-2">
                <button type="submit" class="btn custom-bg text-white shadow-none rounded-pill px-5 py-2 fw-bold"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Save Changes</button>
              </div>
            </form>

          </div>
        </div>
      </div>

    </div>
  </div>

  <?php require('include/footer.php'); ?>

  <script>
    // Handle Profile Text Fields Updates
    let info_form = document.getElementById('info-form');
    info_form.addEventListener('submit', function(e){
      e.preventDefault();
      let data = new FormData(info_form);
      data.append('info_form', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function(){
        if(this.responseText == 1){
          alert('success', 'Profile changes saved successfully!');
        } else if(this.responseText == 'phone_already') {
          alert('error', 'This mobile number is already registered!');
        } else {
          alert('error', 'No alterations detected.');
        }
      }
      xhr.send(data);
    });

    // Handle Profile Avatar Binary Uploads
    let profile_form = document.getElementById('profile-form');
    profile_form.addEventListener('submit', function(e){
      e.preventDefault();
      let data = new FormData(profile_form);
      data.append('profile_form', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function(){
        if(this.responseText == 'inv_img'){
          alert('error', 'Only JPG, WEBP, and PNG image extensions are supported!');
        } else if(this.responseText == 'upd_failed'){
          alert('error', 'Avatar file compilation processing failed.');
        } else if(this.responseText == 1){
          alert('success', 'Profile picture updated successfully!');
          window.location.reload();
        } else {
          alert('error', 'Server Error.');
        }
      }
      xhr.send(data);
    });

    // Asynchronous Reset Password Form Controller Logic
    let pass_form = document.getElementById('pass-form');
    pass_form.addEventListener('submit', function(e){
      e.preventDefault();
      
      let new_pass = pass_form.elements['new_pass'].value;
      let confirm_pass = pass_form.elements['confirm_pass'].value;

      if(new_pass !== confirm_pass) {
        alert('error', 'Passwords do not match!');
        return;
      }

      let data = new FormData(pass_form);
      data.append('pass_form', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/profile.php", true);
      xhr.onload = function(){
        if(this.responseText == 1){
          alert('success', 'Your password has been changed successfully!');
          pass_form.reset();
        } else {
          alert('error', 'Internal system update execution error.');
        }
      }
      xhr.send(data);
    });
  </script>
</body>
</html>