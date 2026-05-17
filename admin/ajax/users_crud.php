<?php 
    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin();

    if(isset($_POST['get_users'])) {
        $frm_data = filteration($_POST);

        // --- Logic for Pagination ---
        $limit = 10; 
        $page = isset($frm_data['page']) ? $frm_data['page'] : 1;
        $start = ($page - 1) * $limit;

        // Fetch paginated data
        $query = "SELECT * FROM `user_cred` ORDER BY `id` DESC LIMIT $start, $limit";
        $res = mysqli_query($con, $query);

        // Fetch total records for pagination
        $total_q = mysqli_query($con, "SELECT COUNT(*) as total FROM `user_cred`");
        $total_res = mysqli_fetch_assoc($total_q);
        $total_pages = ceil($total_res['total'] / $limit);

        $path = USERS_IMG_PATH;
        $data = "";

        // UUID -> # Logic: Start counter based on the current page
        $i = $start + 1; 

        if(mysqli_num_rows($res) == 0){
            $data = "<tr><td colspan='10'>No Users Found!</td></tr>";
        }
        else {
            while($row = mysqli_fetch_assoc($res)) {
                $del_btn = "<button type='button' onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i></button>";
                $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
                
                if($row['is_verified']){
                    $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
                    $del_btn = ""; 
                }

                $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
                if(!$row['status']){
                    $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-danger btn-sm shadow-none'>banned</button>";
                }

                $date = date("d-m-Y", strtotime($row['datentime']));
                
                // Added "row-animation" class and replaced UUID with counter $i
                $data .= "<tr class='row-animation align-middle'>
                    <td>$i</td>
                    <td><img src='$path$row[profile]' width='40px' class='rounded-circle me-2'>$row[name]</td>
                    <td>$row[email]</td>
                    <td>$row[phonenum]</td>
                    <td>$row[address] | $row[pincode]</td>
                    <td>$row[dob]</td>
                    <td>$verified</td>
                    <td>$status</td>
                    <td>$date</td>
                    <td>$del_btn</td>
                </tr>";
                $i++;
            }
        }

        // --- Logic for Pagination UI ---
        $pagination = "";
        if($total_pages > 1){
            for($p=1; $p<=$total_pages; $p++){
                $active = ($p == $page) ? "btn-dark" : "btn-outline-dark";
                $pagination .= "<button onclick='get_users($p)' class='btn btn-sm $active shadow-none me-1'>$p</button>";
            }
        }

        // Return as JSON
        echo json_encode([
            "table_data" => $data,
            "pagination" => $pagination
        ]);
    }

    if(isset($_POST['toggle_status'])) {
        $frm_data = filteration($_POST);
        $q = "UPDATE `user_cred` SET `status`=? WHERE `id`=?";
        $v = [$frm_data['value'], $frm_data['toggle_status']];
        if(update($q, $v, 'ii')) echo 1;
        else echo 0;
    }
?>