<?php
require_once 'rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session
$_SESSION['userID'];
if(!isset($_SESSION['userID'])){
    header("Location:login.php");
}

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);


$STATUS_PENDING = "PENDING MATCH";
$STATUS_MATCHED = "ITEM MATCHED";
$STATUS_FOUND = "FOUND";
$STATUS_APPROVED = "APPROVED BY ADMIN";


if(isset($_POST['submitted'])){
  
  $permissions = $rbac->getPermissions($_SESSION["role"]);

  $action = $_POST['submit'];
  $foundID = $_POST['foundID'];

  if($rbac->hasPermission("resolve_report", $permissions)){
    if(isset($foundID)){
      $update = "UPDATE found_items SET status = 3 WHERE id = '$foundID'";
                header("Refresh:0");
    }
    else{
      $itemID = $_POST['itemID'];
      $update = "UPDATE lost_items SET status = 3 WHERE id = '$itemID'";
                header("Refresh:0");
    }
  
    $isUpdateSuccess = mysqli_query($conn, $update) or die(mysqli_error($conn));
  }
  else{
    echo '<span>NO PERMISSIONS</span>';
  }

  
}

echo '<!DOCTYPE html>';
echo '<html>';
echo '';
echo '<head>';
echo '<meta charset="UTF-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
echo '<title>Got It - Dashboard</title>';
echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />';
echo '<link href="css/templatemo-style.css" media="all" rel="stylesheet" />';
echo '<link href="css/custom.css?v=<?php echo time(); " media="all" rel="stylesheet" />';
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
echo '<div class="container" style="overflow: hidden">';
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
echo '<li class="tm-nav-li"><a href="index.php" class="tm-nav-link">Home</a></li>';
echo '<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>';
if(!isset($_SESSION['userID'])){
    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
}
else{
  echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link active">Dashboard</a></li>';
}
echo '</ul>';
echo '</nav>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '';
echo '<main style="height: 1000px">';
$id = $_SESSION['userID'];
$sql_userInfo = "SELECT * FROM users
                 WHERE ID = $id";
$retval_userInfo = mysqli_query($conn, $sql_userInfo);

if(!$retval_userInfo){echo '<p class=\"itemInfo\"><b>Error displaying user data...</b></p>';}

$result_userInfo = mysqli_query($conn, $sql_userInfo) or die(mysqli_error($conn));

if (mysqli_num_rows($result_userInfo) > 0) {
  while($row = mysqli_fetch_assoc($result_userInfo)){
    echo '<div class="custom-item-profile">';
    echo '<div style="float:left;" class="custom-div-section item-section extra-margin-left">';
    echo "<h2 float=\"left\" class=\"tm-section-title\"><b>User Profile</b></h2>";
    echo '<p><b>Username:</b> ' . $row['username']; '</p>';
    echo '<p><b>Email:</b> ' . $row['email']; '</p>';
    echo '<p><b>Contact Number:</b> ' . $row['contact_no']; '</p>';
    echo '<p><b>Address:</b> ' . $row['address']; '</p>';
    echo '<br/>';
    echo '<a href="logout.php">Logout of your account</a>';
    echo '</div>';
    echo '</div>';
  }
}

echo '<div class="custom-item-profile">';
echo '<div style="float:left; margin-top:-120px;" class="custom-div-section extra-margin-left">';

$sql_lostItems = "SELECT * FROM lost_items
                 WHERE userID = $id AND status != 3";
$retval_lostItems = mysqli_query($conn, $sql_lostItems);

if(!$retval_lostItems){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}

$result_lostItems = mysqli_query($conn, $sql_lostItems) or die(mysqli_error($conn));

