    <!-- Footer -->
    <div class="container-fluid bg-white mt-5">
        <div class="row">
            <div class="col-lg-4 p-4">
                <h3 class="h-font fw-bold fs-3 mb-2">Hotel Gaarland</h3>
                <p>
                    Rights reserved.
                </p>
            </div>
            <div class="col-lg-4 p-4">
                <h5 class="mb-3">Links</h5>
                <a href="index.php" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a><br>
                <a href="rooms.php" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a><br>
                <a href="facilities.php" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a><br>
                <a href="contact.php" class="d-inline-block mb-2 text-dark text-decoration-none">Contact Us</a><br>
                <a href="about.php" class="d-inline-block mb-2 text-dark text-decoration-none">About</a>
            </div>
            
            <div class="col-lg-4 p-4">
                <h5 class="mb-3">Follow us</h5>
                <?php 
                    $contact_r = $contact_r ?? ['tw' => '', 'fb' => '', 'insta' => ''];
                    if(!empty($contact_r['tw'])){
                        echo<<<data
                        <a href="{$contact_r['tw']}" class="d-inline-block text-dark text-decoration-none mb-2">
                            <i class="bi bi-twitter me-1"></i>Twitter
                        </a>
                        data;
                        echo '<br>';
                    }
                    if(!empty($contact_r['fb'])){
                        echo '<a href="' . $contact_r['fb'] . '" class="d-inline-block text-dark text-decoration-none mb-2">';
                        echo '<i class="bi bi-facebook me-1"></i>Facebook</a><br>';
                    }
                    if(!empty($contact_r['insta'])){
                        echo '<a href="' . $contact_r['insta'] . '" class="d-inline-block text-dark text-decoration-none">';
                        echo '<i class="bi bi-instagram me-1"></i>Instagram</a>';
                    }
                ?>
            </div>
        </div>
     </div>

    <h6 class="text-center bg-dark text-white p-3 m-0">Designed and Developed by Ankita</h6>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        function setActive(){
            let navbar =document.getElementById('nav-bar');
            let a_tags =navbar.getElementsByTagName('a');

            for(i=0; i<a_tags.length; i++){
                let file =a_tags[i].href.split('/').pop();
                let file_name = file.split('.')[0];

                if(document.location.href.indexOf(file_name) >= 0){
                    a_tags[i].classList.add('active');
                }
            }
        }
        setActive();

        function alert(type, msg) {
            let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
            let element = document.createElement('div');
            element.innerHTML = `
                <div class="alert ${bs_class} alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 80px; right: 25px; z-index: 1111;">
                    <strong class="me-3">${msg}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.append(element);
            setTimeout(() => element.remove(), 3000);
        }

        // --- REGISTER  ---
{            let register_form = document.getElementById('register-form');

            // ONLY run this if the form exists (Guard)
            if (register_form) {
                register_form.addEventListener('submit', (e) => {
                    e.preventDefault();

                    let data = new FormData(register_form);
                    data.append('register', '');

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "ajax/login_register.php", true);

                    xhr.onload = function() {
                        if (this.responseText == 'pass_mismatch') {
                            alert('error', "Passwords do not match!");
                        } else if (this.responseText == 'email_already') {
                            alert('error', "Email already registered!");
                        } else if (this.responseText == 'phone_already') {
                            alert('error', "Phone number already registered!");
                        } else if (this.responseText == 'inv_img') {
                            alert('error', "Only JPG, WEBP & PNG allowed!");
                        } else if (this.responseText == 'upd_failed') {
                            alert('error', "Image upload failed!");
                        } else if (this.responseText == 'ins_failed') {
                            alert('error', "Registration failed!");
                        } else if (this.responseText.trim() == '1') {
                            alert('success', "Registration successful!");
                            register_form.reset();
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
                        }
                    }
                    xhr.send(data);
                });
            }

            // --- LOGIN LOGIC ---
            let login_form = document.getElementById('login-form');

                login_form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    let data = new FormData();
                    data.append('email_mob', login_form.elements['email_mob'].value);
                    data.append('pass', login_form.elements['pass'].value);
                    data.append('login', '');

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "ajax/login_register.php", true);
                    xhr.onload = function() {
                        if (this.responseText == 'inv_email_mob') {
                            alert('error', "Invalid Email or Mobile Number!");
                        } else if (this.responseText == 'not_verified') {
                            alert('error', "Email is not verified!");
                        } else if (this.responseText == 'inactive') {
                            alert('error', "Account Suspended! Please contact admin.");
                        } else if (this.responseText == 'invalid_pass') {
                            alert('error', "Incorrect Password!");
                        } else if (this.responseText == 1) {
                            // SUCCESSFUL REDIRECTION
                            window.location = window.location.pathname; 
                        }
                    }
                    xhr.send(data);
                });
}
</script>

