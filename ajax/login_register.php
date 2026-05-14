<?php
    require_once('../admin/inc/db_config.php');
    require_once('../admin/inc/essentials.php');


    // Handle Registration
    if(isset($_POST['register']))
    {
        $data = filteration($_POST);

        if($data['pass'] != $data['cpass']){
            echo 'pass_mismatch';
            exit;
        }

        $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", 
            [$data['email'], $data['phonenum']], "ss");

        if(mysqli_num_rows($u_exist) != 0){
            $u_exist_fetch = mysqli_fetch_assoc($u_exist);
            echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
            exit;
        }

        $img = uploadUserImage($_FILES['profile']);

        if($img == 'inv_img'){ echo 'inv_img'; exit; }
        else if($img == 'upd_failed'){ echo 'upd_failed'; exit; }

        $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

        $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`,`status`) 
                  VALUES (?,?,?,?,?,?,?,?,?,?)";
        
        $values = [$data['name'],$data['email'],$data['address'],$data['phonenum'],$data['pincode'],$data['dob'],$img,$enc_pass, 1,1];

       // Inside ajax/login_register.php registration block
        if(insert($query, $values, 'ssssisssii')){
            global $con;
            $u_id = mysqli_insert_id($con); 
            
            if(session_status() == PHP_SESSION_NONE) session_start();
            
            $_SESSION['login'] = true;
            $_SESSION['uId'] = $u_id;
            $_SESSION['uName'] = $data['name'];
            $_SESSION['uPic'] = $img;
            $_SESSION['uPhone'] = $data['phonenum'];
            
            echo 1; // Return success to the AJAX call
        } else {
            echo 'ins_failed';
        }
      }
    

    // Handle Login
    if(isset($_POST['login']))
    {
        $data = filteration($_POST);

        // Crucial: You need to pass $data['email_mob'] TWICE in the values array
        $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", 
            [$data['email_mob'], $data['email_mob']], "ss");
        
        if(mysqli_num_rows($u_exist) == 0){
            echo 'inv_email_mob'; // User not found
            exit;
        }
        else {
            $u_fetch = mysqli_fetch_assoc($u_exist);

            // 1. Check Verification (Your data shows 1, so this passes)
            if($u_fetch['is_verified'] == 0){
                echo 'not_verified';
                exit;
            }
            // 2. Check Status (This is why Prapti fails)
            else if($u_fetch['status'] == 0){
                echo 'inactive';
                exit;
            }
            else {
                // 3. Verify Password
                if(!password_verify($data['pass'], $u_fetch['password'])){
                    echo 'invalid_pass';
                    exit;
                }
                else {
                    // Login Success - Start Session
                    session_start();
                    $_SESSION['login'] = true;
                    $_SESSION['uId'] = $u_fetch['id'];
                    $_SESSION['uName'] = $u_fetch['name'];
                    $_SESSION['uPic'] = $u_fetch['profile'];
                    $_SESSION['uPhone'] = $u_fetch['phonenum'];
                    echo 1;
                }
            }
        }
    }
?>