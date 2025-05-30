<?php
   include 'db/db.php';
   if(session_status() == PHP_SESSION_NONE) {
      session_start();
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>STAYSPOT - Find Perfect Home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

   <link rel="stylesheet" href="css/style.css">

</head>
<body>


<header class="header">

   <nav class="navbar nav-1">
      <section class="flex">
         <a href="home.php" class="logo"><i class="fas fa-house"></i>STAYSPOT</a>

         <?php 
          
         if(isset($_SESSION['user_name'])){
         ?>
         <ul>
            <li><a href="contact.php">Contact<i class="fas fa-paper-plane"></i></a></li>
         </ul>
         <?php 
         } 
         else{
         ?>
         <ul>
            <li><a href="login.php">Contact<i class="fas fa-paper-plane"></i></a></li>
         </ul>
         <?php 
         } 
         ?>
      </section>
   </nav>

   <nav class="navbar nav-2">
      <section class="flex">
         <div id="menu-btn" class="fas fa-bars"></div>

         <div class="menu">
            <ul>
               <li><a href="home.php">Home</a></li>
               <li><a href="listings.php">Properties<i class="fas fa-angle-down"></i></a>
                  <ul>
                     <li><a href="house.php">House</a></li>
                     <li><a href="apartment.php">Apartmnets</a></li>
                  </ul>
               </li>
               <li><a href="about.php">About</a></li>
               <li><a href="home.php#sevices">Service</a>
               </li>
            </ul>
         </div>

         <ul>
            

            <?php 
            if(isset($_SESSION['user_name'])){
            ?>
            <li><a href="saved.php">saved <i class="far fa-heart"></i></a></li>
            <li><a href="#">account <i class="fas fa-angle-down"></i></a>
               <ul>
                  <li><a href="profile.php">profile</a></li>
                  <li><a href="bookings.php">bookings</a></li>
                  <li><a href="logout.php">logout</a></li>
               </ul>
            </li>
            <?php 
            } 
            else{
            ?>
            <li><a href="login.php">saved <i class="far fa-heart"></i></a></li>
            <li><a href="#">account <i class="fas fa-angle-down"></i></a>
               <ul>
                  <li><a href="login.php">login</a></li>
                  <li><a href="register.php">register</a></li>
               </ul>
            </li>
            <?php 
            } 
            ?>

         </ul>
      </section>
   </nav>

</header>

