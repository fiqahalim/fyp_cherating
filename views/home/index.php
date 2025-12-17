<?php
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
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
            <img class="d-block w-100" src="<?= $base_url ?>/assets/images/AboutUsPage/Picture3.png" alt="First slide">
         </div>
         <div class="carousel-item">
            <img class="d-block w-100" src="<?= $base_url ?>/assets/images/SingleRoom/Picture2.png" alt="Second slide">
         </div>
         <div class="carousel-item">
            <img class="d-block w-100" src="<?= $base_url ?>/assets/images/FamilySuite/Picture4.png" alt="Third slide">
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

      <!-- Booking Section (overlay on desktop, below on mobile) -->
      <div class="booking_ocline">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-md-6 col-lg-5">
                  <div class="book_room">
                     <h1>Book a Room Online</h1>
                     <form class="book_now" method="POST" action="<?= APP_URL ?>/rooms">
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

                           <div class="col-md-12 text-center">
                              <button type="submit" class="book_btn">Book Now</button>
                           </div>
                        </div>
                     </form>
                  </div>
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
               <p>Located just a short walk from the golden sands of <b>Cherating Beach</b> and close to local attractions like the <b>Cherating Turtle Sanctuary</b> and <b>Limbong Art</b>. Cherating Guest House is perfectly positioned for exploring the area. Whether you’re here to relax by the sea, enjoy nearby activities, or simply unwind in a peaceful setting, our guesthouse is the ideal base for your stay.</p>
               <a class="read_more" href="<?= $base_url ?>/about"> Read More</a>
            </div>
         </div>
         <div class="col-md-7">
            <div class="about_img">
               <figure><img src="<?= $base_url ?>/assets/images/cherating-beach-bar.jpg" alt="#"/></figure>
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
               <p>Perfect for families, couples, or solo travelers, every room is designed to make you feel right at home</p>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/SingleRoom/Picture2.png" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Single Room</h3>
                  <p>Cozy room for one person with all essential facilities.</p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/StandardRoom/Picture2.png" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Standard Room</h3>
                  <p>Comfortable room with queen bed, ideal for solo travelers or couples.</p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div id="serv_hover"  class="room">
               <div class="room_img">
                  <figure><img src="<?= $base_url ?>/assets/images/FamilySuite/Picture4.png" alt="#"/></figure>
               </div>
               <div class="bed_room">
                  <h3>Family Room</h3>
                  <p>A spacious room with king-size bed, modern amenities, and sea view.</p>
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
               <figure><img src="<?= $base_url ?>/assets/images/SingleRoom/Picture1.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/DeluxeRoom/Picture1.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/FamilySuite/Picture1.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/StandardRoom/Picture1.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/StandardRoom/Picture2.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/FamilySuite/Picture2.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/DeluxeRoom/Picture2.png" alt="#"/></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="<?= $base_url ?>/assets/images/SingleRoom/Picture2.png" alt="#"/></figure>
            </div>
         </div>
      </div>
   </div>
</div>

<!--  contact -->
<?php
include_once __DIR__ . '/contact.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Calendar Booking -->
<script>
document.addEventListener("DOMContentLoaded", function () {
   const arrivalInput = document.getElementById("arrival_date");
   const departureInput = document.getElementById("departure_date");
    
   // Get today's date in YYYY-MM-DD format
   const today = new Date().toISOString().split('T')[0];
    
   // Set minimum date for arrival to today
   arrivalInput.setAttribute('min', today);

   // Update departure min date based on arrival selection
   arrivalInput.addEventListener('change', function() {
      if (this.value) {
         // Departure must be at least 1 day after arrival
         let arrivalDate = new Date(this.value);
         arrivalDate.setDate(arrivalDate.getDate() + 1);
         departureInput.setAttribute('min', arrivalDate.toISOString().split('T')[0]);
      }
   });

   <?php 
        // We use get() here. Ensure your Flash class doesn't clear 
        // until this specific line is called.
        $msg = Flash::get('error'); 
    ?>
    
    const errorMessage = "<?= $msg ? addslashes($msg) : '' ?>";

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Booking Unavailable',
            text: errorMessage,
            confirmButtonColor: '#d33',
            background: '#fff'
        });
    }

   // Calendar Icon trigger logic
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