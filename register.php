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
$message = '';

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
?>
<?php
// Register Form Processing
if (isset($_POST['submitted'])) {
    $error = false;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $contact_number = mysqli_real_escape_string($conn, $_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role_query = "SELECT * FROM roles WHERE role_name='user'";
    $result_role_query = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
    $status = '1';
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    do {
        if (!$email) {
            $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"red\">Invalid Email Format</font></span>";
            break;
        }
    
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
    
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"red\">Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</font></span>";
            break;
        }
    
        // Validate contact number length
        if(!(strlen($contact_number) >= 10 && strlen($contact_number) <= 11 && substr($contact_number, 0, 2) == "01")) {
            $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"red\">Contact number should start with 01 and be 10 to 11 digits long.</font></span>";
            break;
        }

        if (!$error) {
            // Checking if user already exists
            $checking_user_existence = "SELECT * FROM users WHERE email='$email'";
            $result_register = mysqli_query($conn, $checking_user_existence);
        
            // If user already exists
            if (mysqli_num_rows($result_register) > 0) {
                $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"red\">Email has been used.</font></span>";
                $error = true;
            }
        
            if (!$error) {
                $new_role = $rbac->getRoleIdFromName('user');
        
                // If user does not exist in the database
                $query = "INSERT INTO users (username, email, password, contact_no, address, roleId, status)
                          VALUES " . "('" . $name . "','" . $email . "','" . $hashed_password . "','" . $contact_number . "', '" . $address . "','" . $new_role . "','" . $status . "')";
        
                if (!$conn->query($query)) {
                    $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"red\">Error inserting new user.</font></span>";
                    $error = true;
                }
        
                if (!$error) {
                    $message = "<span style=\"display: block; background-color: #9ffa91; padding: 20px;\"><font color=\"green\">Successfully registered.</font></span>";
                    echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 3000);</script>";
                }
            }
            $conn->close();
            break;
        }
    } while (false);


}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<title>Got It - Register</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />
<link href="css/all.min.css" rel="stylesheet" />
<link href="css/templatemo-style.css" rel="stylesheet" />
<link href="css/custom.css" media="all" rel="stylesheet" />
</head>
<body>

<div class="container">
<div class="custom-placeholder">
<div class="parallax-window">
<div class="tm-header">
<div class="row tm-header-inner">
<div class="col-md-6 col-12">
<div class="tm-site-text-box">
<img class="tm-site-logo" width="150" src="img/logo.png"/>
</div>
</div>
<nav class="col-md-6 col-12 tm-nav">
<ul class="tm-nav-ul">
<li class="tm-nav-li"><a href="index.php" class="custom-link active">Home</a></li>
<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>
<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>
<?php 
if (!isset($_SESSION['userID'])) {
    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
} else {
    echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link">Dashboard</a></li>';
}
?>
</ul>
</nav>
</div>
</div>
</div>
</div>

<main>
<header class="row tm-welcome-section">
<h2 class="col-12 text-center tm-section-title">Register Account</h2>
</header>


<div class="tm-container-login" style="width: 70%">
<form action="register.php" method="POST" class="tm-login-form" onsubmit="return validateForm()">
<?php echo $message; ?>
<div class="form-group">
<input type="text" name="name" class="form-control" placeholder="Name" required value="<?php (isset($username) ? $username : '')?>">
</div>

<div class="form-group">
<input type="email" name="email" class="form-control" placeholder="Email" required value="<?php (isset($email) ? $email : '')?>">
</div>

<div class="form-group">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>

<div class="form-group">
<input type="text" name="number" class="form-control" placeholder="Contact Number" required value="<?php (isset($contact_number) ? $contact_number : '')?>">
</div>

<div class="form-group">
<input type="text" name="address" class="form-control" placeholder="Address" required value="<?php (isset($address) ? $address: '')?>">
</div>

<div class="form-group tm-d-flex">
    <label>
        <input type="checkbox" id="privacyCheckbox"> I have read and accept the <a href="#" onclick="openPrivacyPolicy()">Privacy Policy</a>
    </label>
</div>

<div class="form-group tm-d-flex">
    <button type="submit" class="tm-btn tm-btn-success tm-btn-right">
        <input type="hidden" name="submitted" value="true">
        Register
    </button>
</div>

</form>
<p> Have an account? <a href="login.php">Login here instead!</a></p>
</div>
</main>


<footer class="tm-footer text-center">
    <p>Copyright &copy; 2020 Simple House
    | Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>
</footer>
</div>

<script>
function openPrivacyPolicy() {
    var privacyWindow = window.open("privacy_policy.php", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=800,height=600");
    privacyWindow.focus();
}

function validateForm() {
    var checkbox = document.getElementById("privacyCheckbox");
    if (!checkbox.checked) {
        alert("Please read and accept the Privacy Policy.");
        return false;
    }
    return true;
}
</script>

<script src="js/jquery.min.js"></script>
<script src="js/parallax.min.js"></script>
</body>
</html>




