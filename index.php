<?php
session_start();
if(empty($_SESSION['username'])) {
    header("location: login.php");
    exit();
}
echo "Welcome, " . $_SESSION['fullname'] . "!";
?>