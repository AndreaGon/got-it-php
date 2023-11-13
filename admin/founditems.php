<?php
  session_start();
  $dbServername = "localhost";
  $dbUsername = "root";
  $dbPassword = "";
  $dbName = "gotit_db";
  $dbPort = 3306;
  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

  $query = "SELECT
  found_items.id AS found_id,
  found_items.userID AS found_user_id,
  found_items.itemName AS found_name,
  found_items.status AS status,
  users.id AS user_id,
  users.email AS email
  FROM found_items
  INNER JOIN users ON
  found_items.userID = users.id";

  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

  if(isset($_POST['submitted'])){
    $foundID = $_POST['foundId'];
    $isDelete = $_POST['submit'];
    $delete = "DELETE FROM found_items WHERE id = " . $foundID;
    $isUpdateSuccess = mysqli_query($conn, $delete) or die(mysqli_error($conn));
  }

  $STATUS_PENDING = 0;
  $STATUS_MATCHED = 1;
  $STATUS_APPROVED = 2;
  $STATUS_RESOLVED = 3;

  if($_SESSION["role"] == 'user'){
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
    echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link">Verify Item</a></li>';
    echo '<li class="tm-nav-li"><a href="lostitems.php" class="custom-link">Lost Items</a></li>';
    echo '<li class="tm-nav-li"><a href="founditems.php" class="custom-link active">Found Items</a></li>';
    echo '<li class="tm-nav-li"><a href="manageusers.php" class="custom-link">Manage Users</a></li>';
    if($_SESSION['role'] == "superadmin"){
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
      echo '<h2 class="col-12 text-center tm-section-title">Reported Found Items</h2>';
      echo '</header>';
      echo '';
      echo '<div class="custom-center" style="width: 100%">';
      echo '<table class = "custom-table" border="1" style="display:center;" width=1000 height=80>';
      echo '<tr>';
      echo '<th>ITEM ID</th>';
      echo '<th>ITEM NAME</th>';
      echo '<th>SUBMITTED BY</th>';
      echo '<th>STATUS</th>';
      echo '<th>ACTION</th>';
      echo '</tr>';

      while($row = $result->fetch_assoc()){
          echo '<tr>';
          echo '<td>'.$row['found_id'].'</td>';
          echo '<td>'.$row['found_name'].'</td>';
          echo '<td>'.$row['email'].'</td>';
          switch($row['status']){
            case $STATUS_PENDING:
              echo    '<td>Pending</td>';
              break;
            case $STATUS_MATCHED:
              echo    '<td>Matched</td>';
              break;
            case $STATUS_APPROVED:
              echo    '<td>Approved</td>';
              break;
            case $STATUS_RESOLVED:
              echo    '<td>Resolved</td>';
              break;
          }
          echo    '<td style="width:300px;">
                      <form action="founditemsinfo.php" method="POST" style="float: left;margin-left: 5px;margin-right:5px;">
                        <input type=\'hidden\' name="foundId" value="'.$row['found_id'].'"/>
                        <input type="Submit" class="custom-button" style="width:100px;" name="submit" value="Info"/>
                      </form>
                      <form action="founditems.php" method="POST">
                        <input type=\'hidden\' name="foundId" value="'.$row['found_id'].'"/>
                        <input type="Submit" class="custom-button" style="width:100px;" name="submit" value="Delete"/>
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