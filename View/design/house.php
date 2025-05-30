<?php include "header.php"; ?>

<!-- listings section starts  -->

<section class="listings">

   <h1 class="heading">House Listings</h1>

   <div class="box-container">

      <?php 
      $result = mysqli_query($con,"SELECT * FROM property WHERE type='house'");
      while($row = mysqli_fetch_assoc($result)) { ?>
      <div class="box">
         <div class="thumb">
            <p class="total-images"><i class="far fa-image"></i><span>4</span></p>
            <p class="type"><span><?php echo $row['type']; ?></span><?php if($row['status'] == 'rented') { ?>
                        <span style="background-color: #303030;"><?php echo $row['status']; ?></span>
               <?php } else { ?>
                  <span><?php echo $row['status']; ?></span>
               <?php } ?></p>
            <img src="../admin/uploads/<?php echo $row['img1']; ?>" >
         </div>
         <h3 class="name"><?php echo $row['name']; ?></h3>
         <p class="location"><i class="fas fa-map-marker-alt"></i><span><?php echo $row['address']; ?></span></p>
         <div class="flex">
            <p><i class="fas fa-bed"></i><span><?php echo $row['bedroom']; ?></span></p>
            <p><i class="fas fa-bath"></i><span><?php echo $row['bathroom']; ?></span></p>
            <p><i class="fas fa-maximize"></i><span><?php echo $row['area']; ?> sqft</span></p>
         </div>
         <?php if($row['status'] == 'rented') { ?>
                        <a href="view_property.php?id=<?php echo $row['property_id']; ?>" class="btn" style="background-color: #303030;">Not Available</a>
                    <?php } else { ?>
                        <a href="view_property.php?id=<?php echo $row['property_id']; ?>" class="btn">View Property</a>
                    <?php } ?>
      </div>
      <?php } ?>

   </div>

</section>

<!-- listings section ends -->

<?php include "footer.php"; ?>
