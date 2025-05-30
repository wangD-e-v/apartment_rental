<?php include "header.php"; ?>

<!-- profile page section starts  -->

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" autocomplete="off"> 
      <h3>Profile Page</h3>
      <?php 
      $id = $_SESSION['user_id'];
      $result = mysqli_query($con,"select * from user_reg where user_id='$id'");
      while($row = mysqli_fetch_assoc($result)) { ?>
      <input type="text" name="profile_name" required maxlength="50" placeholder="Your Name" class="box" value="<?php echo $row['username']; ?>">
      <input type="email" name="profile_email" required maxlength="50" placeholder="Your Email" class="box" value="<?php echo $row['email']; ?>">
      <input type="number" name="profile_number" required maxlength="10" placeholder="Your Number" max="9999999999" min="0" class="box" value="<?php echo $row['number']; ?>">
      <?php } ?>
      <input type="password" name="profile_password" required maxlength="20" placeholder="New Password" class="box">
      <input type="submit" value="Update Profile" name="update_profile" class="btn">
   </form>
</section>

<!-- profile page section ends -->
 
<?php

if(isset($_POST['update_profile'])){
      $username = $_POST['profile_name'];
      $password = $_POST['profile_password'];
      $email = $_POST['profile_email'];
      $number = $_POST['profile_number'];
        
      $id = $_SESSION['user_id'];
      $update = mysqli_query($con,"update user_reg set username='$username', password='$password',email='$email',number='$number' where user_id='$id'");
    }
?>

<?php include "footer.php"; ?>