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
            let navbar = document.getElementById('nav-bar');
            if (navbar) {
                let a_tags = navbar.getElementsByTagName('a');
                for(i=0; i<a_tags.length; i++){
                    let file = a_tags[i].href.split('/').pop();
                    let file_name = file.split('.')[0];

                    if(document.location.href.indexOf(file_name) >= 0){
                        a_tags[i].classList.add('active');
                    }
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

        // --- REGISTER ---
        let register_form = document.getElementById('register-form');
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
                        bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
                    }
                }
                xhr.send(data);
            });
        }

        // --- LOGIN ---
        let login_form = document.getElementById('login-form');
        if (login_form) {
            login_form.addEventListener('submit', (e) => {
                e.preventDefault();

                let data = new FormData(login_form);
                data.append('login', '');

                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/login_register.php", true);

                xhr.onload = function() {
                    if (this.responseText == 'inv_credential') {
                        alert('error', "Invalid Email/Phone or Password!");
                    } else if (this.responseText == 'not_verified') {
                        alert('error', "Email verification pending!");
                    } else if (this.responseText == 'inactive') {
                        alert('error', "Account suspended. Please contact administration.");
                    } else if (this.responseText.trim() == '1') {
                        window.location = window.location.pathname;
                    }
                }
                xhr.send(data);
            });
        }

        // --- AUTHENTICATED CHECKOUT INTERCEPTOR ---
        function checkLoginToBook(login, room_id) {
            if (login == 1) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'pay_now.php';

                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'room_id';
                input.value = room_id;

                form.appendChild(input);
                document.body.appendChild(form);
                window.location.href = 'confirm_booking.php?id=' + room_id;
                } else {
                    let myModal = document.getElementById('loginModal');
                    if (myModal) {
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        alert('error', 'Please login to your account first to book rooms!');
                        modal.show();
                    } else {
                        alert('error', 'Authentication window component missing!');
                    }
                }
            }
        
    </script>