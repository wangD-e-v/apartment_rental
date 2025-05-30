<?php
session_start();
if (isset($_SESSION['user_name'])) {
    unset($_SESSION['user_name']);
    unset($_SESSION['user_id']);
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    echo "No user session found.";
}
?>