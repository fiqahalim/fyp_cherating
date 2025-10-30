<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bookings Management</h6>
            <!-- Create New Room Button -->
            <!-- <a href="<?= APP_URL . '/admin/rooms/create' ?>" class="btn btn-success btn-sm float-right">Create New Booking</a> -->
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_length" id="dataTable_length">
                                <label>Show 
                                    <select name="dataTable_length" aria-controls="dataTable" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> entries
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div id="dataTable_filter" class="dataTables_filter">
                                <label>Search:
                                    <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="dataTable">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Booking Ref.No</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Payment Status</th>
                                        <th>Total Amount (MYR)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td class="text-primary"><strong><?= htmlspecialchars($booking['booking_ref_no']) ?></strong></td>
                                            <td><?= htmlspecialchars($booking['full_name']) ?></td>
                                            <td><?= htmlspecialchars($booking['email']) ?></td>
                                            <td><?= htmlspecialchars($booking['phone']) ?></td>
                                            <td><?= htmlspecialchars($booking['check_in']) ?></td>
                                            <td><?= htmlspecialchars($booking['check_out']) ?></td>
                                            <td class="<?= strtolower($booking['payment_status']) === 'unpaid' ? 'payment-status-unpaid' : '' ?>">
                                                <?= ucfirst(htmlspecialchars($booking['payment_status'])) ?>
                                            </td>
                                            <td><strong><?= htmlspecialchars($booking['total_amount']) ?></strong></td>
                                            <td class="action-buttons">
                                                <a href="<?= APP_URL . '/admin/bookings/view/' . $booking['id'] ?>" class="btn btn-info  btn-sm"><i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= APP_URL . '/admin/bookings/delete/' . $booking['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this booking?')"><i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">Showing 1 to <?= count($bookings) ?> of <?= count($bookings) ?> entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                <ul class="pagination">
                                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>