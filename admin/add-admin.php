<?php
  require_once '../rbac.php';
  $rbac = new RBAC();

  error_reporting(E_ALL);  //give warning if session cannot start
  session_start(); //start the session
  if(!isset($_SESSION['userID']) || !isset($_SESSION['token'])){
      header("Location: ../login.php");
  }

  $dbServername = "localhost";
  $dbUsername = "root";
  $dbPassword = "";
  $dbName = "gotit_db";
  $dbPort = 3306;

  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);
  
  $permissions = $rbac->getPermissions($_SESSION["role"]);

  if(isset($_POST['submitted'])){

    $adminName = $_POST['adminName'];
    $role_name = $_POST['role'];
    $contact_number = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(
      $adminName == "" ||
      $role_name == "" ||
      $contact_number == "" ||
      $email == "" ||
      $password == ""
    ){
      $_SESSION['toast_message'] = 'Please fill in all details.';
      $isSuccess = false;
    }
    else{
      $checking_user_existence = "SELECT * FROM users WHERE email='$email'";
      $result_register = mysqli_query($conn, $checking_user_existence) or die(mysqli_error($conn));

      if(mysqli_num_rows($result_register) > 0){
        $_SESSION['toast_message'] = 'Email has been used.';
        $isSuccess = false;
      }
      else{
          //if user does not exist in database
          $query = "INSERT INTO users (username, email, password, contact_no, roleId)
          VALUES " . "('" .$adminName. "','" .$email. "','" .$password. "','" .$contact_number. "', '" .$rbac->getRoleIdFromName($role_name). "')";


          if ($conn->query($query) === TRUE) {
              $isSuccess = true;
          }

          $_SESSION['toast_message'] = 'Successfully registered admin';
      }

      $conn->close();
    }
  }

  
  if($rbac->getRoleNameFromId($_SESSION['role']) == 'user' || $rbac->getRoleNameFromId($_SESSION['role']) == 'admin'){
    echo '<script>
    alert("Invalid access!");
    window.location.href="../index.php";
    </script>'; 
  }
  else{
    if($rbac->hasPermission("add_admins", $permissions)){
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
      echo '<h2 class="col-12 text-center tm-section-title">Register Admins</h2>';
      echo '</header>';
      echo '';
      echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page">';
      if (isset($_SESSION['toast_message'])) {
        if($isSuccess){
          echo '<div class="success-message" style="display: block; margin-left: 50px;">' . $_SESSION['toast_message'] . '</div>';
        }
        else{
          echo '<div class="error-message" style="display: block; margin-left: 50px;">' . $_SESSION['toast_message'] . '</div>';
        }
        
        // Clear the success message after displaying it
        unset($_SESSION['toast_message']);
      }

      echo '<form action="add-admin.php" method="POST" enctype="multipart/form-data">';

      echo '<div style="float:left;" class="custom-div-section">';
      echo '<div class="custom-section">';
      echo '<h4>Name of Admin</h4>';
      echo '<input class = "custom-input" type="text" name = "adminName" placeholder="Ex. Derek Hans"/>';
      echo '</div>';

      echo '<div class="custom-section">';
      echo '<h4>Contact no.</h4>';
      echo '<input class = "custom-input" type="text" name = "contact" placeholder="Ex. Derek Hans"/>';
      echo '</div>';
      
      echo '<div class="custom-section">';
      echo '<h4>Password</h4>';
      echo '<input class = "custom-input" name = "password" type="password" placeholder="Admin password"/>';
      echo '</div>';
      
      echo '<div class="custom-section">';
      echo '<input type="Submit" class="custom-button submit" value="Submit"/>';
      echo '<input type=\'hidden\' name=\'submitted\' value=\'true\'/>';
      echo '</div>';
      echo '</div>';
      echo '<div style="float:right;" class="custom-div-section">';
      echo '<div class="custom-section">';
      echo '<h4>Role</h4>';
      echo '<select name = "role" class="custom-select">';
      echo '<option value="admin" selected>Admin</option>';
      echo '<option value="superadmin">Superadmin</option>';
      echo '</select>';
      echo '</div>';

      echo '<div class="custom-section">';
      echo '<h4>Email</h4>';
      echo '<input class = "custom-input" name = "email" type="text" placeholder="Ex. derek@mail.com"/>';
      echo '</div>';

      echo '</div>';
      echo '</form>';
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
    else{
      echo '<script>
      alert("No permission to add admins!");
      </script>'; 
    }
}
  
?>
