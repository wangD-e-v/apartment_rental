<?php
session_start();
if(isset($_SESSION['admin_name'])){
    unset($_SESSION['admin_name']);
    session_destroy();
    header("location: login.php");
    exit();
}else {
    echo "No admin session found.";
}

?>