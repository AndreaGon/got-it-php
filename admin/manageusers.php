<?php
    error_reporting(E_ALL);
    session_start();

    // Check if the user is logged in and has the admin role
    if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit();
    }

    $dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "gotit_db";
    $dbPort = 3306;

    $conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '';
    echo '<head>';
    echo '<meta charset="UTF-8" />';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
    echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
    echo '<title>Got It - Manage Users - Admin</title>';
    echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />';
    echo '<link href="../css/templatemo-style.css" media="all" rel="stylesheet" />';
    echo '<link href="../css/custom.css?v=' . time() . '" media="all" rel="stylesheet" />';
    echo '</head>';

    echo '<body>';
    echo '<div class="container custom-div-height">';
    echo '<div class="custom-placeholder">';
    echo '<div class="parallax-window">';
    echo '<div class="tm-header">';
    echo '<div class="row tm-header-inner">';
    echo '<div class="col-md-6 col-12">';

    echo '<div class="tm-site-text-box">';
    echo '<img class="tm-site-logo" width="150" src="../img/logo.png"/>';
    echo '</div>';
    echo '</div>';

    echo '<nav class="col-md-6 col-12 tm-nav">';
    echo '<ul class="tm-nav-ul">';
    echo '<li class="tm-nav-li"><a href="admin.php" class="custom-link">Verify Item</a></li>';
    echo '<li class="tm-nav-li"><a href="lostitems.php" class="custom-link">Lost Items</a></li>';
    echo '<li class="tm-nav-li"><a href="founditems.php" class="custom-link">Found Items</a></li>';
    echo '<li class="tm-nav-li"><a href="manageusers.php" class="custom-link active">Manage Users</a></li>';
    if($_SESSION['role'] == "superadmin"){
        echo '<li class="tm-nav-li"><a href="manage-admin.php" class="custom-link active">Manage Admins</a></li>';
    }
    echo '<li class="tm-nav-li"><a href="../logout.php" class="custom-link">Logout</a></li>';

    echo '</ul>';
    echo '</nav>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<main>';

    echo '<h1 align="left" style="margin-bottom:10px"><b>Manage Users</b></h1>';

    // Fetch and display users from the database
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

    echo '<table class="custom-table" border="1" width="1000" height="80">';
    echo '<tr>';
    echo '<th>User ID</th>';
    echo '<th>Email</th>';
    echo '<th>Status</th>';
    echo '<th>Action</th>';
    echo '</tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['ID'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . ($row['status'] == 1 ? 'Active' : 'Inactive') . '</td>';
        echo '<td>';
        // Allow user management actions only for non-admin and non-superadmin roles
        if ($row['role'] !== 'admin' && $row['role'] !== 'superadmin') {
            echo '<form action="manageusers.php" method="POST">';
            echo '<input type="hidden" name="userID" value="' . $row['ID'] . '"/>';
            echo '<button type="submit" class="custom-button" name="activate">Activate</button>';
            echo '<button type="submit" class="custom-button" name="deactivate">Deactivate</button>';
            echo '</form>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';

    // Handle user activation/deactivation using prepared statements
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['activate']) || isset($_POST['deactivate'])) {
            // Additional checks can be added here if needed
            $userID = $_POST['userID'];
            $action = isset($_POST['activate']) ? 'Activate' : 'Deactivate';

            // Allow user activation/deactivation only for non-admin and non-superadmin roles
            $userQuery = "SELECT * FROM users WHERE ID = $userID AND role NOT IN ('admin', 'superadmin')";
            $userResult = mysqli_query($conn, $userQuery);

            if ($userResult && mysqli_num_rows($userResult) > 0) {
                $updateStatusQuery = "UPDATE users SET status = ? WHERE ID = ?";
                $stmt = mysqli_prepare($conn, $updateStatusQuery);
                mysqli_stmt_bind_param($stmt, "ii", $status, $userID);

                $status = ($action == 'Activate') ? 1 : 0;
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                header("Refresh:0");
            } else {
                // Handle invalid user or display an error message
                echo "Invalid user or unauthorized action.";
            }
        }
    }

    echo '</div>';
    echo '</main>';
    echo '<footer class="tm-footer text-center"></footer>';
    echo '</div>';
    echo '<script src="../js/jquery.min.js"></script>';
    echo '<script src="../js/parallax.min.js"></script>';
    echo '</body>';
    echo '</html>';

    mysqli_close($conn);
?>