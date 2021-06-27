<?php
error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session
if(!isset($_SESSION['userID'])){
    echo'<p>Failed to run session!</p>';
}

$dbServername = "db4free.net";
$dbUsername = "gotit_db";
$dbPassword = "sqlDatabase143";
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
echo '<title>GotIt Lost and Found</title>';
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
echo '<li class="tm-nav-li"><a href="index.php" class="tm-nav-link active">Home</a></li>';
echo '<li class="tm-nav-li"><a href="login.php" class="tm-nav-link">Login/Register</a></li>';
echo '<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link">Dashboard</a></li>';
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

echo "<h2 align=\"left\" class=\"col-12 tm-section-title\" style=\"margin-top:75px; margin-left:120px\"><b>Matched Items</b></h2>";

if (isset($_GET['lostID'])) {
    $lostID = $_GET['lostID'];
}
else{
    echo "<p class=\"itemInfo\"><b>Unable to get parameter!</b></p>";
}
$sql_matchedItems = "SELECT * FROM matched_items
                    WHERE lost_id = $lostID";
$retval_matchedItems = mysqli_query($conn, $sql_matchedItems);

if(!$retval_matchedItems){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}

$result_matchedItems = mysqli_query($conn, $sql_matchedItems) or die(mysqli_error($conn));

if (mysqli_num_rows($result_matchedItems) > 0) {
    while($row = mysqli_fetch_assoc($result_matchedItems)){
    $foundItemID = $row['found_id'];

    $sql_foundItems = "SELECT * FROM found_items
                        WHERE ID = $foundItemID";
    $retval_foundItems = mysqli_query($conn, $sql_foundItems);
    if(!$retval_foundItems){echo '<p class=\"itemInfo\"><b>Error displaying item data...</b></p>';}
    $result_foundItems = mysqli_query($conn, $sql_foundItems) or die(mysqli_error($conn));
    
    if (mysqli_num_rows($result_foundItems) > 0){
        while($row = mysqli_fetch_assoc($result_foundItems)){
            $userID = $row['userID'];
            $image = $row["image"];
            echo '<div class="custom-item-profile">';
            echo '<div style="float:left;" class="custom-div-section item-section extra-margin-left">';
            echo "<h2 align=\"left\" class=\"col-12 tm-section-title\" style=\"margin-bottom:10px\"><b>{$row['itemName']}</b></h2>";
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
        echo '<div style="float:right;" class="custom-div-section item-section extra-margin-right">';
        echo '<img class="custom-item-image" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
        echo '</div>';
        echo '</div>';
    }
    }
}
else{
    echo "<p style=\"margin-left: 15px;\"><b>Unable to fetch item data!</b></p>";
}

mysqli_close ($conn);

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
?>
