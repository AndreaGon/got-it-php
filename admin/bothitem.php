<?php
require_once '../rbac.php';
$rbac = new RBAC();

session_start();
error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session
if(!isset($_SESSION['userID'])){
    header("Location: ../login.php");
}

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

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
    echo '<title>Got It - Matched - Admin</title>';
    echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />';
    echo '<link href="../css/templatemo-style.css" media="all" rel="stylesheet" />';
    echo '<link href="../css/custom.css?v=<?php echo time(); " media="all" rel="stylesheet" />';
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
    echo '<div class="container custom-div-height">';
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

    echo "<style>";
    echo "  .itemInfo{";
    echo "    margin-left: 15px";
    echo "}";
    echo "  .contact_additional2{";
    echo "    margin-left: 83px";
    echo "}";
    echo "</style>";

    if (isset($_POST['lostID']) && isset($_POST['foundID'])) {
        $lostID = $_POST['lostID'];
        $foundID = $_POST['foundID'];
    }
    else{
        echo "<p class=\"itemInfo\"><b>Unable to get parameter!</b></p>";
    }
    $sql_itemInfo = "SELECT * FROM lost_items
                    WHERE ID = $lostID";

    $retval_itemInfo = mysqli_query($conn, $sql_itemInfo);

    if(!$retval_itemInfo){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}

    $result_itemInfo = mysqli_query($conn, $sql_itemInfo) or die(mysqli_error($conn));

    echo '<div class="custom-item-profile">';
    echo "<h1 align=\"left\" class=\"col-12\" style=\"margin-bottom:10px\"><b>Compare Items</b></h1>";
    if (mysqli_num_rows($result_itemInfo) > 0) {
        while($row = mysqli_fetch_assoc($result_itemInfo)){
        $userID = $row['userID'];
        $image = $row["image"];
        echo '<div style="float:left; width: 400px;" class="custom-div-section item-section extra-margin-left">';
        echo "<h2 align=\"left\" class=\"col-12\" style=\"margin-bottom:10px\"><b>Lost Item</b></h2>";
        if($image != null){
            echo '<img class="custom-item-image-medium" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
        }
        else{
            echo '<img class="custom-item-image-medium" src="img/nip.jpg"/>';
        }
        echo "<h3 align=\"left\" class=\"col-12\" style=\"margin-bottom:10px\"><b>{$row['itemName']}</b></h3>";
        echo "<p class=\"itemInfo\"><b>Category:</b> {$row['category']}</p>";
        echo "<p class=\"itemInfo\"><b>Color:</b> {$row['color']}</p>";
        echo "<p class=\"itemInfo\"><b>Brand:</b> {$row['brand']}</p>";
        echo "<p class=\"itemInfo\"><b>Description:</b> {$row['description']}</p>";
        echo "<p class=\"itemInfo\"><b>Lost Date:</b> {$row['lost_date']}</p>";
        echo "<p class=\"itemInfo\"><b>Lost Time:</b> {$row['lost_time']}</p>";
        echo "<p class=\"itemInfo\"><b>Location Lost:</b> {$row['location']}</p>";


        $sql_contact = "SELECT * FROM users
                        WHERE ID = $userID";
        $retval_contact = mysqli_query($conn, $sql_contact);
        if(!$retval_contact){
            echo "<p class=\"itemInfo\"><b>Unable to retrieve contact data!</b></p>";
        }
        $result_contact = mysqli_query($conn, $sql_contact) or die(mysqli_error($conn));
        if (mysqli_num_rows($result_contact) > 0){
            while($row = mysqli_fetch_assoc($result_contact)){
            echo "<p class=\"itemInfo\"><b>Contact:</b> {$row['email']}</p>";
            echo "<p class=\"contact_additional2\">{$row['contact_no']}</p>";
            echo "<p class=\"contact_additional2\" style=\"margin-bottom: 30px;\">{$row['address']}</p>";
            }
        }
        else{
            echo "<p class=\"itemInfo\"><b>Unable to fetch contact data!</b></p>";
        }

        }
        echo '<a href="admin.php" class="custom-link">Back to Admin Dashboard</a>';
        echo '</div>';
    }
    else{
        echo "<p style=\"margin-left: 15px;\"><b>Unable to fetch item data!</b></p>";
    }

    $sql_foundInfo = "SELECT * FROM found_items
                    WHERE ID = $foundID";

    $retval_foundInfo = mysqli_query($conn, $sql_foundInfo);

    if(!$retval_foundInfo){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}

    $result_foundInfo = mysqli_query($conn, $sql_foundInfo) or die(mysqli_error($conn));

    if (mysqli_num_rows($result_foundInfo) > 0) {
        while($row = mysqli_fetch_assoc($result_foundInfo)){
        $userID = $row['userID'];
        $image = $row["image"];
        echo '<div style="float:right; width: 400px;" class="custom-div-section item-section extra-margin-right">';
        echo "<h2 align=\"left\" class=\"col-12\" style=\"margin-bottom:10px\"><b>Found Item</b></h2>";
        if($image != null){
            echo '<img class="custom-item-image-medium" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
        }
        else{
            echo '<img class="custom-item-image-medium" src="../img/nip.jpg"/>';
        }

        echo "<h3 align=\"left\" class=\"col-12\" style=\"margin-bottom:10px\"><b>{$row['itemName']}</b></h3>";
        echo "<p class=\"itemInfo\"><b>Category:</b> {$row['category']}</p>";
        echo "<p class=\"itemInfo\"><b>Color:</b> {$row['color']}</p>";
        echo "<p class=\"itemInfo\"><b>Brand:</b> {$row['brand']}</p>";
        echo "<p class=\"itemInfo\"><b>Description:</b> {$row['description']}</p>";
        echo "<p class=\"itemInfo\"><b>Lost Date:</b> {$row['found_date']}</p>";
        echo "<p class=\"itemInfo\"><b>Lost Time:</b> {$row['found_time']}</p>";
        echo "<p class=\"itemInfo\"><b>Location Lost:</b> {$row['location']}</p>";


        $sql_contact = "SELECT * FROM users
                        WHERE ID = '$userID'";
        $retval_contact = mysqli_query($conn, $sql_contact);
        if(!$retval_contact){
            echo "<p class=\"itemInfo\"><b>Unable to retrieve contact data!</b></p>";
        }
        $result_contact = mysqli_query($conn, $sql_contact) or die(mysqli_error($conn));
        if (mysqli_num_rows($result_contact) > 0){
            while($row = mysqli_fetch_assoc($result_contact)){
            echo "<p class=\"itemInfo\"><b>Contact:</b> {$row['email']}</p>";
            echo "<p class=\"contact_additional2\">{$row['contact_no']}</p>";
            echo "<p class=\"contact_additional2\">{$row['address']}</p>";
            }
        }
        else{
            echo "<p class=\"itemInfo\"><b>Unable to fetch contact data!</b></p>";
        }

        }
        echo '</div>';
    }
    else{
        echo "<p style=\"margin-left: 15px;\"><b>Unable to fetch item data!</b></p>";
    }

    echo '</div>';


    mysqli_close ($conn);

    echo '</main>';
    echo '';
    echo '<footer class="tm-footer text-center">';
    echo '</footer>';
    echo '</div>';
    echo '<script src="../js/jquery.min.js"></script>';
    echo '<script src="../js/parallax.min.js"></script>';
    echo '</body>';
    echo '</html>';
}


?>
