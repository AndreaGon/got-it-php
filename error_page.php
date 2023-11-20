<?php
error_reporting(E_ALL);  //give warning if session cannot start
session_start(); //start the session
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" />
    <link href="css/all.min.css" rel="stylesheet" />
    <link href="css/templatemo-style.css" rel="stylesheet" />
    <link href="css/custom.css" media="all" rel="stylesheet" />

    <title>Error</title>
    <style>
        .container-error {
            max-width: 600px;
            margin: 100px auto 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container-error h1{
            color: #d9534f;
            text-align: center;
            margin: 10px 0;
        }

        .container-error p{
            text-align: center;
        }

        .container-error img{
            display: block;
            margin: auto;
            height: 300px;
            width: 400px;
        }
    </style>
</head>

<body>
    <!-- Header + Navigation Bar -->
    <div class="container">
        <!-- Top box -->
        <!-- Logo & Site Name -->
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
                                <li class="tm-nav-li"><a href="lostitemform.php" class="custom-link">Lost Item Report</a></li>
                                <li class="tm-nav-li"><a href="founditemform.php" class="custom-link">Found Item Report</a></li>
                                <?php
                                if (!isset($_SESSION['userID'])) {
                                    echo '<li class="tm-nav-li"><a href="login.php" class="custom-link active">Login/Register</a></li>';
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

        <!-- Login Form -->
        <main>
            <div class="container-error">
                <img src ="img/notfound.jpg" alt="Page Not Found">
                <h1>Page Not Found</h1>
                <p>Oops! Something went wrong.</p>
                <p>Please try again later or contact support.</p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="tm-footer text-center">
            <p>Copyright &copy; 2020 Simple House | Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>
        </footer>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/parallax.min.js"></script>
</body>

</html>