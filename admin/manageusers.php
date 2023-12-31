<?php
require_once '../rbac.php';
$rbac = new RBAC();

error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['token'])) {
    header("Location: ../login.php");
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
    echo '<script>window.location.href = "../login.php";</script>';

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

// Fetch and display users from the database
$query = "SELECT * FROM users WHERE roleId=1";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));

$permissions = $rbac->getPermissions($_SESSION["role"]);

// Check if the user is logged in and has the admin role
if ($rbac->getRoleNameFromId($_SESSION['role']) == 'user') {
    echo '<script>
        alert("Invalid access!");
        window.location.href="../logout.php";
        </script>';

    exit();
} else {
    if ($rbac->hasPermission("view_users", $permissions)) {
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '';
        echo '<head>';
        echo '<meta charset="UTF-8" />';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
        echo '<meta http-equiv="X-UA-Compatible" content="ie=edge" />';
        echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />';
        echo '<link href="../css/all.min.css" rel="stylesheet" />';
        echo '<link href="../css/templatemo-style.css" rel="stylesheet" />';
        echo '<link href="../css/custom.css" media="all" rel="stylesheet" />';
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

        //header + navigation bar
        echo '<div class="container">';
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
        echo '<li class="tm-nav-li"><a href="dashboard.php" class="custom-link">Verify Item</a></li>';
        echo '<li class="tm-nav-li"><a href="lostitems.php" class="custom-link">Lost Items</a></li>';
        echo '<li class="tm-nav-li"><a href="founditems.php" class="custom-link">Found Items</a></li>';
        echo '<li class="tm-nav-li"><a href="manageusers.php" class="custom-link active">Manage Users</a></li>';
        if ($rbac->getRoleNameFromId($_SESSION['role']) == "superadmin") {
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

        // Handle user activation/deactivation using prepared statements
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['token']) {
                if (isset($_POST['activate']) || isset($_POST['deactivate'])) {
                    $userID = $_POST['userID'];
                    $action = isset($_POST['activate']) ? 'Activate' : 'Deactivate';

                    // Check if user has permission to manage users    
                    if ($rbac->hasPermission("manage_users", $permissions)) {
                        $updateStatusQuery = "UPDATE users SET status = ? WHERE ID = ?";
                        $stmt = mysqli_prepare($conn, $updateStatusQuery);
                        mysqli_stmt_bind_param($stmt, "ii", $status, $userID);

                        $status = ($action == 'Activate') ? 1 : 0;
                        mysqli_stmt_execute($stmt);

                        mysqli_stmt_close($stmt);

                        $_SESSION['success_message'] = 'Action completed successfully!';
                        header("Location: manageusers.php?status=success"); // Redirect to the same page to avoid resubmission
                        exit();
                    } else {
                        // Handle invalid user or display an error message
                        echo "Invalid user or unauthorized action.";
                    }
                }
            } else {
                // prompt user to login again
                echo '<script>alert("Invalid session token. Please log in and try again.");</script>';

                // redirect to login page
                echo '<script>window.location.href = "../login.php";</script>';
            }
        }


        echo '<main>';
        echo '<header class="row tm-welcome-section">';
        echo '<h2 class="col-12 text-center tm-section-title">Manage Users</h2>';
        echo '</header>';
        echo '';
        echo '<div class="custom-center" style="width: 100%">';

        // Show success message if set in the URL parameters
        if (isset($_GET['status']) && $_GET['status'] === 'success' && isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
            // Clear the success message after displaying it
            unset($_SESSION['success_message']);
        }

        echo '<table class="custom-table" border="1" style="display:center;" width=1000 height=100>';
        echo '<tr style="height: 70px;">';
        echo '<th>ID</th>';
        echo '<th>USERNAME</th>';
        echo '<th>EMAIL ADDRESS</th>';
        echo '<th>STATUS</th>';
        echo '<th>ACTION</th>';
        echo '</tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr style="height: 50px;">';
            echo '<td style="text-align: center;">' . $row['ID'] . '</td>';
            echo '<td style="text-align: center;">' . $row['username'] . '</td>';
            echo '<td style="text-align: center;">' . $row['email'] . '</td>';
            echo '<td style="text-align: center;">' . ($row['status'] == 1 ? 'Active' : 'Inactive') . '</td>';
            echo '<td style="text-align: center;">';
            // Allow user management actions only for non-admin and non-superadmin roles
            if ($rbac->getRoleNameFromId($row['roleId']) !== 'admin' && $rbac->getRoleNameFromId($row['roleId']) !== 'superadmin') {
                echo '<form action="manageusers.php" method="POST">';
                echo '<input type="hidden" name="userID" value="' . $row['ID'] . '"/>';

                // add csrf token in the form
                echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '">';

                // Check if the user is active, if so, hide the activate button
                if ($row['status'] == 1) {
                    echo '<button type="submit" class="custom-button" name="deactivate">Deactivate</button>';
                } else {
                    // Check if the user is inactive, if so, hide the deactivate button
                    echo '<button type="submit" class="custom-button" name="activate">Activate</button>';
                }

                echo '</form>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '';
        echo '</main>';

        // Handle user activation/deactivation using prepared statements
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['token']) {
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
            } else {
                // prompt user to login again
                echo '<script>alert("Invalid session token. Please log in and try again.");</script>';

                // redirect to login page
                echo '<script>window.location.href = "../login.php";</script>';
            }
        }

        //footer
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
        echo '';

        mysqli_close($conn);
    } else {
        echo '<script>
            alert("No permission to view this page!");
            window.location.href="../logout.php";
            </script>';

        exit();
    }
}
