<?php
require_once 'rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

// Start session before any output
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

// If the user is already logged in, redirect them to the home page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: index.php");
    exit;
}

// Initialize login success flag
$loginSuccess = false;
$credential_error = '';

if (isset($_POST['submitted'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Pulling a certain section of the database into the scope of the code
    $credentials = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result_login = $conn->query($credentials);

    // Validating credentials
    if ($result_login === false) {
        // Display SQL error
        echo "Error: " . $conn->error;
    } else {
        if ($result_login->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result_login)) {
                if ($row['status'] == 1) {
                    $loginSuccess = true;

                    // Set session variables
                    $_SESSION['userID'] = $row['ID'];
                    $_SESSION['role'] =  $row['roleId'];
                    $_SESSION['loggedin'] = true;

                    // Regenerate the session ID
                    session_regenerate_id(true);

                    // Generate a random token
                    $token = bin2hex(random_bytes(16));

                    // Store the token in the session variable
                    $_SESSION['token'] = $token;

                    if ($rbac->getRoleNameFromId($row['roleId']) == "admin" || $rbac->getRoleNameFromId($row['roleId']) == "superadmin") {
                        header("location: admin/dashboard.php");
                        exit;
                    } else {
                        // Redirecting the user to the home page
                        header("location: index.php");
                        exit;
                    }
                } else {
                    // User is inactive, set the loginSuccess flag to false
                    $credential_error = "Your account is inactive.";
                }
            }
        } else {
            // Email and password do not match data in the database, set the loginSuccess flag to false
            $credential_error = "Email and password are incorrect.";
        }
    }

    // Close the database connection
    $conn->close();
}

// Display the HTML content
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />
    <link href="css/all.min.css" rel="stylesheet" />
    <link href="css/templatemo-style.css" rel="stylesheet" />
    <link href="css/custom.css" media="all" rel="stylesheet" />
</head>
<body>
    <!-- Header + Navigation Bar -->
    <div class="container">
        <!-- Top box -->
        <!-- Logo & Site Name -->
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
                                <li class="tm-nav-li"><a href="index.php" class="custom-link">Home</a></li>
                                <li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>
                                <li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>
                                <?php
                                if (!isset($_SESSION['userID'])) {
                                    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link active">Login/Register</a></li>';
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

        <!-- Login Form -->
        <main>
            <header class="row tm-welcome-section">
                <h2 class="col-12 text-center tm-section-title">Login to your Account</h2>
            </header>
            
            <div class="tm-container-login" style="width: 70%">
                <form action="login.php" method="POST" class="tm-login-form">
                    <?php
                    // Display error message if login fails
                    if (isset($_POST['submitted']) && !$loginSuccess) {
                        if ($credential_error == "Your account is inactive.") {
                            echo '<div class="alert alert-danger" role="alert" style="border: 2px solid #dc3545; padding: 10px; margin-top: 20px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">';
                            echo 'Your account is inactive. Please contact the administrator to activate your account.';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert" style="border: 2px solid #dc3545; padding: 10px; margin-top: 20px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">';
                            echo 'The provided email and password are incorrect.';
                            echo '</div>';
                        }
                    }
                    ?>

                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required="" />
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required="" />
                    </div>

                    <div class="form-group tm-d-flex">
                        <button type="submit" class="tm-btn tm-btn-success tm-btn-right">
                            <input type="hidden" name="submitted" value="true">
                            Submit
                        </button>
                    </div>
                </form>

                <p> No account? <a href="register.php">Register one now!</a></p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="tm-footer text-center">
            <p>Copyright &copy; 2020 Simple House | Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>
        </footer>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/parallax.min.js"></script>
</body>
</html>
