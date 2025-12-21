<div class="contact">
   <div class="container">
    <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>360° Virtual Tour</h2>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage" style="padding-bottom: 20px;">
               <h3>Experience: <?= htmlspecialchars($tourData['name']) ?></h3>
               <p><?= htmlspecialchars($tourData['title']) ?></p>
            </div>
         </div>
      </div>
      
      <div class="row">
         <div class="col-md-12">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

            <div id="panorama-container" style="width: 100%; height: 600px; background: #000; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.3);"></div>
         </div>
      </div>

      <!-- <div class="row mt-5">
         <div class="col-md-12 text-center">
               <a href="<?= APP_URL ?>/rooms" class="btn btn-secondary" style="padding: 12px 35px; border-radius: 30px; margin-right: 10px;">Back to Rooms</a>
               <a href="<?= APP_URL ?>/booking?id=<?= $roomId ?>" class="btn btn-warning" style="padding: 12px 35px; border-radius: 30px; font-weight: bold;">Book Now</a>
         </div>
      </div> -->
   </div>
</div>

<script>
window.onload = function() {
    // 1. Initialize the viewer first
    var viewer = pannellum.viewer('panorama-container', {
        "type": "equirectangular",
        "panorama": "<?= !empty($tourData['image_path']) ? APP_URL . '/' . $tourData['image_path'] : 'https://pannellum.org/images/alma.jpg' ?>",
        "autoLoad": true,
        "title": "<?= htmlspecialchars($tourData['name']) ?>",
        "author": "Cherating Guest House",
        "autoRotate": -2,
        "hotSpots": [
            <?php foreach($hotspots as $spot): ?>
            {
                "pitch": <?= (float)$spot['pitch'] ?>,
                "yaw": <?= (float)$spot['yaw'] ?>,
                "type": "info",
                "text": "<?= htmlspecialchars($spot['text']) ?>"
            },
            <?php endforeach; ?>
            {
                "pitch": -10,
                "yaw": 20,
                "type": "info",
                "text": "Luxury King Sized Bed (Test)"
            },
            {
                "pitch": -15,
                "yaw": 10,
                "type": "info",
                "text": "Premium 1000 Thread Count Sheets"
            },
            {
                "pitch": 5,
                "yaw": 110,
                "type": "info",
                "text": "Beachfront View Balcony"
            },
            {
                "pitch": -20,
                "yaw": -30,
                "type": "info",
                "text": "Complimentary Mini Bar"
            }
        ]
    });

    // 2. Click listener to find coordinates for your database
    // viewer.on('mousedown', function(event) {
    //     var coords = viewer.mouseEventToCoords(event);
    //     console.log("Coordinate found! Pitch:", coords[0], "Yaw:", coords[1]);
    //     alert("Pitch: " + coords[0] + "\nYaw: " + coords[1] + "\nCheck console for easy copying!");
    // });
};
</script>