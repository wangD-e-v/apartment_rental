<?php include "header.php"; ?>

<!-- login section starts  -->

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
      <h3>Login Form!</h3>
      <input type="text" name="username" required maxlength="50" placeholder="enter your username" class="box">
      <input type="password" name="password" required maxlength="8" placeholder="enter your password" class="box">
      <p>don't have an account? <a href="register.php">register now</a></p>
      <input type="submit" value="login now" name="login" class="btn">
   </form>

</section>

<?php
   if(isset($_POST['login'])){
      $username = $_POST['username'];
      $password = $_POST['password'];
  
      $signin = mysqli_query($con,"select * from user_reg where username='$username' and password='$password'");
      if(mysqli_num_rows($signin) > 0){
         while($obj = mysqli_fetch_assoc($signin)){
           $user = $obj['username'];
           $pass = $obj['password'];
           $id = $obj['user_id'];
         }
         if($user==$username && $pass==$password){
            session_start();
            $_SESSION['user_name']=$user;
            $_SESSION['user_id']=$id;
            header("location: home.php");
            exit();
         }
         else{
           echo "<script type='text/javascript'> 
                   alert('Invalid Password!'); 
                 </script>";
         }
      }
      else{
        echo "<script type='text/javascript'> 
                alert('Invalid Username and Password!'); 
              </script>";
      }
    }
?>

<?php include "footer.php"; ?>












