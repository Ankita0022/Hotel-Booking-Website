<?php 
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  if(isset($_POST['get_queries'])) {
    $frm_data = filteration($_POST);

    $limit = 5; // Records per page
    $page = isset($frm_data['page']) ? $frm_data['page'] : 1;
    $start = ($page - 1) * $limit;

    $query = "SELECT * FROM `user_queries` ORDER BY `sr_no` DESC LIMIT $start, $limit";
    $res = mysqli_query($con, $query);
    
    $total_q = mysqli_query($con, "SELECT COUNT(*) as total FROM `user_queries`");
    $total_res = mysqli_fetch_assoc($total_q);
    $total_pages = ceil($total_res['total'] / $limit);

    $data = "";
    $i = $start + 1; // Sequential numbering logic

    if(mysqli_num_rows($res) == 0){
      $data = "<tr><td colspan='7'>No Data Found!</td></tr>";
    }
    else {
      while($row = mysqli_fetch_assoc($res)) {
        $seen = "";
        if($row['seen'] != 1){
          $seen = "<button onclick='update_status($row[sr_no],1)' class='btn btn-sm rounded-pill btn-primary'>Mark as read</button>";
        }
        $seen .= "<button onclick='rem_query($row[sr_no])' class='btn btn-sm rounded-pill btn-danger ms-2'>Delete</button>";

        $data .= "
          <tr class='row-animation align-middle'>
            <td>$i</td>
            <td>$row[name]</td>
            <td>$row[email]</td>
            <td>$row[subject]</td>
            <td>$row[message]</td>
            <td>$row[date]</td>
            <td>$seen</td>
          </tr>
        ";
        $i++;
      }
    }

    $pagination = "";
    if($total_pages > 1) {
        $disabled = ($page <= 1) ? "disabled" : "";
        $prev = $page - 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='get_queries($prev)' class='page-link shadow-none'>&laquo;</button></li>";

        for($p=1; $p<=$total_pages; $p++) {
            $active = ($p == $page) ? "active" : "";
            $pagination .= "<li class='page-item $active'><button onclick='get_queries($p)' class='page-link shadow-none'>$p</button></li>";
        }

        $disabled = ($page >= $total_pages) ? "disabled" : "";
        $next = $page + 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='get_queries($next)' class='page-link shadow-none'>&raquo;</button></li>";
    }

    echo json_encode([
      "table_data" => $data,
      "pagination" => $pagination
    ]);
  } // End of get_queries block

  if(isset($_POST['update_status'])) {
    $frm_data = filteration($_POST);
    $q = "UPDATE `user_queries` SET `seen`=? WHERE `sr_no`=?";
    $v = [$frm_data['val'], $frm_data['update_status']];
    if(update($q, $v, 'ii')) echo 1;
    else echo 0;
  }

  if(isset($_POST['rem_query'])) {
    $frm_data = filteration($_POST);
    $q = "DELETE FROM `user_queries` WHERE `sr_no`=?";
    $v = [$frm_data['rem_query']];
    if(delete($q, $v, 'i')) echo 1;
    else echo 0;
  }

  if(isset($_POST['bulk_action'])) {
    $frm_data = filteration($_POST);
    if($frm_data['type'] == 'seen'){
      $q = "UPDATE `user_queries` SET `seen`=?";
      if(update($q, [1], 'i')) echo 1;
      else echo 0;
    } else {
      $q = "DELETE FROM `user_queries`";
      if(mysqli_query($con, $q)) echo 1;
      else echo 0;
    }
  }
?>