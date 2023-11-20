<?php
require_once 'rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session

if (!isset($_SESSION['userID']) || !isset($_SESSION['token'])) {
  header("Location:login.php");
}

// set the session timeout to 30 minutes (1800 seconds)
$sessionTimeout = 1800;

// check if the session has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
  // session expired, destroy the session and redirect to login
  session_unset();
  session_destroy();

  // display an alert using JavaScript
  echo '<script>alert("Session expired. Please log in again.");</script>';

  // redirect to login page
  echo '<script>window.location.href = "login.php";</script>';

  exit();
}

// update the last activity timestamp
$_SESSION['last_activity'] = time();

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

if (isset($_POST['submitted'])) {
  if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['token']) {
    $permissions = $rbac->getPermissions($_SESSION["role"]);
    $isSuccess = false;

    $itemName = $_POST['item'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    if (empty($color)) $color = NULL;
    if (empty($brand)) $brand = NULL;

    if ($rbac->hasPermission("add_lost_item", $permissions)) {


      $query = "INSERT INTO lost_items (userID, itemName, category, color, brand, description, lost_date, lost_time, location, image, status)
      VALUES " . "('" . $_SESSION['userID'] . "','" . $itemName . "','" . $category . "','" . $color . "', '" . $brand . "', '" . $description . "', '" . $date . "', '" . $time . "', '" . $location . "','" . $image . "'," . '0' . ")";

      if ($conn->query($query) === TRUE) {
        $isSuccess = true;
      }
    } else {
      echo '<span>NO PERMISSIONS</span>';
    }


    $conn->close();
  } else {
    // prompt user to login again
    echo '<script>alert("Invalid session token. Please log in and try again.");</script>';

    // redirect to login page
    echo '<script>window.location.href = "login.php";</script>';
  }
}

echo '<!DOCTYPE html>';
echo '<html>';
echo '';
echo '<head>';
echo '<meta charset="UTF-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
echo '<title>Got It - Lost Item Form</title>';
echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />';
echo '<link href="css/templatemo-style.css" media="all" rel="stylesheet" />';
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
echo '<li class="tm-nav-li"><a href="index.php" class="custom-link">Home</a></li>';
echo '<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link active">Lost Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>';
if (!isset($_SESSION['userID'])) {
  echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
} else {
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
echo '<h2 class="col-12 text-center tm-section-title">Submit a Lost Item</h2>';
echo '</header>';
echo '';
echo '';
echo '<!-- Gallery -->';
echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page">';
echo '<form action="lostitemform.php" method="POST" enctype="multipart/form-data">';
if (isset($_POST['submitted'])) {
  if ($isSuccess) {
    echo '<span style="display: block; backgorund-color: #9ffa91; padding: 20px;">Item reported successfully</span>';
  } else {
    echo '<span>Item report failed</span>';
  }
}
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '">';
echo '<div style="float:left;" class="custom-div-section">';
echo '<div class="custom-section">';
echo '<h4>Name of Item Lost</h4>';
echo '<input class = "custom-input" type="text" name = "item" placeholder="Name of Item Lost (Wallet, Pen, Notebook, etc.)"/>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Category</h4>';
echo '<select name = "category" class="custom-select">';
echo '<option disabled selected>Select...</option>';
echo '<option value="Accessories">Accessories</option>';
echo '<option value="Clothing">Clothing</option>';
echo '<option value="Electronic Device">Electronic Device</option>';
echo '<option value="Identification Item">Identification Item</option>';
echo '<option value="Other Items">Other Items</option>';
echo '<option value="School Stationary">School Stationary</option>';
echo '<option value="Textbook">Textbook</option>';
echo '<option value="Wallets">Wallets</option>';
echo '</select>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Color (optional)</h4>';
echo '<input class = "custom-input" name = "color" type="text" placeholder="Color (optional)"/>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Brand (optional)</h4>';
echo '<input class = "custom-input" name = "brand" type="text" placeholder="Brand (optional)"/>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Description</h4>';
echo '<textarea rows="10" cols="55" name = "description" class = "custom-textarea"></textarea>';
echo '</div>';
echo '<div class="custom-section">';
echo '<input type="Submit" class="custom-button submit" value="Submit"/>';
echo '<input type=\'hidden\' name=\'submitted\' value=\'true\'/>';
echo '</div>';
echo '</div>';
echo '<div style="float:right;" class="custom-div-section">';
echo '<div class="custom-section">';
echo '<h4>Date Lost</h4>';
echo '<input class = "custom-input" type="date" name="date"/>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Time Lost</h4>';
echo '<input class = "custom-input" type="time" name="time"/>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Location</h4>';
echo '<select class="custom-select" name="location">';
echo '<option disabled selected>Select...</option>';
echo '<option value="Billiard">Billiard</option>';
echo '<option value="Cafeteria">Cafeteria</option>';
echo '<option value="Car Park">Car Park</option>';
echo '<option value="Convenience Store">Convenience Store</option>';
echo '<option value="Lecture Hall">Lecture Hall</option>';
echo '<option value="Level 2 Lecture Rooms">Level 2 Lecture Rooms</option>';
echo '<option value="Level 3 Lecture Rooms">Level 3 Lecture Rooms</option>';
echo '<option value="Level 4 Lecture Rooms">Level 4 Lecture Rooms</option>';
echo '<option value="Level 5 Lecture Rooms">Level 5 Lecture Rooms</option>';
echo '<option value="Level 6 Lecture Rooms">Level 6 Lecture Rooms</option>';
echo '<option value="Library">Library</option>';
echo '<option value="Music Room">Music Room</option>';
echo '<option value="Reception">Reception</option>';
echo '</select>';
echo '</div>';
echo '<div class="custom-section">';
echo '<h4>Item image</h4>';
echo '<input class = "custom-input" name="image" type="file" accept="image/*"/>';
echo '</div>';
echo '</div>';
echo '</form>';
echo '';
echo '</div>';
echo '</main>';
echo '';
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
