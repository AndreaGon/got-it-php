<?php
  require_once '../rbac.php';
  $rbac = new RBAC();

  session_start();
  if(!isset($_SESSION['userID']) || !isset($_SESSION['token'])){
    header("Location: ../login.php");
  }
  
  $dbServername = "localhost";
  $dbUsername = "root";
  $dbPassword = "";
  $dbName = "gotit_db";
  $dbPort = 3306;
  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

  $query = "SELECT
  matched_items.id AS matched_id,
  matched_items.status AS matched_status,
  lost_items.id AS lost_id,
  found_items.id AS found_id,
  lost_items.itemName AS lost_name,
  found_items.itemName AS found_name
  FROM matched_items
  INNER JOIN lost_items ON
  matched_items.lost_id = lost_items.id
  INNER JOIN found_items ON matched_items.found_id = found_items.id WHERE matched_items.status = 0";
  //$result = $conn->query($query);
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

  if(isset($_POST['submitted'])){
    $matchId = (isset($_POST['matchedId']) ? $_POST['matchedId'] : '');
    $lostId = (isset($_POST['lostID']) ? $_POST['lostID'] : '');
    $action = $_POST['submit'];
    switch($action){
      case "Approve": $update = "UPDATE matched_items SET status = 1 WHERE id = " . $matchId;
                      $update2 = "UPDATE lost_items SET status = 2 WHERE id = " . $lostId;
                      $isUpdateSuccess = mysqli_query($conn, $update) or die(mysqli_error($conn));
                      $isUpdateSuccess2 = mysqli_query($conn, $update2) or die(mysqli_error($conn));
                      header("Refresh:0");
                      break;
      case "Deny":    $update = "UPDATE matched_items SET status = 2 WHERE id = " . $matchId;
                      $update2 = "UPDATE lost_items SET status = 0 WHERE id = " . $lostId;
                      $isUpdateSuccess = mysqli_query($conn, $update) or die(mysqli_error($conn));
                      $isUpdateSuccess2 = mysqli_query($conn, $update2) or die(mysqli_error($conn));
                      header("Refresh:0");
                      break;
      case "Start Automatic Match":
            $url = 'http://127.0.0.1:5000/api/startmatch';
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            break;

    }

  }

  
  if($rbac->getRoleNameFromId($_SESSION['role']) == 'user'){
    echo '<script>
    alert("Invalid access!");
    window.location.href="../index.php";
    </script>'; 
  }
  else{
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '';
    echo '<head>';
    echo '<meta charset="UTF-8" />';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
    echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
    echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />';
    echo '<link href="../css/all.min.css" rel="stylesheet" />';
    echo '<link href="../css/templatemo-style.css" rel="stylesheet" />';
    echo '<link href="../css/custom.css" media="all" rel="stylesheet" />';
    echo '</head>';
    echo '<!--';
    echo '';
    echo 'Simple House';
    echo '';
    echo 'https://templatemo.com/tm-539-simple-house';
    echo '';
    echo '-->';
    echo '<body>';
    echo '';

    //header + navigation bar
    echo '<div class="container">';
    echo '<!-- Top box -->';
    echo '<!-- Logo & Site Name -->';
    echo '<div class="custom-placeholder">';
    echo '<div class="parallax-window">';
    echo '<div class="tm-header">';
    echo '<div class="row tm-header-inner">';
    echo '<div class="col-md-6 col-12">';
    echo '<div class="tm-site-text-box">';
    echo '<img class="tm-site-logo" width="150" src = "../img/logo.png"/>';
    echo '</div>';
    echo '</div>';
    echo '<nav class="col-md-6 col-12 tm-nav">';
    echo '<ul class="tm-nav-ul">';
    echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link active">Verify Item</a></li>';
    echo '<li class="tm-nav-li"><a href="lostitems.php" class="custom-link">Lost Items</a></li>';
    echo '<li class="tm-nav-li"><a href="founditems.php" class="custom-link">Found Items</a></li>';
    echo '<li class="tm-nav-li"><a href="manageusers.php" class="custom-link">Manage Users</a></li>';
    if($rbac->getRoleNameFromId($_SESSION['role']) == "superadmin"){
      echo '<li class="tm-nav-li"><a href="manage-admin.php" class="custom-link">Manage Admins</a></li>';
    }
    echo '<li class="tm-nav-li"><a href="../logout.php" class="custom-link">Logout</a></li>';

    echo '</ul>';
    echo '</nav>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '';

    echo '<main>';
      echo '<header class="row tm-welcome-section">';
      echo '<h2 class="col-12 text-center tm-section-title">Items Verification</h2>';
      echo '</header>';
      echo '';
      echo '<div class="custom-center" style="width: 100%">';
      echo '<table class = "custom-table" border="1" width=1000 height=80>';
      echo '<form action="admin.php" method="POST" style="float: left;margin-left: 5px;margin-right:5px;">
            <input type=\'hidden\' name=\'submitted\' value=\'true\'/>
            <input type="Submit" style="width:200px;" class="custom-button" name="submit" value="Start Automatic Match"/>
          </form>';
      echo '<br/>';
      echo '<br/>';
      echo '<tr>';
      echo '<th>ITEM LOST</th>';
      echo '<th>ITEM CLAIMED</th>';
      echo '<th>ACTION</th>';
      echo '</tr>';
      while($row = $result->fetch_assoc()){
          echo '<tr>';
          echo    '<td>'.$row['lost_name'].'</td>';
          echo    '<td>'.$row['found_name'].'</td>';
          echo    '<td>
                      <form action="bothitem.php" method="POST" style="float: left;margin-left: 5px;margin-right:5px;">
                        <input type=\'hidden\' name="lostID" value="'.$row['lost_id'].'"/>
                        <input type=\'hidden\' name="foundID" value="'.$row['found_id'].'"/>
                        <input type="Submit" style="width:100px;" class="custom-button" name="submit" value="Info"/>
                      </form>
                      <form action="admin.php" method="POST">
                        <input type=\'hidden\' name="matchedId" value="'.$row['matched_id'].'"/>
                        <input type=\'hidden\' name="lostID" value="'.$row['lost_id'].'"/>
                        <input type="Submit" style="width:100px;" class="custom-button" name="submit" value="Approve"/>
                        <input type="Submit" style="width:100px;" class="custom-button" name="submit" value="Deny"/>
                        <input type=\'hidden\' name=\'submitted\' value=\'true\'/>
                      </form>
                    </td>';
          echo '</tr>';
      }

      echo '</table>';
      echo '</div>';
      echo '';
      echo '</main>';


    //footer
    echo '<footer class="tm-footer text-center">';
    echo '<p>Copyright &copy; 2020 Simple House';
    echo '';
    echo '| Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>';
    echo '</footer>';
    echo '</div>';
    echo '<script src="js/jquery.min.js"></script>';
    echo '<script src="js/parallax.min.js"></script>';
    echo '</body>';
    echo '</html>';
    echo '';
  }

  
?>
