<?php
  $dbServername = "db4free.net";
  $dbUsername = "gotit_db";
  $dbPassword = "sqlDatabase143";
  $dbName = "gotit_db";
  $dbPort = 3306;
  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);

  $query = "SELECT
  matched_items.id AS matched_id,
  matched_items.status AS matched_status,
  lost_items.id AS lost_id,
  found_items.id AS found_id,
  lost_items.itemName AS lost_name,
  found_items.itemName AS found_name
  FROM matched_items
  INNER JOIN lost_items ON
  matched_items.lost_id = lost_items.id
  INNER JOIN found_items ON matched_items.found_id = found_items.id";
  //$result = $conn->query($query);
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

  if(isset($_POST['submitted'])){
    $matchId = (isset($_POST['matchedId']) ? $_POST['matchedId'] : '');
    $isApprove = $_POST['submit'];
    switch($isApprove){
      case "Approve": $update = "UPDATE matched_items SET status = 1 WHERE id = " . $matchId;
                      break;
      case "Deny":    $update = "UPDATE matched_items SET status = 2 WHERE id = " . $matchId;
                      break;

    }
    $isUpdateSuccess = mysqli_query($conn, $update) or die(mysqli_error($conn));
  }

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
  echo '<div class="custom-div-main">';
  echo '<div class="tm-header">';
  echo '<div class="row tm-header-inner">';
  echo '<div class="col-md-6 col-12">';
  echo '<div class="tm-site-text-box">';
  echo '<img class="tm-site-logo" width="150" src = "img/logo.png"/>';
  echo '</div>';
  echo '</div>';
  echo '<nav class="col-md-6 col-12 tm-nav">';
  echo '<ul class="tm-nav-ul">';
  echo '</ul>';
  echo '</nav>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '';
  echo '<main>';
  echo '<header class="row tm-welcome-section">';
  echo '<h2 class="col-12 text-center tm-section-title">Items Verification</h2>';
  echo '</header>';
  echo '';
  echo '<div class="custom-center" style="width: 100%">';
  echo '<table class = "custom-table" border="1" width=1000 height=80>';
  echo '<tr>';
  echo '<th>ITEM LOST</th>';
  echo '<th>ITEM CLAIMED</th>';
  echo '<th>ACTION</th>';
  echo '</tr>';
  while($row = $result->fetch_assoc()){
      echo '<tr>';
      echo    '<td>'.$row['lost_name'].'</td>';
      echo    '<td>'.$row['found_name'].'</td>';
      echo    '<td>
                  <form action="bothitem.php" method="POST" style="float: left;margin-left: 5px;margin-right:5px;">
                    <input type=\'hidden\' name="lostId" value="'.$row['lost_id'].'"/>
                    <input type=\'hidden\' name="foundId" value="'.$row['found_id'].'"/>
                    <input type="Submit" class="custom-button" name="submit" value="Info"/>
                  </form>
                  <form action="admin.php" method="POST">
                    <input type=\'hidden\' name="matchedId" value="'.$row['matched_id'].'"/>
                    <input type="Submit" class="custom-button" name="submit" value="Approve"/>
                    <input type="Submit" class="custom-button" name="submit" value="Deny"/>
                    <input type=\'hidden\' name=\'submitted\' value=\'true\'/>
                  </form>
                </td>';
      echo '</tr>';
  }

  echo '</table>';
  echo '</div>';
  echo '';
  echo '</main>';
  echo '';
  echo '<footer class="tm-footer text-center">';
  echo '</footer>';
  echo '</div>';
  echo '<script src="js/jquery.min.js"></script>';
  echo '<script src="js/parallax.min.js"></script>';
  echo '</body>';
  echo '</html>';
  echo '';
?>
