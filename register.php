<?php
require_once 'rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session


$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);


echo '<!DOCTYPE html>';
echo '<html>';
echo '';
echo '<head>';
echo '<meta charset="UTF-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
echo '<title>Got It - Register</title>';
echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />';
echo '<link href="css/all.min.css" rel="stylesheet" />';
echo '<link href="css/templatemo-style.css" rel="stylesheet" />';
echo '<link href="css/custom.css" media="all" rel="stylesheet" />';
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
echo '<img class="tm-site-logo" width="150" src = "img/logo.png"/>';
echo '</div>';
echo '</div>';
echo '<nav class="col-md-6 col-12 tm-nav">';
echo '<ul class="tm-nav-ul">';
echo '<li class="tm-nav-li"><a href="index.php" class="custom-link active">Home</a></li>';
echo '<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>';
if(!isset($_SESSION['userID'])){
    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
}
else{
  echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link">Dashboard</a></li>';
}
echo '</ul>';
echo '</nav>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '';
echo '<main>';
echo '<header class="row tm-welcome-section">';
echo '<h2 class="col-12 text-center tm-section-title">Register Account</h2>';
echo '</header>';
echo '';

//register form
echo '<div class="tm-container-login" style="width: 70%">';
echo '<form action="register.php" method="POST" class="tm-login-form">';
//register form processing
if(isset($_POST['submitted'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact_number = $_POST['number'];
    $address = $_POST['address'];
    $role_query = "SELECT * FROM roles WHERE role_name='user'";
    $result_role_query = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
    $status = '1';

    //checking if user already exists
    $checking_user_existence = "SELECT * FROM users WHERE email='$email'";
    $result_register = mysqli_query($conn, $checking_user_existence) or die(mysqli_error($conn));

    //if user already exists
    if(mysqli_num_rows($result_register) > 0){
        echo "<span style=\"display: block; backgorund-color: #9ffa91; padding: 20px;\"><font color=\"red\">Email has been used.</font></span>";
    }
    else{

        $new_role = $rbac->getRoleIdFromName('user');
        
        //if user does not exist in database
        $query = "INSERT INTO users (username, email, password, contact_no, address, roleId, status)
                  VALUES " . "('" .$name. "','" .$email. "','" .$password. "','" .$contact_number. "', '" .$address. "','" .$new_role. "','" .$status. "')";

        $conn->query($query);
        echo "<span style=\"display: block; backgorund-color: #9ffa91; padding: 20px;\"><font color=\"green\">Successfully registered.</font></span>";
    }

    $conn->close();

}
echo '';
echo '<div class="form-group">';
echo '<input type="text" name="name" class="form-control" placeholder="Name" required="" />';
echo '</div>';
echo '';
echo '<div class="form-group">';
echo '<input type="email" name="email" class="form-control" placeholder="Email" required="" />';
echo '</div>';
echo '';
echo '<div class="form-group">';
echo '<input type="password" name="password" class="form-control" placeholder="Password" required="" />';
echo '</div>';
echo '';
echo '<div class="form-group">';
echo '<input type="text" name="number" class="form-control" placeholder="Contact Number" required="" />';
echo '</div>';
echo '';
echo '<div class="form-group">';
echo '<input type="text" name="address" class="form-control" placeholder="Address" required="" />';
echo '</div>';
echo '';
echo '<div class="form-group tm-d-flex">';
echo '<button type="submit" class="tm-btn tm-btn-success tm-btn-right">';
echo '<input type="hidden" name="submitted" value="true">';
echo 'Register';
echo '</button>';
echo '</div>';
echo '</form>';
echo '<p> Have an account? <a href="login.php">Login here instead!</a></p>';
echo '</div>';
echo '';
echo '</main>';
echo '';

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
?>