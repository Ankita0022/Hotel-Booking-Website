<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Gaarland - ROOMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <?php require("include/links.php"); ?>
</head>

<body class="bg-light">

    <?php require("include/header.php"); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 px-lg-0">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-2">FILTERS</h4>
                        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse flex-column mt-2 align-items-stretch" id="filterDropdown">
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">CHECK AVAILABILITY</h5>
                                <label class="form-label">Check-in</label>
                                <input type="date" class="form-control shadow-none mb-3" id="checkin" onchange="fetch_rooms()">
                                <label class="form-label">Check-out</label>
                                <input type="date" class="form-control shadow-none mb-3" id="checkout" onchange="fetch_rooms()">
                            </div>

                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">FACILITIES</h5>
                                <?php 
                                    $res = selectAll('facilities');
                                    while($row = mysqli_fetch_assoc($res)){
                                        echo<<<data
                                            <div class="mb-2">
                                                <input type="checkbox" onclick="fetch_rooms()" name="facilities" value="$row[id]" id="f$row[id]" class="form-check-input shadow-none me-1">
                                                <label class="form-check-label" for="f$row[id]">$row[name]</label>
                                            </div>
                                        data;
                                    }
                                ?>
                            </div>

                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">GUESTS</h5>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <label class="form-label">Adults</label>
                                        <input type="number" id="adults" oninput="fetch_rooms()" class="form-control shadow-none mb-3">
                                    </div>
                                    <div>
                                        <label class="form-label">Children</label>
                                        <input type="number" id="children" oninput="fetch_rooms()" class="form-control shadow-none mb-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="col-lg-9 col-md-12 px-4" id="rooms-data">
                <?php
                    // Fetch active and non-removed rooms from database
                    $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC", [1, 0], 'ii');

                    while($room_data = mysqli_fetch_assoc($room_res)) {
                        
                        // Fetch Features for each room
                        $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
                            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
                            WHERE rfea.room_id = '$room_data[id]'");

                        $features_data = "";
                        while($fea_row = mysqli_fetch_assoc($fea_q)){
                            $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$fea_row[name]</span>";
                        }

                        // Fetch Facilities for each room
                        $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f 
                            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
                            WHERE rfac.room_id = '$room_data[id]'");

                        $facilities_data = "";
                        while($fac_row = mysqli_fetch_assoc($fac_q)){
                            $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$fac_row[name]</span>";
                        }

                        // Get Room Thumbnail Image
                        $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
                        $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");

                        if(mysqli_num_rows($thumb_q) > 0){
                            $thumb_res = mysqli_fetch_assoc($thumb_q);
                            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
                        }

                        // Handle Booking Button logic (Shutdown check)
                        $book_btn = "";
                        if(!$settings_r['shutdown']){
                            $login = 0;
                            if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                                $login = 1;
                            }
                            $book_btn = "<button onclick='checkLoginToBook($login, $room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Book Now</button>";
                        } else {
                            $book_btn = "<button class='btn btn-sm w-100 btn-danger shadow-none mb-2' disabled>Bookings Closed</button>";
                        }

                        // Print the Room Card dynamically
                        echo<<<data
                            <div class="card mb-4 shadow border-0">
                                <div class="row g-0 p-3 align-items-center">
                                    <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                                        <img src="$room_thumb" class="img-fluid rounded-start">
                                    </div>
                                    <div class="col-md-5 px-lg-3 px-md-3 px-0">
                                        <h5 class="mb-3">$room_data[name]</h5>
                                        <div class="features mb-3">
                                            <h6 class="mb-1">Features</h6>
                                            $features_data
                                        </div>
                                        <div class="facilities mb-3">
                                            <h6 class="mb-1">Facilities</h6>
                                            $facilities_data
                                        </div>
                                        <div class="guests">
                                            <h6 class="mb-1">Guests</h6>
                                            <span class="badge rounded-pill bg-light text-dark text-wrap">$room_data[adult] Adults</span>
                                            <span class="badge rounded-pill bg-light text-dark text-wrap">$room_data[children] Children</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h6 class="mb-4">₹$room_data[price] per night</h6>
                                        $book_btn
                                        <a href="room_details.php?id=$room_data[id]" class="btn btn-sm w-100 btn-outline-dark shadow-none">More details</a>
                                    </div>
                                </div>
                            </div>
                        data;
                    }
                ?>
            </div>
        </div>
    </div>

    <?php require("include/footer.php"); ?>


    <script>
    let rooms_data = document.getElementById('rooms-data');
    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let adults = document.getElementById('adults');
    let children = document.getElementById('children');

    function fetch_rooms() {
        let chk_in_val = checkin.value;
        let chk_out_val = checkout.value;

        if(chk_in_val != '' && chk_out_val != '') {
            if(chk_in_val > chk_out_val) {
                alert('Check-out date must be greater than Check-in date!');
                return;
            }
        }

        let facility_list = [];
        let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
        get_facilities.forEach((val) => {
            facility_list.push(val.value);
        });

        let data = {
            check_availability: "",
            checkin: chk_in_val,
            checkout: chk_out_val,
            adults: adults.value,
            children: children.value,
            facility_list: JSON.stringify(facility_list)
        };

        let xhr = new XMLHttpRequest();
        xhr.open("GET", "ajax/rooms.php?" + new URLSearchParams(data), true);

        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                rooms_data.innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // Load rooms automatically when the page opens
    window.onload = fetch_rooms;
</script>

</body>
</html>