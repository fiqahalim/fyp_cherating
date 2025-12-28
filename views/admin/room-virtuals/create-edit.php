<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?= $isEdit ? 'Edit' : 'Create' ?> Virtual Tour</h6>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Select Room</label>
                        <select name="room_id" class="form-control" required>
                            <option value="">-- Select a Room --</option>
                            <?php foreach($rooms as $room): ?>
                                <?php 
                                    // Check if this room is the one currently saved in the virtual tour
                                    $selected = ($roomVirtual && $roomVirtual['room_id'] == $room['id']) ? 'selected' : ''; 
                                ?>
                                <option value="<?= $room['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($room['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tour Title</label>
                        <input type="text" name="title" class="form-control" value="<?= $roomVirtual['title'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>360° Panorama Image (Equirectangular)</label>
                        <input type="file" name="panorama_image" class="form-control-file" <?= $isEdit ? '' : 'required' ?>>
                        <?php if($isEdit): ?>
                            <small class="text-muted">Current file: <?= $roomVirtual['image_path'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>
                <h5>Hotspots (Information Points)</h5>
                <div id="hotspot-container">
                    <?php if(empty($hotspots)): ?>
                        <div class="row hotspot-row mb-2">
                            <div class="col-md-3"><input type="number" step="any" name="hotspots[0][pitch]" class="form-control" placeholder="Pitch"></div>
                            <div class="col-md-3"><input type="number" step="any" name="hotspots[0][yaw]" class="form-control" placeholder="Yaw"></div>
                            <div class="col-md-5"><input type="text" name="hotspots[0][text]" class="form-control" placeholder="Info Text"></div>
                            <div class="col-md-1"></div>
                        </div>
                    <?php else: ?>
                        <?php foreach($hotspots as $index => $spot): ?>
                            <div class="row hotspot-row mb-2">
                                <div class="col-md-3"><input type="number" step="any" name="hotspots[<?= $index ?>][pitch]" class="form-control" value="<?= $spot['pitch'] ?>"></div>
                                <div class="col-md-3"><input type="number" step="any" name="hotspots[<?= $index ?>][yaw]" class="form-control" value="<?= $spot['yaw'] ?>"></div>
                                <div class="col-md-5"><input type="text" name="hotspots[<?= $index ?>][text]" class="form-control" value="<?= $spot['text'] ?>"></div>
                                <div class="col-md-1"><button type="button" class="btn btn-danger remove-spot">&times;</button></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-hotspot" class="btn btn-sm btn-info mt-2">+ Add Hotspot Field</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Virtual Tour</button>
                    <a href="<?= APP_URL ?>/admin/room-virtuals" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let spotIndex = <?= count($hotspots) > 0 ? count($hotspots) : 1 ?>;

document.getElementById('add-hotspot').addEventListener('click', function() {
    const container = document.getElementById('hotspot-container');
    const html = `
        <div class="row hotspot-row mb-2">
            <div class="col-md-3"><input type="number" step="any" name="hotspots[${spotIndex}][pitch]" class="form-control" placeholder="Pitch"></div>
            <div class="col-md-3"><input type="number" step="any" name="hotspots[${spotIndex}][yaw]" class="form-control" placeholder="Yaw"></div>
            <div class="col-md-5"><input type="text" name="hotspots[${spotIndex}][text]" class="form-control" placeholder="Info Text"></div>
            <div class="col-md-1"><button type="button" class="btn btn-danger remove-spot">&times;</button></div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    spotIndex++;
});

document.addEventListener('click', function(e) {
    if(e.target && e.target.classList.contains('remove-spot')) {
        e.target.closest('.hotspot-row').remove();
    }
});
</script>

<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>