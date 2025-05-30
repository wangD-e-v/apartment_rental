<?php include "header.php";
$uid = $_SESSION['user_id'];

if(isset($_POST['cancel'])){
   $tid = $_POST['tenant_id'];
   $pid = $_POST['property_id'];
   $sql = mysqli_query($con,"delete from payment where tenant_id =$tid");
   $sql = mysqli_query($con,"delete from tenant where tenant_id =$tid");
   $sql = mysqli_query($con,"update property set status='available' where property_id = $pid");
}
?>

<section class="bookings">
    <h1 class="heading">Your Bookings</h1>

    <div class="box-container">
    <?php
      $result = mysqli_query($con,"SELECT t.*,p.name,p.property_id FROM tenant t join property p on t.property_id = p.property_id WHERE t.user_id = $uid");
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { ?>
   <div class="box">
      <p>property name : <span><?= $row['name']; ?></span></p>
      <p>name : <span><?= $row['fullname']; ?></span></p>
      <p>email : <span><?= $row['email']; ?></span></p>
      <p>number : <span><?= $row['number']; ?></span></p>
      <p>check in : <span><?= $row['date_from']; ?></span></p>
      <p>check out : <span><?= $row['date_to']; ?></span></p>
      <p>address : <span><?= $row['address']; ?></span></p>
      <p>amount : <span>₹<?= $row['total_rent']; ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="property_id" value="<?= $row['property_id']; ?>">
         <input type="hidden" name="tenant_id" value="<?= $row['tenant_id']; ?>">
         <input type="submit" value="cancel booking" name="cancel" class="btn" onclick="return confirm('cancel this booking?');">
      </form>
   </div>
   <?php
    }
   }else{
   ?>   
   <div class="box" style="text-align: center;">
      <p style="padding-bottom: .5rem; text-transform:capitalize;">no bookings found!</p>
      <a href="listings.php" class="btn">book new</a>
   </div>
   <?php
   }
   ?>
    </div>
</section>

<?php include "footer.php"; ?>
