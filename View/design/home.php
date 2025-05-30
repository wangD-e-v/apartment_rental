<?php include "header.php"; ?>

<div class="home">
    <div class="content">  
        <h1>Find Your Dream Home</h1>
        <p>Explore our listings and discover the perfect place for you.</p>
        <a href="listings.php" class="btn">Explore Listings</a>
    </div>
</div>


<section class="services" id="sevices">

   <h1 class="heading">our services</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/step-1.png" alt="">
         <h3>search property</h3>
         <p>Use our advanced search filters to find the perfect home or rental investment.</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>rent property</h3>
         <p>Complete the booking process and make a secure payment for rent property.</p>
      </div>

      <div class="box">
         <img src="images/step-3.png" alt="">
         <h3>enjoy property</h3>
         <p> Move in and enjoy hassle-free living with our dedicated support team at your service.</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>flats and buildings</h3>
         <p>Use our advanced search filters to find the perfect home or rental investment.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>shops and malls</h3>
         <p>Use our advanced search filters to find the perfect home or rental investment.</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>24/7 service</h3>
         <p>Use our advanced search filters to find the perfect home or rental investment.</p>
      </div>

   </div>

</section>

<section class="listings">

   <h1 class="heading">latest listings</h1>

   <div class="box-container">

   <?php 
      $result = mysqli_query($con,"select * from property limit 3");
      while($row = mysqli_fetch_assoc($result)) { ?>
      <div class="box">
         <div class="thumb">
            <p class="total-images"><i class="far fa-image"></i><span>4</span></p>
            <p class="type">
               <span><?php echo $row['type']; ?></span>
               <?php if($row['status'] == 'rented') { ?>
                        <span style="background-color: #303030;"><?php echo $row['status']; ?></span>
               <?php } else { ?>
                  <span><?php echo $row['status']; ?></span>
               <?php } ?>
            </p>
            <img src=" ../admin/uploads/<?php echo $row['img1']; ?>" >
         </div>
         <h3 class="name"><?php echo $row['name']; ?></h3>
         <p class="location"><i class="fas fa-map-marker-alt"></i><span><?php echo $row['address']; ?></span></p>
         <div class="flex">
            <p><i class="fas fa-bed"></i><span><?php echo $row['bedroom']; ?></span></p>
            <p><i class="fas fa-bath"></i><span><?php echo $row['bathroom']; ?></span></p>
            <p><i class="fas fa-maximize"></i><span><?php echo $row['area']; ?>sqft</span></p>
         </div>
         <?php if($row['status'] == 'rented') { ?>
                        <a href="not_available.php?id=<?php echo $row['property_id']; ?>" class="btn" style="background-color: #303030;">Not Available</a>
                    <?php } else { ?>
                        <a href="view_property.php?id=<?php echo $row['property_id']; ?>" class="btn">View Property</a>
                    <?php } ?>
      </div>
      <?php } ?>

   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="listings.php" class="inline-btn">view all</a>
   </div>

</section>
<?php include "footer.php"; ?>