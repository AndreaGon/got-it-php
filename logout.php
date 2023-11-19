<?php
session_start();

unset($_SESSION["userID"]);
unset($_SESSION["role"]);
unset($_SESSION["loggedin"]);

// destroy the session token
unset($_SESSION['token']);

session_destroy();

header("Location:index.php");
?>
