<?php
  $dbServername = "localhost";
  $dbUsername = "root";
  $dbPassword = "";
  $dbName = "gotit_db";


  $conn = mysqli_connect($dbServername ,$dbUsername,$dbPassword,$dbName);
  if(isset($_POST['submitted'])){
    $itemName = $_POST['item'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $image = $_FILES['image']['name'];

    if(empty($color)) $color = NULL;
    if(empty($brand)) $brand = NULL;

    $query = "INSERT INTO found_items (userID, itemName, category, color, brand, description, found_date, found_time, location, image, status)
    VALUES " . "('" . "1" . "','" . $itemName . "','" . $category . "','" . $color . "', '" . $brand . "', '" . $description . "', '" . $date . "', '" . $time . "', '" . $location . "','" . $image . "',". '0' . ")";

    if ($conn->query($query) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $query . "<br>" . $conn->error;
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
    echo '<title>Simple House Template</title>';
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
    echo '<li class="tm-nav-li"><a href="index.php" class="custom-link active">Home</a></li>';
    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link">Login/Register</a></li>';
    echo '<li class="tm-nav-li"><a href="about.html" class="custom-link">About</a></li>';
    echo '<li class="tm-nav-li"><a href="contact.html" class="custom-link">Contact</a></li>';
    echo '</ul>';
    echo '</nav>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '';
    echo '<main>';
    echo '<header class="row tm-welcome-section">';
    echo '<h2 class="col-12 text-center tm-section-title">Submit a Found Item</h2>';
    echo '<p class="col-12 text-center">Total 3 HTML pages are included in this template. Header image has a parallax effect. You can feel free to download, edit and use this TemplateMo layout for your commercial or non-commercial websites.</p>';
    echo '</header>';
    echo '';
    echo '';
    echo '<!-- Gallery -->';
    echo '<div id="tm-gallery-page-pizza" class="tm-gallery-page">';
    echo '<form action="founditemform.php" method="POST" enctype="multipart/form-data">';
    echo '<div style="float:left;" class="custom-div-section">';
    echo '<div class="custom-section">';
    echo '<h4>Name of Item Found</h4>';
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
    echo '<h4>Date Found</h4>';
    echo '<input class = "custom-input" type="date" name="date"/>';
    echo '</div>';
    echo '<div class="custom-section">';
    echo '<h4>Time Found</h4>';
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
  }

?>
