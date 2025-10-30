<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>
<div class="container mt-5">
    <?php Flash::display(); ?>

    <h2 class="mb-4"><?= isset($room) ? 'Edit Room' : 'Create New Room' ?></h2>

    <form action="<?= isset($room) ? APP_URL . '/admin/rooms/edit/' . $room['id'] : APP_URL . '/admin/rooms/create' ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Room Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= isset($room) ? htmlspecialchars($room['name']) : '' ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Room Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?= isset($room) ? htmlspecialchars($room['description']) : '' ?></textarea>
        </div>

        <!-- Row: Price, Total Rooms, Capacity -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="price" class="form-label">Price (RM)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01"
                    value="<?= isset($room) ? htmlspecialchars($room['price']) : '' ?>" required>
            </div>

            <div class="col-md-4">
                <label for="total_rooms" class="form-label">Total Rooms</label>
                <input type="number" class="form-control" id="total_rooms" name="total_rooms" min="1"
                    value="<?= isset($room) ? htmlspecialchars($room['total_rooms']) : '' ?>" required>
            </div>

            <div class="col-md-4">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" min="1"
                    value="<?= isset($room) ? htmlspecialchars($room['capacity']) : '' ?>" required>
            </div>
        </div>

        <!-- Image Upload (own row) -->
        <div class="mb-3">
            <label for="image" class="form-label">Room Image (optional)</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <?php if (!empty($room['image'])): ?>
                <div class="mt-2">
                    <img src="<?= APP_URL . $room['image'] ?>" alt="Room Image" width="150">
                </div>
            <?php endif; ?>
        </div>

        <!-- Room Status (own row) -->
        <div class="mb-3">
            <label for="status" class="form-label">Room Status</label>
            <select class="form-select dropdown-toggle" id="status" name="status" required>
                <option value="active" <?= (isset($room) && $room['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= (isset($room) && $room['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= isset($room) ? 'Update Room' : 'Create Room' ?></button>
        <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>