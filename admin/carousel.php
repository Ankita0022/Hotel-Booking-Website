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
  <title>Admin Panel | CAROUSEL</title>
  <?php require('inc/links.php'); ?>

</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>


  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">CAROUSEL</h3>


        <!-- Carousel section -->
        <div class="card shadow-0 mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">Images</h5>
              <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#carousel-s">
                <i class="bi bi-plus-square"></i> Add
              </button>
            </div>

            <div class="row" id="carousel-data">             
            </div>

            </div>
          </div>
        </div>

        <!-- Carousel modal -->
        <div class="modal fade" id="carousel-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="carousel_s_form">
              <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Team Members</h5>
                
              </div>
              <div class="modal-body">
                
                <div class="mb-3">
                  <label class="form-label fw-bold">Picture</label>
                  <input type="file" name="carousel_picture" id="carousel_picture_inp" accept=".jpg,.png,.webp,.jpeg" class="form-control shadow-none" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" onclick="carousel_picture.value=''" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn custom-bg text-white shadow-none">Save</button>
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
    
    let carousel_s_form = document.getElementById('carousel_s_form');
    carousel_picture_inp = document.getElementById('carousel_picture_inp');

    carousel_s_form.addEventListener('submit',function(e){
      e.preventDefault();
        add_image()
    })

    function add_image(){
      let data = new FormData();
      data.append('picture',carousel_picture_inp.files[0]);
      data.append('add_image','')

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/carousel_crud.php", true);

      xhr.onload = function() { 
        let myModalEl = document.getElementById('carousel-s'); 
        let modal = bootstrap.Modal.getInstance(myModalEl); 
        modal.hide();

        if (this.responseText == 'inv_img'){
          alert('error', 'Only JPG and PNF images are allowed!');
        } else if(this.responseText == 'inv_size'){
          alert('error', 'Image should be less than 2MB!');
        }else if(this.responseText == 'upd_failed'){
          alert('error','Image upload failed. Server Down!');
        }else{
          alert('success', 'New Image added!');
          carousel_picture_inp.value='';
          get_carousel();
        }
      }
      xhr.send(data);
    }
    
    function get_carousel(){
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/carousel_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        document.getElementById('carousel-data').innerHTML = this.responseText;
        }
        xhr.send('get_carousel');
    }

    function rem_image(val){
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/carousel_crud.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
      xhr.onload = function() {
        if(this.responseText==1){
          alert('success','Image Removed!');
          get_carousel();
        }else{
          alert('error','Server down!');
        }
      }
      xhr.send('rem_image='+val);
    }


    window.onload = function() {
      get_carousel();
    }

  </script>
  
</body>
</html>