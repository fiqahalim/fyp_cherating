<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($room) ? 'Edit Room' : 'Add New Room' ?></h1>
        <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Rooms
        </a>
    </div>

    <?php Flash::display(); ?>

    <form action="<?= isset($room) ? APP_URL . '/admin/rooms/edit/' . $room['id'] : APP_URL . '/admin/rooms/create' ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="name" class="form-label font-weight-bold">Room Name</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                   placeholder="e.g. Deluxe Sea View Suite"
                                   value="<?= isset($room) ? htmlspecialchars($room['name']) : '' ?>" required>
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label font-weight-bold">Detailed Description</label>
                            <textarea class="form-control" id="description" name="description" rows="8" 
                                      placeholder="Describe the room amenities, view, and bed configuration..."
                                      required><?= isset($room) ? htmlspecialchars($room['description']) : '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Specifications</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_rooms" class="form-label font-weight-bold">Total Inventory</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="total_rooms" name="total_rooms" min="1"
                                        value="<?= isset($room) ? htmlspecialchars($room['total_rooms']) : '' ?>" required>
                                </div>
                                <small class="text-muted">How many rooms of this type exist?</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label font-weight-bold">Max Capacity</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="capacity" name="capacity" min="1"
                                        value="<?= isset($room) ? htmlspecialchars($room['capacity']) : '' ?>" required>
                                </div>
                                <small class="text-muted">Maximum persons allowed.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pricing & Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="price" class="form-label font-weight-bold">Price per Night</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">RM</span>
                                </div>
                                <input type="number" class="form-control form-control-lg font-weight-bold text-primary" 
                                       id="price" name="price" min="0" step="0.01"
                                       value="<?= isset($room) ? htmlspecialchars($room['price']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label font-weight-bold">Visibility Status</label>
                            <select class="custom-select" id="status" name="status" required>
                                <option value="active" <?= (isset($room) && $room['status'] === 'active') ? 'selected' : '' ?>>🟢 Active / Public</option>
                                <option value="inactive" <?= (isset($room) && $room['status'] === 'inactive') ? 'selected' : '' ?>>🔴 Inactive / Hidden</option>
                            </select>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save mr-2"></i><?= isset($room) ? 'Update Room' : 'Save Room' ?>
                        </button>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Room Gallery</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php if (!empty($room['image'])): ?>
                                <div class="mb-3 border rounded p-1 text-center bg-light">
                                    <?php
                                        $path = $room['image'];
                                        if (strpos($path, 'uploads') === false) {
                                            $path = 'uploads/rooms/' . ltrim($path, '/');
                                        }
                                    ?>
                                    <img src="<?= APP_URL . '/' . ltrim($path, '/') ?>" 
                                        class="img-fluid rounded shadow-sm" 
                                        style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choose file...</label>
                            </div>
                            <small class="text-muted mt-2 d-block text-center italic">Upload a high-quality JPG or PNG.</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    document.querySelector('.custom-file-input').addEventListener('change',function(e){
        var fileName = document.getElementById("image").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>

<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>