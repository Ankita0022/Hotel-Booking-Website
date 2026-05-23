<?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $is_booking = ($current_page == 'new_bookings.php' || $current_page == 'refund_bookings.php' || $current_page == 'booking_records.php');
?>
<div class="container-fluid bg-dark text-light p-3 d-flex align-items-center justify-content-between sticky-top shadow-sm">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-light me-3 btn-sm border-0" onclick="toggleSidebar()">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h3 class="mb-0 h-font">HOTEL GAARLAND</h3>
    </div>
    <a href="logout.php" class="btn btn-light btn-sm fw-bold px-3"> LOG OUT </a>
</div>

<div class="bg-dark border-top border-3 border-secondary" id="dashboard-menu">
    <nav class="navbar navbar-dark">
        <div class="container-fluid flex-column align-items-stretch">
            <h4 class="mt-2 text-light text-center border-bottom pb-2">ADMIN PANEL</h4>
            <div class="flex-column mt-2 align-items-stretch" id="adminDropdown">
                <ul class="nav nav-pills flex-column px-2">
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <button class="btn text-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between <?php echo $is_booking ? 'active' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#bookingLinks">
                            <div class="d-flex align-items-center"><i class="bi bi-calendar-check me-2"></i><span>Bookings</span></div>
                            <span><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="collapse <?php echo $is_booking ? 'show' : ''; ?> small" id="bookingLinks">
                            <ul class="nav nav-pills flex-column rounded border border-secondary">
                                <li class="nav-item mb-1">
                                    <a class="nav-link text-white <?php echo ($current_page == 'new_bookings.php') ? 'active' : ''; ?>" href="new_bookings.php"><i class="bi bi-calendar-plus me-2"></i> <span>New Bookings</span></a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'refund_bookings.php') ? 'active' : ''; ?>" href="refund_bookings.php"><i class="bi bi-currency-exchange me-2"></i> <span>Refund Bookings</span></a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'booking_records.php') ? 'active' : ''; ?>" href="booking_records.php"><i class="bi bi-journal-bookmark-fill me-2"></i> <span>All Bookings</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'user.php') ? 'active' : ''; ?>" href="user.php">
                            <i class="bi bi-people me-2"></i> <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'user_queries.php') ? 'active' : ''; ?>" href="user_queries.php">
                            <i class="bi bi-chat-left-dots me-2"></i> <span>User Queries</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'rate_review.php') ? 'active' : ''; ?>" href="rate_review.php">
                            <i class="bi bi-star-half me-2"></i> <span>Ratings & Reviews</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>" href="rooms.php">
                            <i class="bi bi-door-open me-2"></i> <span>Rooms</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'features_facilities.php') ? 'active' : ''; ?>" href="features_facilities.php">
                            <i class="bi bi-star me-2"></i> <span>Features & Facilities</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'carousel.php') ? 'active' : ''; ?>" href="carousel.php">
                            <i class="bi bi-images me-2"></i> <span>Carousel</span>
                        </a>
                    </li>     
                    <li class="nav-item mb-1">
                        <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="settings.php">
                            <i class="bi bi-gear me-2"></i> <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>