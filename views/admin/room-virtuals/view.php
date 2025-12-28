<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container-fluid mt-4">
    <?php Flash::display(); ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-vr-cardboard"></i> 360° Tour: <?= htmlspecialchars($roomVirtual['name']) ?>
        </h1>
        <a href="<?= APP_URL . '/admin/room-virtuals' ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Virtual Experience</h6>
                </div>
                <div class="card-body p-0">
                    <div id="panorama-admin" style="width: 100%; height: 500px; background: #000;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tour Information</h6>
                </div>
                <div class="card-body">
                    <label class="text-xs font-weight-bold text-uppercase mb-1">Room Name</label>
                    <p class="h5 mb-3"><?= htmlspecialchars($roomVirtual['name']) ?></p>
                    
                    <label class="text-xs font-weight-bold text-uppercase mb-1">Tour Title</label>
                    <p class="mb-3"><?= htmlspecialchars($roomVirtual['title']) ?></p>

                    <label class="text-xs font-weight-bold text-uppercase mb-1">Image Path</label>
                    <code class="d-block mb-3 small"><?= htmlspecialchars($roomVirtual['image_path']) ?></code>

                    <hr>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> <strong>Admin Hint:</strong> Click and drag the image to explore. Hotspots are visible as clickable icons.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    pannellum.viewer('panorama-admin', {
        "type": "equirectangular",
        "panorama": "<?= APP_URL . '/' . $roomVirtual['image_path'] ?>",
        "autoLoad": true,
        "title": "<?= htmlspecialchars($roomVirtual['name']) ?>",
        "author": "Cherating Guest House Admin",
        "hotSpots": [
            <?php foreach($hotspots as $spot): ?>
            {
                "pitch": <?= (float)$spot['pitch'] ?>,
                "yaw": <?= (float)$spot['yaw'] ?>,
                "type": "info",
                "text": "<?= htmlspecialchars($spot['text']) ?>"
            },
            <?php endforeach; ?>
        ]
    });
});
</script>

<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>