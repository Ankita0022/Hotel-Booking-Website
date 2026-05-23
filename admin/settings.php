 <?php
  require('inc/essentials.php');
  adminLogin();
  session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | Settings</title>
  <?php require('inc/links.php'); ?>

</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>


  <div class="container-fluid" id="main-content">
    <div class="row">
        <div class="col-lg-12 p-4 overflow-hidden">
        <h3 class="mb-4">Settings</h3>

        <!-- general settings -->
        <div class="card shadow-0 mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">General Settings</h5>
              <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#general-s">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
            </div>
          
            <h6 class="card-subtitle mb-1 fw-bold">Side Title</h6>
            <p class="card-text" id="site_title"></p>

            <h6 class="card-subtitle mb-1 fw-bold">About Us</h6>
            <p class="card-text" id="site_about"></p>
          
          </div>
        </div>

        <!-- shutdown settings -->
         <div class="card border-0 shadow-0 mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">Shutdown Website</h5>
              <div class="form-check form-switch">
                  <input onchange="upd_shutdown(this.value)" class="form-check-input" type="checkbox" id="shutdown-toggle">
                  <label class="form-check-label" for="shutdown-toggle"></label>
              </div>
            </div>
            <p class="card-text">
              No customer will be alloowed to book hotel room when shutdown mode is active. Admin can access the dashboard and turn off the shutdown mode. It is useful when you want to do some maintenance work on website or hotel.
            </p>
          
          </div>
        </div>

        <!-- general settings modal -->
        <div class="modal fade" id="general-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="general_s_form">
              <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">General Settings</h5>
                
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label fw-bold">Site Title</label>
                  <input type="text" name="site_title" id="site_title_inp" class="form-control shadow-none" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Address</label>
                  <textarea name="site_about" id="site_about_inp" class="form-control shadow-none" row="6" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary text-white shadow-none">Save</button>
            </div>
            </div>
            </form>
          </div>
        </div>

        <!-- contact details --> 
         <div class="card shadow-0 mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">Contacts Settings</h5>
              <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#contacts-s">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">Address</h6>
                  <p class="card-text" id="address"></p>
                </div>
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">Google Map</h6>
                  <p class="card-text" id="gmap"></p>
                </div>
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">Phone No.</h6>
                  <p class="card-text mb-1"><i class="bi bi-telephone-fill"></i>
                    <span id="pn1"></span>
                  </p>
                  <p class="card-text"><i class="bi bi-telephone-fill"></i>
                    <span id="pn2"></span>
                  </p>
                </div>
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">Email</h6>
                  <p class="card-text" id="email"></p>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">Social Links</h6>
                  <p class="card-text mb-1">
                    <i class="bi bi-facebook me-1"></i>
                    <span id="fb"></span>
                  </p>
                  <p class="card-text mb-1">
                    <i class="bi bi-instagram me-1"></i>
                    <span id="insta"></span>
                  </p>
                  <p class="card-text">
                    <i class="bi bi-twitter me-1"></i>
                    <span id="tw"></span>
                  </p>
                </div>
                <div class="mb-4">
                  <h6 class="card-subtitle mb-1 fw-bold">iFrames</h6>
                    <iframe id="iframe" class="border p-2 w-100" loading="lazy"></iframe>
                </div>
              </div>
            </div>
          
          </div>
        </div>
        

        <!-- contacts details modal -->
        <div class="modal fade" id="contacts-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <form id="contacts_s_form">
              <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Contact Settings</h5> 
              </div>

              <div class="modal-body">
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <input type="text" name="address" id="address_inp" class="form-control shadow-none" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">Google Map Link</label>
                        <input type="text" name="gmap" id="gmap_inp" class="form-control shadow-none" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">Phone Numbers (with Country code)</label>
                        <div class="input-group mb-3">
                          <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                          <input type="number" name="pn1" id="pn1_inp" class="form-control shadow-none" required>
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                          <input type="number" name="pn2" id="pn2_inp" class="form-control shadow-none">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="email_inp" class="form-control shadow-none" required>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label fw-bold">Social Media Links</label>
                        <div class="input-group mb-3">
                          <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                          <input type="text" name="fb" id="fb_inp" class="form-control shadow-none" required>
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                          <input type="text" name="insta" id="insta_inp" class="form-control shadow-none">
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                          <input type="text" name="tw" id="tw_inp" class="form-control shadow-none">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">iFrame src</label>
                        <input type="text" name="iframe" id="iframe_inp" class="form-control shadow-none" required>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              <div class="modal-footer">
                <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary text-white shadow-none">Save</button>
            </div>
            </div>
            </form>
          </div>
        </div>

        <!-- management team settings -->
        <div class="card shadow-0 mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">Management Team</h5>
              <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#team-s">
                <i class="bi bi-plus-square"></i> Add
              </button>
            </div>

            <div class="row" id="team-data">             
            </div>

            </div>
          </div>
        </div>

        <!-- management team settings modal -->
        <div class="modal fade" id="team-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="team_s_form">
              <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Team Members</h5>
                
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label fw-bold">Name</label>
                  <input type="text" name="member_name" id="member_name_inp" class="form-control shadow-none" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Picture</label>
                  <input type="file" name="member_picture" id="member_picture_inp" accept=".jpg,.png,.webp,.jpeg" class="form-control shadow-none" required>
                </div>
              </div>
                <div class="modal-footer">
                  <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary text-white shadow-none">Save</button>
              </div>
            </div>
            </form>
          </div>
        </div>


      </div>
    </div>
  </div>

  <?php require('inc/script.php'); ?>
  <script>
    let general_data, contacts_data;

    let general_s_form = document.getElementById('general_s_form');
    let site_title_inp = document.getElementById('site_title_inp');
    let site_about_inp = document.getElementById('site_about_inp');

    let contacts_s_form = document.getElementById('contacts_s_form');

    let team_s_form = document.getElementById('team_s_form');
    let member_name_inp = document.getElementById('member_name_inp');
    member_picture_inp = document.getElementById('member_picture_inp');

    function get_general() {
      let site_title = document.getElementById('site_title');
      let site_about = document.getElementById('site_about');

      let shutdown_toggle = document.getElementById('shutdown-toggle');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
      xhr.onload = function() {
        general_data = JSON.parse(this.responseText);
        site_title.innerText = general_data.site_title;
        site_about.innerText = general_data.site_about;

        site_title_inp.value = general_data.site_title;
        site_about_inp.value = general_data.site_about;

        if(general_data.shutdown == 0) {
          shutdown_toggle.checked = false;
          shutdown_toggle.value = 0;
        } else {
          shutdown_toggle.checked = true;
          shutdown_toggle.value = 1;
        }
        
      }

      xhr.send('get_general');
    }

    general_s_form.addEventListener('submit',function(e){
      e.preventDefault();
      upd_general(site_title_inp.value, site_about_inp.value);
    })

    function upd_general(site_title_val, site_about_val) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        let myModalEl = document.getElementById('general-s'); 
        let modal = bootstrap.Modal.getInstance(myModalEl); 
        modal.hide();

        if (this.responseText == 1) {
          alert('success', 'Settings updated!');
          get_general();
        } else {
          alert('error', 'Failed to update settings.');
        }
      }
      
      xhr.send('site_title='+site_title_val+'&site_about='+site_about_val+'&upd_general');
    }


    function upd_shutdown(val) {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
          if (this.responseText == 1 && val == 0) {
              alert('success', 'Site has been shutdown!');
          } else if (this.responseText == 1 && val == 1) {
              alert('success', 'Shutdown mode turned off!');
          } else {
              alert('error', 'Update failed!');
          }
          get_general();
      }
      xhr.send('upd_shutdown=' + val);
    }

    function get_contacts() {
      let contacts_p_id = ['address', 'gmap', 'pn1', 'pn2','email', 'fb','insta','tw'];
      let iframe = document.getElementById('iframe');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
      xhr.onload = function() {
        contacts_data = JSON.parse(this.responseText);
        contacts_data = Object.values(contacts_data);

        for(i=0;i<contacts_p_id.length;i++){
          document.getElementById(contacts_p_id[i]).innerText =contacts_data[i+1];
        }
        iframe.src = contacts_data[9];
        contacts_inp(contacts_data);
      }

      xhr.send('get_contacts');
    }

    function contacts_inp(data){
      let contacts_inp_id =['address_inp', 'gmap_inp', 'pn1_inp', 'pn2_inp','email_inp', 'fb_inp','insta_inp','tw_inp','iframe_inp'];
      for(i=0;i<contacts_inp_id.length;i++){
        document.getElementById(contacts_inp_id[i]).value = data[i+1];
      }
    }

    contacts_s_form.addEventListener('submit',function(e){
      e.preventDefault();
      upd_contacts();
    })

    function upd_contacts(){
      let index = ['address','gmap','pn1','pn2','email','fb','insta','tw','iframe'];
      let contacts_inp_id = ['address_inp','gmap_inp','pn1_inp','pn2_inp','email_inp','fb_inp','insta_inp','tw_inp','iframe_inp'];
      let data_str="";

      for(i=0;i<index.length;i++){
        data_str += index[i] + "=" +document.getElementById(contacts_inp_id[i]).value + '&';
      }
      data_str += "upd_contacts";

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function(){
        let myModalEl = document.getElementById('contacts-s'); 
        let modal = bootstrap.Modal.getInstance(myModalEl); 
        modal.hide();

        if (this.responseText == 1) {
          alert('success','Changes saved!');
          get_contacts();
        } else {
          alert('error', 'No Changes made!');
        }
        get_general();
      }
      xhr.send(data_str);
    }

    team_s_form.addEventListener('submit',function(e){
      e.preventDefault();
      add_member();
    })

    function add_member(){
      let data = new FormData();
      data.append('name',member_name_inp.value);
      data.append('picture',member_picture_inp.files[0]);
      data.append('add_member','')

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);

      xhr.onload = function() { 
        let myModalEl = document.getElementById('team-s'); 
        let modal = bootstrap.Modal.getInstance(myModalEl); 
        modal.hide();

        if (this.responseText == 'inv_img'){
          alert('error', 'Only JPG and PNF images are allowed!');
        } else if(this.responseText == 'inv_size'){
          alert('error', 'Image should be less than 2MB!');
        }else if(this.responseText == 'upd_failed'){
          alert('error','Image upload failed. Server Down!');
        }else{
          alert('success', 'New member added!');
          member_name_inp.value='';
          member_picture_inp.value='';
          get_members();
          //function to show members
        }
      }
      xhr.send(data);
    }
    
    function get_members(){
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        document.getElementById('team-data').innerHTML = this.responseText;
        }
        xhr.send('get_members');
    }

    function rem_member(val){
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/settings_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
      xhr.onload = function() {
        if(this.responseText==1){
          alert('success','Member Removed!');
          get_members();
        }else{
          alert('error','Server down!');
        }
      }
      xhr.send('rem_member='+val);
    }


    window.onload = function() {
      get_general();
      get_contacts();
      get_members();
    }

  </script>
  
</body>
</html>