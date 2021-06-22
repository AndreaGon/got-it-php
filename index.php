<?php
error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session
if(!isset($_SESSION['userID'])){
    echo'<p>Failed to run session!</p>';
}

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";

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
echo '<li class="tm-nav-li"><a href="index.php" class="custom-link active">Home</a></li>';
echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
echo '<li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>';
echo '<li class="tm-nav-li"><a href="#" class="custom-link">Dashboard</a></li>';
echo '</ul>';
echo '</nav>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '';
echo '<main>';
echo '<header class="row tm-welcome-section">';
echo '<h2 class="col-12 text-center tm-section-title">Recent Lost Items</h2>';
echo '<p class="col-12 text-center"></p>';
echo '</header>';
echo '';
echo '';
echo '<!-- Gallery -->';
echo '<div class="row tm-gallery">';
echo '<!-- gallery page 1 -->';
echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page" style="margin-left: 150px;">';

//codes for normal data fetching
$sql = "SELECT * FROM lost_items";
$retval = mysqli_query($conn,$sql);
if(!$retval){
    echo "<p style=\"color:red;\">Unable to retreive data.</p>";
}
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$path = "/tmp/images";

//codes for counting numbers of lost items that have status 2 (claimed status)
$claimed_status = 2;
$sql_totalRows_status2 = "SELECT * FROM lost_items 
                          WHERE status = $claimed_status";
$retval_totalRows_status2 = mysqli_query($conn,$sql);
if(!$retval_totalRows_status2){
    echo "<p style=\"color:red;\">Unable to retreive data.</p>";
}
$result_totalRows_status2 = mysqli_query($conn, $sql_totalRows_status2) or die(mysqli_error($conn));
$count_status2 = $result_totalRows_status2->num_rows;

if (mysqli_num_rows($result) > 0 && (mysqli_num_rows($result) != $count_status2)) {
    while($row = mysqli_fetch_assoc($result)){
        if ($row['status']== 0 || $row['status'] == 1){
            $itemID = $row['ID'];
            $image = $row["image"];
    
            echo '<article class="custom-item-container">';
            echo '<img class="custom-item-thumbnail" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/>';
            echo "<h4 class=\"tm-gallery-title\">{$row['itemName']}</h4>";
            echo "<p class=\"tm-gallery-description\">{$row['description']}</p>";
            echo "<a class='custom-link button'  style='margin-top:30px;' href=\"item.php?itemInfoID=$itemID\">See item</a>";
            echo '</article>';
        }
    }
}
else {
    echo '<p><b>Nothing lost recently....</b></p>';
}


mysqli_close ($conn);


echo '</div> <!-- gallery page 1 -->';
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
?>
