<?php
error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

//pulling a certain section of the database into the scope of the code
$credentials = "SELECT email, password FROM users WHERE email = '".$email."' AND password = '".$password."'";
$result_login = mysqli_query($conn, $credentials) or die(mysqli_error($conn));

//if user is already logged in, redirect them to home page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    //header("location: index.php");
    exit;
}

if(isset($_POST['submitted'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    //validating credentials
    if($login_status = mysqli_num_rows($result_login) > 0){
        //if credentials match the system, start a new session
        session_start();

        //telling the system that the user is entitled to be logged in
        $_SESSION["loggedin"] = true;

        //redirecting user to home page
        header("location: index.php");
    }
    else{
        //display error message if email and password do not match data in the database
        $credential_error = "Email and password are incorrect";
    }

    if ($conn->query($query) === TRUE) {
        $isSuccess = true;
    }

    $conn->close();


}
else{
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '';
    echo '<head>';
    echo '<meta charset="UTF-8" />';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
    echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
    echo '<title>Simple House - Contact Page</title>';
    echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />';
    echo '<link href="css/all.min.css" rel="stylesheet" />';
    echo '<link href="css/templatemo-style.css" rel="stylesheet" />';
    echo '<link href="css/custom.css" media="all" rel="stylesheet" />';
    echo '</head>';
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

    //login form
    echo '<main>';
    echo '<header class="row tm-welcome-section">';
    echo '<h2 class="col-12 text-center tm-section-title">Login to your Account</h2>';
    echo '</header>';
    echo '';
    echo '<div class="tm-container-login" style="width: 70%">';
    echo '<form action="login.php" method="POST" class="tm-login-form">';
    echo '';
    echo '<div class="form-group">';
    echo '<input type="email" name="email" class="form-control" placeholder="Email" required="" />'; //email
    echo '</div>';
    echo '';
    echo '<div class="form-group">';
    echo '<input type="text" name="password" class="form-control" placeholder="Password" required="" />'; //password
    echo '</div>';
    echo '';
    echo '<div class="form-group tm-d-flex">';
    echo '<button type="submit" class="tm-btn tm-btn-success tm-btn-right">';
    echo '<input type="hidden" name="submitted" value="true">'; //letting the file know that the submit button has been clicked
        //if credentials are correct, redirect to home page. if not, display error message
        if(!empty($_POST['submitted'])){
            $login_status;
        }
    echo 'Submit';
    echo '</button>';
    echo '</div>';
    echo '</form>';
    echo '<p> No account? <a href="register.php">Register one now!</a></p>';
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
}
?>
