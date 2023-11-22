<?php
require_once 'rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['token'])) {
    header("Location:login.php");
}

$sessionTimeout = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    session_unset();
    session_destroy();
    echo '<script>alert("Session expired. Please log in again.");</script>';
    echo '<script>window.location.href = "login.php";</script>';
    exit();
}

$_SESSION['last_activity'] = time();

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gotit_db";
$dbPort = 3306;

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

// Initialize error messages
$locationError = '';
$categoryError = '';
$descriptionError = '';

if (isset($_POST['submitted'])) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['token']) {
        $permissions = $rbac->getPermissions($_SESSION["role"]);
        $isSuccess = false;

        $itemName = mysqli_real_escape_string($conn, $_POST['item']);
        $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
        $color = mysqli_real_escape_string($conn, $_POST['color']);
        $brand = mysqli_real_escape_string($conn, $_POST['brand']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
        $image = isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']) ? addslashes(file_get_contents($_FILES['image']['tmp_name'])) : getDefaultImage();

        if (empty($color)) $color = NULL;
        if (empty($brand)) $brand = NULL;

        // Validate location and category
        $locationError = empty($location) ? '<span style="color: red;">Please select a location.</span>' : '';
        $categoryError = empty($category) ? '<span style="color: red;">Please select a category.</span>' : '';
        $descriptionError = empty($description) ? '<span style="color: red;">Please enter a description.</span>' : '';

        if (empty($locationError) && empty($categoryError)) {
            if ($rbac->hasPermission("add_lost_item", $permissions)) {
                $query = "INSERT INTO lost_items (userID, itemName, category, color, brand, description, lost_date, lost_time, location, image, status)
                          VALUES ('" . $_SESSION['userID'] . "','" . $itemName . "','" . $category . "','" . $color . "', '" . $brand . "', '" . $description . "', '" . $date . "', '" . $time . "', '" . $location . "','" . $image . "'," . '0' . ")";

                if ($conn->query($query) === TRUE) {
                    $isSuccess = true;
                }
            } else {
                echo '<span>NO PERMISSIONS</span>';
            }
        } else {
            // Display error message at the top of the form
            $error_message = '<h2 class="col-12 text-center tm-section-title">Submit a Lost Item</h2>';
            $error_message .= '<br>' . $locationError . '<br>' . $categoryError . '<br>' . $descriptionError;
        }

        $conn->close();
    } else {
        echo '<script>alert("Invalid session token. Please log in and try again.");</script>';
        echo '<script>window.location.href = "login.php";</script>';
    }
}

function getDefaultImage()
{
    $defaultImagePath = 'img/nip.jpg';

    if (file_exists($defaultImagePath)) {
        return addslashes(file_get_contents($defaultImagePath));
    } else {
        echo '<span style="color: red;">Error: Default image not found.</span>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Got It - Lost Item Form</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />
    <link href="css/templatemo-style.css" media="all" rel="stylesheet" />
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
                                <img class="tm-site-logo" width="150" src="img/logo.png" />
                            </div>
                        </div>
                        <nav class="col-md-6 col-12 tm-nav">
                            <ul class="tm-nav-ul">
                                <li class="tm-nav-li"><a href="index.php" class="custom-link">Home</a></li>
                                <li class="tm-nav-li"><a href="lostitemform.php" class="custom-link active">Lost Item Report</a></li>
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
            <h2 class="col-12 text-center tm-section-title">Submit a Lost Item</h2>
                <?php
                if (isset($_POST['submitted']) && $isSuccess) {
                  echo '<span style="display: block; background-color: #9ffa91; padding: 20px;">Item reported successfully</span>';
              } elseif (isset($_POST['submitted']) && !$isSuccess) {
                  echo '<div>';
                  echo '<p style="color: red; ">Item report failed.</p>';
                  if (isset($locationError)) {
                      echo '<p style="color: red;">Please select a location.</p>';
                  }
                  if (isset($categoryError)) {
                      echo '<p style="color: red;">Please select a category.</p>';
                  }
                  echo '</div>';
              }
                ?>
            </header>

            <div id="tm-gallery-page-pizza" class="tm-gallery-page">
                <form action="lostitemform.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['token']; ?>">
                    <div style="float:left;" class="custom-div-section">
                        <div class="custom-section">
                            <h4>Name of Item Lost</h4>
                            <input class="custom-input" type="text" name="item" placeholder="Name of Item Lost (Wallet, Pen, Notebook, etc.)" />
                        </div>
                        <div class="custom-section">
                            <h4>Category</h4>
                            <select name="category" class="custom-select">
                                <option disabled selected>Select...</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Electronic Device">Electronic Device</option>
                                <option value="Identification Item">Identification Item</option>
                                <option value="Other Items">Other Items</option>
                                <option value="School Stationary">School Stationary</option>
                                <option value="Textbook">Textbook</option>
                                <option value="Wallets">Wallets</option>
                            </select>
                        </div>
                        <div class="custom-section">
                            <h4>Color (optional)</h4>
                            <input class="custom-input" name="color" type="text" placeholder="Color (optional)" />
                        </div>
                        <div class="custom-section">
                            <h4>Brand (optional)</h4>
                            <input class="custom-input" name="brand" type="text" placeholder="Brand (optional)" />
                        </div>
                        <div class="custom-section">
                            <h4>Description</h4>
                            <textarea rows="10" cols="55" name="description" class="custom-textarea"></textarea>
                        </div>
                        <div class="custom-section">
                            <input type="Submit" class="custom-button submit" value="Submit" />
                            <input type='hidden' name='submitted' value='true' />
                        </div>
                    </div>
                    <div style="float:right;" class="custom-div-section">
                        <div class="custom-section">
                            <h4>Date Lost</h4>
                            <input class="custom-input" type="date" name="date" />
                        </div>
                        <div class="custom-section">
                            <h4>Time Lost</h4>
                            <input class="custom-input" type="time" name="time" />
                        </div>
                        <div class="custom-section">
                            <h4>Location</h4>
                            <select class="custom-select" name="location">
                                <option disabled selected>Select...</option>
                                <option value="Billiard">Billiard</option>
                                <option value="Cafeteria">Cafeteria</option>
                                <option value="Car Park">Car Park</option>
                                <option value="Convenience Store">Convenience Store</option>
                                <option value="Lecture Hall">Lecture Hall</option>
                                <option value="Level 2 Lecture Rooms">Level 2 Lecture Rooms</option>
                                <option value="Level 3 Lecture Rooms">Level 3 Lecture Rooms</option>
                                <option value="Level 4 Lecture Rooms">Level 4 Lecture Rooms</option>
                                <option value="Level 5 Lecture Rooms">Level 5 Lecture Rooms</option>
                                <option value="Level 6 Lecture Rooms">Level 6 Lecture Rooms</option>
                                <option value="Library">Library</option>
                                <option value="Music Room">Music Room</option>
                                <option value="Reception">Reception</option>
                            </select>
                        </div>
                        <div class="custom-section">
                            <h4>Item image</h4>
                            <input class="custom-input" name="image" type="file" accept="image/*" />
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <footer class="tm-footer text-center">
            <p>Copyright &copy; 2020 Simple House | Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>
        </footer>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/parallax.min.js"></script>
</body>

</html>
