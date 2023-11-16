<?php
  require_once '../rbac.php';
  $rbac = new RBAC();
  
  session_set_cookie_params(0, '/', '', true, true);
  session_start();
  
  if(!isset($_SESSION['userID'])){
    header("Location: ../login.php");
  }

  $dbServername = "localhost";
  $dbUsername = "root";
  $dbPassword = "";
  $dbName = "gotit_db";
  $dbPort = 3306;
  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

  $query = "SELECT
  id AS admin_id,
  email AS admin_email,
  roleId AS admin_role,
  username AS admin_username
  FROM users WHERE roleId = 2 OR roleId = 3";

  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

  if(isset($_POST['submitted'])){
    $adminId = $_POST['adminId'];
    $isDelete = $_POST['submit'];
    $delete = "DELETE FROM users WHERE id = " . $adminId;
    $isUpdateSuccess = mysqli_query($conn, $delete) or die(mysqli_error($conn));
    header("Refresh:0");
  }

  
  if($rbac->getRoleNameFromId($_SESSION['role']) == 'user' || $rbac->getRoleNameFromId($_SESSION['role']) == 'admin'){
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
    echo '<li class="tm-nav-li"><a href="founditems.php" class="custom-link">Found Items</a></li>';
    echo '<li class="tm-nav-li"><a href="manageusers.php" class="custom-link">Manage Users</a></li>';
    if($rbac->getRoleNameFromId($_SESSION['role']) == "superadmin"){
      echo '<li class="tm-nav-li"><a href="manage-admin.php" class="custom-link active">Manage Admins</a></li>';
    }
    echo '<li class="tm-nav-li"><a href="../logout.php" class="custom-link">Logout</a></li>';

    echo '</ul>';
    echo '</nav>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<main>';
    echo '<header class="row tm-welcome-section">';
    echo '<h2 class="col-12 text-center tm-section-title">Manage Admins</h2>';
    echo '</header>';
    echo '';
    echo '<div class="custom-center" style="width: 100%">';

    echo '<table class = "custom-table" border="1" style="display:center;" width=1000 height=80>';

    echo '<a class="custom-button" style="text-decoration: none" href="add-admin.php">Register admin</a>';
    echo '<br/>';
    echo '<br/>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>NAME</th>';
    echo '<th>EMAIL</th>';
    echo '<th>ROLE</th>';
    echo '<th>ACTION</th>';
    echo '</tr>';
    while($row = $result->fetch_assoc()){
        echo '<tr>';
        echo    '<td>'.$row['admin_id'].'</td>';
        echo    '<td>'.$row['admin_username'].'</td>';
        echo    '<td>'.$row['admin_email'].'</td>';
        echo    '<td>'.$rbac->getRoleNameFromId($row['admin_role']).'</td>';

        if($row["admin_id"] != $_SESSION['userID']){
          echo    '<td style="width:300px;">
                    <center>
                      <form action="manage-admin.php" method="POST">
                      <input type=\'hidden\' name="adminId" value="'.$row['admin_id'].'"/>
                      <input type="Submit" class="custom-button" style="width:100px;" name="submit" value="Delete"/>
                      <input type=\'hidden\' name=\'submitted\' value=\'true\'/>
                      </form>
                    </center>
                </td>';
        }
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
