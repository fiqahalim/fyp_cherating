<?php
$base_url = '/fyp_cherating';
?>

<!-- banner -->
<section class="banner_main">
   <div id="myCarousel" class="carousel slide banner" data-ride="carousel">
      <ol class="carousel-indicators">
         <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
         <li data-target="#myCarousel" data-slide-to="1"></li>
         <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
         <div class="carousel-item active">
            <img class="first-slide" src="<?= $base_url ?>/assets/images/banner1.jpg" alt="First slide">
            <div class="container">
            </div>
         </div>
         <div class="carousel-item">
            <img class="second-slide" src="<?= $base_url ?>/assets/images/banner2.jpg" alt="Second slide">
         </div>
         <div class="carousel-item">
            <img class="third-slide" src="<?= $base_url ?>/assets/images/banner3.jpg" alt="Third slide">
         </div>
      </div>
      <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
      </a>
   </div>
   
   <!-- Booking Section -->
   <div class="booking_ocline">
      <div class="container">
         <div class="row">
            <div class="col-md-5">
               <div class="book_room">
                  <h1>Book a Room Online</h1>
                  <form class="book_now"method="POST" action="<?= APP_URL ?>/rooms">
                        <div class="row">
                            <div class="col-md-12">
                              <span>Arrival</span>
                              <div class="date-wrapper">
                                 <input class="online_book" id="arrival_date" type="date" name="arrival_date" required>
                                 <img class="date_cua" src="<?= $base_url ?>/assets/images/date.png" data-target="arrival_date">
                              </div>
                           </div>

                           <div class="col-md-12">
                              <span>Departure</span>
                              <div class="date-wrapper">
                                 <input class="online_book" id="departure_date" type="date" name="departure_date" required>
                                 <img class="date_cua" src="<?= $base_url ?>/assets/images/date.png" data-target="departure_date">
                              </div>
                           </div>
                           <div class="col-md-12">
                              <button type="submit" class="book_btn">Book Now</button>
                           </div>
                        </div>
                    </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- about -->
<div class="about">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-5">
            <div class="titlepage">
               <h2>About Us</h2>
               <p>The passage experienced a surge in popularity during the 1960s when Letraset used it on their dry-transfer sheets, and again during the 90s as desktop publishers bundled the text with their software. Today it's seen all around the web; on templates, websites, and stock designs. Use our generator to get your own, or read on for the authoritative history of lorem ipsum. </p>
               <a class="read_more" href="<?= $base_url ?>/views/about.php"> Read More</a>
            </div>
         </div>
         <div class="col-md-7">
            <div class="about_img">
               <figure><img src="<?= $base_url ?>/assets/images/about.png" alt="#"/></figure>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- our_room -->
<div  class="our_room">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>Our Room</h2>
               <p>Lorem Ipsum available, but the majority have suffered </p>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room1.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room2.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room3.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room4.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room5.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/room6.jpg" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Bed Room</h3>
                  <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- gallery -->
<div  class="gallery">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>gallery</h2>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery1.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery2.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery3.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery4.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery5.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery6.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery7.jpg" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/gallery8.jpg" alt="#"/></figure>
            </div>
         </div>
      </div>
   </div>
</div>

<!--  contact -->
<?php
include_once __DIR__ . '/contact.php';
?>

<!-- Calendar Booking -->
<script>
document.addEventListener("DOMContentLoaded", function () {
   const calendarIcons = document.querySelectorAll(".date_cua");

   calendarIcons.forEach(function (icon) {
      icon.addEventListener("click", function () {
         const targetInputId = this.getAttribute("data-target");
         const targetInput = document.getElementById(targetInputId);
         if (targetInput) {
            targetInput.showPicker ? targetInput.showPicker() : targetInput.focus();
         }
      });
   });
});
</script>