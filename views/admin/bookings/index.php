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
                            <div id="dataTable_filter" class="dataTables_filter text-right">
                                <form action="" method="GET" class="form-inline d-inline-block">
                                    <label>Search:
                                        <input type="search" name="search" class="form-control form-control-sm" 
                                            placeholder="Ref No, Name..." 
                                            value="<?= htmlspecialchars($search ?? '') ?>">
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Ref.No</th>
                                        <th>Customer</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Method</th>
                                        <th>Deposit</th>
                                        <th>Balance After</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <?php
                                            $method = $booking['payment_method'] ?? '';
                                            $vStatus = $booking['payment_verify_status'] ?? '';
                                            $isWaitingQR = ($method === 'qr' && $vStatus === 'pending');
                                        ?>
                                        <tr>
                                            <td class="text-primary"><strong><?= htmlspecialchars($booking['booking_ref_no']) ?></strong></td>
                                            <td><?= htmlspecialchars($booking['full_name']) ?></td>
                                            <td><?= date('d M Y', strtotime($booking['check_in'])) ?></td>
                                            <td><?= date('d M Y', strtotime($booking['check_out'])) ?></td>
                                            <td>
                                                <?php if (strtolower($method) === 'qr'): ?>
                                                    <span class="badge badge-light text-dark"><i class="fas fa-qrcode text-primary"></i> QR</span>
                                                <?php elseif (strtolower($method) === 'cash'): ?>
                                                    <span class="badge badge-light text-dark"><i class="fas fa-money-bill-wave text-success"></i> Cash</span>
                                                <?php else: ?>
                                                    <span class="text-muted"><?= strtoupper($method) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong>RM <?= number_format($booking['payment_amount'], 2) ?></strong></td>
                                            <td><strong>RM <?= number_format($booking['balance_after'], 2) ?></strong></td>
                                            <td>
                                                <?php if ($isWaitingQR): ?>
                                                    <span class="badge badge-info animate-pulse">
                                                        <i class="fas fa-clock"></i> Waiting Verification
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge <?= $booking['payment_status'] === 'paid' ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= strtoupper($booking['payment_status']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
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
                            <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                                Showing <?= $offset + 1 ?> to <?= min($offset + $resultsPerPage, $totalBookings) ?> of <?= $totalBookings ?> entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                <ul class="pagination">
                                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= $currentPage == $totalPages || $totalPages == 0 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
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