<?php
   include "db/db.php";

   if(isset($_POST['signup'])){
      $username = $_POST['username'];
      $password = $_POST['password'];
      $email = $_POST['email'];
      $number = $_POST['number'];

      $check_user = mysqli_query($con, "SELECT * FROM user_reg WHERE username = '$username'");
      
      if(mysqli_num_rows($check_user) > 0){
         // If username exists, show an error message
         echo "<script>alert('Username already exists, please choose another one');</script>";
      }
      else{
         $signup = mysqli_query($con,"insert into user_reg(username,password,email,number) values('$username','$password','$email','$number')");
         if($signup){
            header("location: login.php");
         }
      } 
    }
?>

<?php include "header.php"; ?>

<!-- register section starts  -->

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" autocomplete="off"> 
      <h3>Register Form!</h3>
      <input type="text" name="username" required maxlength="50" placeholder="enter your name" class="box">
      <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
      <input type="number" name="number" required maxlength="10" max="9999999999" min="0" placeholder="enter your number" class="box">
      <input type="password" name="password" required maxlength="8" placeholder="enter your password" class="box">
      <p>already have an account? <a href="login.php">login now</a></p>
      <input type="submit" value="register now" name="signup" class="btn">
   </form>

</section>

<!-- register section ends -->
<?php
if(isset($_POST['signup'])){
      $username = $_POST['username'];
      $password = $_POST['password'];
      $email = $_POST['email'];

      $check_user = mysqli_query($con, "SELECT * FROM user_reg WHERE username = '$username'");
      
      if(mysqli_num_rows($check_user) > 0){
         // If username exists, show an error message
         echo "<script>alert('Username already exists, please choose another one');</script>";
      }
      else{
         $signup = mysqli_query($con,"insert into user_reg(username,password,email) values('$username','$password','$email')");
         if($signup){
            header("location: login.php");
            exit();
         }
      } 
}
?>

<?php include "footer.php"; ?>