<?php
session_start();

unset($_SESSION["userID"]);
unset($_SESSION["role"]);
unset($_SESSION["loggedin"]);

session_destroy();

header("Location:index.php");
?>
