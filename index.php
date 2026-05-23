<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Gaarland - HOME</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <?php require("include/links.php"); ?>
</head>

<body class="bg-light">

    <?php require("include/header.php"); ?>

    <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
        <div class="swiper-wrapper">
            <?php 
                $res = selectAll('carousel');
                while($row = mysqli_fetch_assoc($res)){
                    $path = CAROUSEL_IMG_PATH;
                    echo<<<data
                    <div class="swiper-slide">
                        <img src="$path$row[image]" class="w-100 d-block">
                    </div>
                    data;
                }
            ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    </div>

    <div class="container availability-form">
        <div class="row">
            <div class="col-lg-12 bg-white shadow p-4 rounded">
                <h5 class="mb-4 fw-bold">Check Booking Availability</h5>
                <form action="rooms.php" method="GET">
                    <div class="row align-items-end">
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Check-in</label>
                            <input type="date" class="form-control shadow-none" name="checkin" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Check-out</label>
                            <input type="date" class="form-control shadow-none" name="checkout" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Adult</label>
                            <select class="form-select shadow-none" name="adult">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">More</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-3">
                            <label class="form-label" style="font-weight: 500;">Children</label>
                            <select class="form-select shadow-none" name="children">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">More</option>
                            </select>
                        </div>
                        <input type="hidden" name="check_availability" value="">
                        <div class="col-lg-1 mb-lg-3 mt-2">
                            <button type="submit" class="btn text-white shadow-none custom-bg fw-bold text-uppercase">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
     
    <div class="container">
        <div class="row">
            <?php
                $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3", [1, 0], 'ii');

                while($room_data = mysqli_fetch_assoc($room_res)){
                    
                    // Fetch Features of the current room
                    $fea_q = "SELECT f.name FROM `features` f 
                              INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
                              WHERE rfea.room_id = ?";
                    $fea_res = select($fea_q, [$room_data['id']], 'i');
                    $features_data = "";
                    while($fea_row = mysqli_fetch_assoc($fea_res)){
                        $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border'>{$fea_row['name']}</span>";
                    }

                    // Fetch Facilities of the current room
                    $fac_q = "SELECT f.name FROM `facilities` f 
                              INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
                              WHERE rfac.room_id = ?";
                    $fac_res = select($fac_q, [$room_data['id']], 'i');
                    $facilities_data = "";
                    while($fac_row = mysqli_fetch_assoc($fac_res)){
                        $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1 p-2 border'>{$fac_row['name']}</span>";
                    }

                    // Fetch Room Thumbnail/Main Image
                    $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
                    $thumb_q = select("SELECT * FROM `room_images` WHERE `room_id`=? AND `thumb`=?", [$room_data['id'], 1], 'ii');
                    if(mysqli_num_rows($thumb_q) > 0){
                        $thumb_res = mysqli_fetch_assoc($thumb_q);
                        $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
                    }

                    // Aggregate Review Ratings Calculation block loop
                    global $con;
                    $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `room_id`='$room_data[id]'";
                    $rating_res = mysqli_query($con, $rating_q);
                    $rating_fetch = mysqli_fetch_assoc($rating_res);
                    $rating_stars = "";
                    if($rating_fetch['avg_rating'] != NULL){
                        $avg_rating = round($rating_fetch['avg_rating']);
                        for($i=0; $i<$avg_rating; $i++){
                            $rating_stars .= "<i class='bi bi-star-fill text-warning me-1 small'></i>";
                        }
                    } else {
                        $rating_stars = "<i class='bi bi-star text-muted me-1 small'></i><span class='text-muted small'>No ratings yet</span>";
                    }

                    // Check availability for tonight
                    $today = date('Y-m-d');
                    $tomorrow = date('Y-m-d', strtotime('+1 day'));
                    $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order` 
                        WHERE booking_status='booked' AND room_id=? 
                        AND check_in < ? AND check_out > ?";
                    $tb_res = select($tb_query, [$room_data['id'], $tomorrow, $today], 'iss');
                    $tb_fetch = mysqli_fetch_assoc($tb_res);

                    $is_available = ($tb_fetch['total_bookings'] < $room_data['quantity']);

                    $book_btn = "";
                    if($settings_r['shutdown']){
                        $book_btn = "<button class='btn btn-sm btn-danger shadow-none rounded-pill px-3 fw-bold' disabled>Bookings Closed</button>";
                    } else if(!$is_available) {
                        $book_btn = "<button class='btn btn-sm btn-danger shadow-none rounded-pill px-3 fw-bold' disabled>Not Available</button>";
                    } else {
                        $login = 0;
                        if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                            $login = 1;
                        }
                        $book_btn = "<button onclick='checkLoginToBook($login, {$room_data['id']})' class='btn btn-sm text-white custom-bg shadow-none rounded-pill px-3 fw-bold'>Book Now</button>";
                    }

                    echo "
                    <div class='col-lg-4 col-md-6 my-3'>
                        <div class='card border-0 shadow' style='max-width:350px; margin:auto;'>
                            <img src='$room_thumb' class='card-img-top' style='height:220px; object-fit:cover;'>
                            <div class='card-body'>
                                <h5 class='fw-bold'>{$room_data['name']}</h5>
                                <h6 class='mb-4 text-secondary fw-bold'>₹{$room_data['price']} per night</h6>
                                
                                <div class='features mb-3'>
                                    <h6 class='mb-1 fw-bold small text-dark'>Features</h6>
                                    $features_data
                                </div>
                                <div class='facilities mb-3'>
                                    <h6 class='mb-1 fw-bold small text-dark'>Facilities</h6>
                                    $facilities_data
                                </div>
                                <div class='guests mb-3'>
                                    <h6 class='mb-1 fw-bold small text-dark'>Guests</h6>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap p-2 border'>{$room_data['adult']} Adults</span>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap p-2 border'>{$room_data['children']} Children</span>
                                </div>
                                <div class='rating mb-4'>
                                    <h6 class='mb-1 fw-bold small text-dark'>Rating</h6>
                                    <div>$rating_stars</div>
                                </div>
                                <div class='d-flex justify-content-evenly mb-2'>
                                    $book_btn
                                    <a href='room_details.php?id={$room_data['id']}' class='btn btn-sm btn-outline-dark shadow-none rounded-pill px-3 fw-bold'>More details</a>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            ?>

            <div class="col-lg-12 text-center mt-5">
                <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none text-uppercase px-4 py-2">More Rooms >>></a>
            </div>
        </div>
    </div>

    <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR FACILITIES</h2>
    <div class="container">
        <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
            <?php
                // FIX: Bring $con into global scope before running raw mysqli_query lines
                global $con;
                $fac_res = mysqli_query($con, "SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 5");
                while($fac_row = mysqli_fetch_assoc($fac_res)){
                    echo "
                    <div class='col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3 border-top border-3 border-dark'>
                        <img src='".FACILITIES_IMG_PATH.$fac_row['icon']."' width='60px' style='height:60px; object-fit:contain;'>
                        <h5 class='mt-3 fw-bold small'>{$fac_row['name']}</h5>
                    </div>";
                }
            ?>

            <div class="col-lg-12 text-center mt-5">
                <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none text-uppercase px-4 py-2">More Facilities >>></a>
            </div>
        </div>
    </div>

    <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">TESTIMONIALS</h2>

    <div class="container mt-5">
        <div class="swiper swiper-testimonials">
            <div class="swiper-wrapper mb-5">
                <?php
                    // FIX: Bring $con into global scope before running raw booking review lines
                    global $con;
                    $review_q = "SELECT rr.*, uc.name AS user_name, uc.profile AS user_pic FROM `rating_review` rr 
                                 INNER JOIN `user_cred` uc ON rr.user_id = uc.id 
                                 ORDER BY rr.sr_no DESC LIMIT 6";
                    $review_res = mysqli_query($con, $review_q);

                    if(mysqli_num_rows($review_res) == 0){
                        echo "
                        <div class='swiper-slide bg-white p-4 text-center text-muted fw-bold'>
                            <i class='bi bi-chat-square-quote fs-2 d-block mb-2'></i>No testimonials received yet.
                        </div>";
                    } else {
                        while($rev_row = mysqli_fetch_assoc($review_res)){
                            $stars = "";
                            for($i=0; $i<$rev_row['rating']; $i++){
                                $stars .= "<i class='bi bi-star-fill text-warning small me-1'></i>";
                            }
                            $user_avatar = ($rev_row['user_pic'] != '') ? USERS_IMG_PATH.$rev_row['user_pic'] : "images/users/thumbnail.jpg";
                            
                            echo "
                            <div class='swiper-slide bg-white p-4 rounded shadow-sm border-start border-3 border-primary'>
                                <div class='profile d-flex align-items-center mb-3'>
                                    <img src='$user_avatar' class='rounded-circle border' style='width:35px; height:35px; object-fit:cover;'>
                                    <h6 class='m-0 ms-2 fw-bold text-dark small'>{$rev_row['user_name']}</h6>
                                </div>
                                <p class='text-secondary small mb-3' style='line-height:1.6; height:75px; overflow:hidden;'>
                                    \"{$rev_row['review']}\"
                                </p>
                                <div class='rating'>$stars</div>
                            </div>";
                        }
                    }
                ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <?php
        $contact_q = "SELECT * FROM `contact_details` WHERE `sr_no`=?";
        $values = [1];
        $contact_r = mysqli_fetch_assoc(select($contact_q,$values,'i'));
    ?>

    <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">REACH US</h2>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-8 mb-3 bg-white rounded shadow">
                <iframe class="w-100 rounded" height="380px" src="<?php echo $contact_r['iframe']?>" loading="lazy"></iframe>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="bg-white p-4 rounded mb-4 shadow">
                    <h5>Call us</h5>
                    <a href="tel: +<?php echo $contact_r['pn1']?>" class="d-inline-block mb-2 text-decoration-none text-dark fw-bold">
                        <i class="bi bi-telephone-fill me-1 text-primary"></i> +<?php echo $contact_r['pn1']?>
                    </a>
                    <br>
                    <?php
                    if($contact_r['pn2']!=''){
                        echo<<<data
                        <a href="tel: +$contact_r[pn2]" class="d-inline-block mb-2 text-decoration-none text-dark fw-bold">
                        <i class="bi bi-telephone-fill me-1 text-primary"></i> +$contact_r[pn2]
                        </a>
                        data;
                    }
                    ?>
                </div>

                <div class="bg-white p-4 rounded mb-4 shadow">
                    <h5>Follow us</h5>
                    <?php 
                        if($contact_r['tw']!=''){
                            echo<<<data
                            <a href="$contact_r[tw]" class="d-inline-block mb-3 text-decoration-none">
                                <span class="badge bg-light text-dark fs-6 p-2 border">
                                    <i class="bi bi-twitter me-1 text-info"></i>Twitter
                                </span>
                            </a>
                            <br>
                            data;
                        }
                    ?>
                    <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block mb-3 text-decoration-none">
                        <span class="badge bg-light text-dark fs-6 p-2 border">
                            <i class="bi bi-instagram me-1 text-danger"></i>Instagram
                        </span>
                    </a>
                    <br>
                    <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block text-decoration-none">
                        <span class="badge bg-light text-dark fs-6 p-2 border">
                            <i class="bi bi-facebook me-1 text-primary"></i>Facebook
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php require("include/footer.php"); ?>


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            let today = new Date().toISOString().split('T')[0];
            let checkin = document.querySelector('input[name="checkin"]');
            let checkout = document.querySelector('input[name="checkout"]');
            if(checkin && checkout){
                checkin.min = today;
                checkout.min = today;
                checkin.addEventListener('change', () => { checkout.min = checkin.value; });
            }
        });

        var swiper = new Swiper(".swiper-container", {
            spaceBetween: 30,
            effect: "fade",
            pagination: { el: ".swiper-pagination" },
            autoplay: { delay: 3500, disableOnInteraction: false },
            keyboard: true,
            loop: true
        });

        var swiper = new Swiper(".swiper-testimonials", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            loop: true,
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
            },
            pagination: { el: ".swiper-pagination" },
            breakpoints: {
                320: { slidesPerView: 1 },
                640: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    </script>
</body>
</html>