echo "<h2 align=\"left\" class=\"tm-section-title\" style=\"margin-bottom:10px\"><b>Submitted Lost Items</b></h2>";
echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page">';
if (mysqli_num_rows($result_lostItems) > 0) {
  while($row = mysqli_fetch_assoc($result_lostItems)){
    $itemID = $row['ID'];
    $image = $row["image"];

    echo '<article class="custom-item-container" style="overflow-x: hidden; height:auto;">';
    if($image != null){
        echo '<img class="custom-item-thumbnail" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
    }
    else{
        echo '<img class="custom-item-thumbnail" src="img/nip.jpg"/>';
    }
    echo "<h4 class=\"tm-gallery-title\">{$row['itemName']}</h4>";
    echo "<p style='overflow-x: hidden;' class=\"tm-gallery-description\">{$row['description']}</p>";
    switch($row['status']){
      case 0:
        echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_PENDING}</p>";
        break;
      case 1:
        echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_MATCHED}</p>";
        break;
      case 2:
        echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_APPROVED}</p>";
        break;
    }
    echo "<a class='custom-link button'  style='margin-top:30px;' href=\"item.php?itemInfoID=$itemID\">See item</a>";
    if ($row['status'] == 2){
        echo "<a class='custom-link button'  style='margin-top:30px;margin-left:0px;' href=\"matched_item.php?lostID=$itemID\">See matched</a>";
    }
    if ($row['status'] != 3){
      echo "<form action=\"dashboard.php\" method=\"POST\">";
      echo "<input type=\"hidden\" name=\"itemID\" value=\"$itemID\"/>";
      if ($row['status'] == 1 || $row['status'] == 2){
        echo "<input type=\"Submit\" style=\"margin-top:10px;width:100px; float:left;margin-right:50px; font-family:'Open Sans', Arial, sans-serif; font-size: 17px;\" class=\"custom-button\" name=\"submit\" value=\"Resolve\"/>";
      }
      else{
        echo "<input type=\"Submit\" style=\"margin-top:-38px;width:100px; float:right;margin-right:50px; font-family:'Open Sans', Arial, sans-serif; font-size: 17px;\" class=\"custom-button\" name=\"submit\" value=\"Resolve\"/>";
      }

      echo "<input type=\"hidden\" name=\"submitted\" value=\"true\"/>";
      echo "</form>";
    }
    echo '</article>';
    }
}
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="custom-item-profile">';
echo '<div style="float:left; margin-top:auto;" class="custom-div-section extra-margin-left">';


$sql_foundItems = "SELECT * FROM found_items
                 WHERE userID = $id";
$retval_foundItems = mysqli_query($conn, $sql_foundItems);

if(!$retval_foundItems){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}

$result_foundItems = mysqli_query($conn, $sql_foundItems) or die(mysqli_error($conn));

echo "<h2 align=\"left\" class=\"tm-section-title\" style=\"margin-bottom:10px\"><b>Submitted Found Items</b></h2>";
echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page" >';
if (mysqli_num_rows($result_foundItems) > 0) {
  while($row = mysqli_fetch_assoc($result_foundItems)){
    if ($row['status']!=3){
      $foundItemID = $row['ID'];
      $image = $row["image"];

      echo '<article class="custom-item-container" style="overflow-x: hidden; height:auto;">';
      if($image != null){
          echo '<img class="custom-item-thumbnail" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
      }
      else{
          echo '<img class="custom-item-thumbnail" src="img/nip.jpg"/>';
      }
      echo "<h4 class=\"tm-gallery-title\">{$row['itemName']}</h4>";
      echo "<p style='overflow-x: hidden;' class=\"tm-gallery-description\">{$row['description']}</p>";
      switch($row['status']){
        case 0:
          echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_PENDING}</p>";
          break;
        case 1:
          echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_MATCHED}</p>";
          break;
        case 2:
          echo "<p class=\"tm-gallery-description\"><b>Status: </b>{$STATUS_APPROVED}</p>";
          break;
      }
      echo "<a class='custom-link button'  style='margin-top:30px;' href=\"founditem.php?itemInfoID=$foundItemID\">See item</a>";
      if ($row['status'] != 3){
        echo "<form action=\"dashboard.php\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"foundID\" value=\"$foundItemID\"/>";
        echo "<input type=\"Submit\" style=\"margin-top:-38px;width:100px; float:right;margin-right:50px; font-family:'Open Sans', Arial, sans-serif; font-size: 17px;\" class=\"custom-button\" name=\"submit\" value=\"Resolve\"/>";
        echo "<input type=\"hidden\" name=\"submitted\" value=\"true\"/>";
        echo "</form>";
      }
      echo '</article>';
    }
    }
}
echo '</div>';
echo '</div>';
echo '</div>';


echo '</main>';
echo '';
echo '<footer class="tm-footer text-center">';
echo '';
echo '</footer>';
echo '</div>';
echo '<script src="js/jquery.min.js"></script>';
echo '<script src="js/parallax.min.js"></script>';
echo '</body>';
echo '</html>';
?>
