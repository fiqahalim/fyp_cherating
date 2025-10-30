<div class="contact">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>Contact Us</h2>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <form id="request" class="main_form" method="POST" action="<?= APP_URL ?>/contact-submit">
               <input type="hidden" name="contact_submit" value="1">
               <div class="row">
                  <div class="col-md-12 ">
                     <input class="contactus" placeholder="Name" type="text" name="name"> 
                  </div>
                  <div class="col-md-12">
                     <input class="contactus" placeholder="Email" type="email" name="email"> 
                  </div>
                  <div class="col-md-12">
                     <input class="contactus" placeholder="Phone Number" type="text" name="phone">                          
                  </div>
                  <div class="col-md-12">
                     <textarea class="textarea" placeholder="Message" name="message"></textarea>
                  </div>
                  <div class="col-md-12" target="_blank" rel="noopener noreferrer">
                     <button class="send_btn" type="submit" id="sendWhatsappBtn">
                        <i class="fab fa-whatsapp"></i> Send Us WhatsApp
                     </button>
                  </div>
               </div>
            </form>

            <!-- Feedback messages -->
            <?php if (!empty($_SESSION['success'])): ?>
               <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
               <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Optional: Show redirecting message -->
            <?php if (!empty($_SESSION['whatsapp_url'])): ?>
               <div class="alert alert-info">Redirecting you to WhatsApp...</div>
               <script>
                  setTimeout(function () {
                     window.location.href = "<?= $_SESSION['whatsapp_url'] ?>";
                  }, 2000); // 2 seconds delay
               </script>
            <?php endif; ?>

            <?php
            // Clear session messages after use
            unset($_SESSION['success'], $_SESSION['error'], $_SESSION['whatsapp_url']);
            ?>
         </div>
         <!-- Google Maps -->
         <div class="col-md-6">
            <div class="map_main">
               <div class="map-responsive">
                     <iframe
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA0s1a7phLN0iaD6-UE7m4qP-z21pH0eSc&q=4/1000+Kampung+Budaya,+Jalan+Kampung+Cherating+Lama,+26080+Kuantan,+Pahang,+Malaysia"
                        style="border:0; position: absolute; width: 100%;"
                        width="600" height="400" frameborder="0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                     </iframe>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>