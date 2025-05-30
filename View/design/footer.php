<!-- footer section starts  -->

<footer class="footer">

   <section class="flex">

      <div class="box">
         <a href="tel:9313031381"><i class="fas fa-phone"></i><span>9313031381</span></a>
         <a href="tel:7026270278"><i class="fas fa-phone"></i><span>7026270278</span></a>
         <a href="mailto:stayspot@gmail.com"><i class="fas fa-envelope"></i><span>stayspot@gmail.com</span></a>
         <a href="#"><i class="fas fa-map-marker-alt"></i><span>Amreli, Gujrat, India - 365601</span></a>
      </div>

      <div class="box">
         <a href="home.php"><span>Home</span></a>
         <a href="about.php"><span>About</span></a>
         <a href="contact.php"><span>Contact</span></a>
         <a href="saved.php"><span>Saved Properties</span></a>
      </div>

      <div class="box">
         <a href="#"><span>Facebook</span><i class="fab fa-facebook-f"></i></a>
         <a href="#"><span>Twitter</span><i class="fab fa-twitter"></i></a>
         <a href="#"><span>Linkedin</span><i class="fab fa-linkedin"></i></a>
         <a href="#"><span>Instagram</span><i class="fab fa-instagram"></i></a>
      </div>

   </section>

   <div class="credit">&copy; copyright @ 2024 by <span>stayspot.com</span> | all rights reserved!  [DARSHAN]</div>

</footer>

<!-- footer section ends -->


<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

document.querySelector('#filter-btn').onclick = () =>{
   document.querySelector('.filters').classList.add('active');
}

document.querySelector('#close-filter').onclick = () =>{
   document.querySelector('.filters').classList.remove('active');
}

<?php

if(isset($success_msg)){
   foreach($success_msg as $success_msg){
      echo '<script>swal("'.$success_msg.'", "" ,"success");</script>';
   }
}

if(isset($warning_msg)){
   foreach($warning_msg as $warning_msg){
      echo '<script>swal("'.$warning_msg.'", "" ,"warning");</script>';
   }
}

if(isset($info_msg)){
   foreach($info_msg as $success_msg){
      echo '<script>swal("'.$info_msg.'", "" ,"info");</script>';
   }
}

if(isset($error_msg)){
   foreach($error_msg as $error_msg){
      echo '<script>swal("'.$error_msg.'", "" ,"error");</script>';
   }
}

?>

</script>

</body>
</html>