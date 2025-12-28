<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container-fluid mt-4">
    <?php Flash::display(); ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verify Payment Request</h1>
        <a href="<?= APP_URL . '/admin/payments' ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-invoice-dollar"></i> Customer Receipt</h6>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($payment['receipt_image'])): ?>
                        <?php 
                            // Clean the path to ensure no leading slashes
                            $cleanPath = ltrim($payment['receipt_image'], '/');
                            $fullUrl = APP_URL . '/' . $cleanPath;
                        ?>
                        
                        <a href="<?= $fullUrl ?>" target="_blank">
                            <img src="<?= $fullUrl ?>" 
                                class="img-fluid rounded border shadow-sm" 
                                style="max-height: 500px;" 
                                alt="Receipt">
                        </a>
                        
                        <p class="mt-2 text-muted small"><i class="fas fa-search-plus"></i> Click image to enlarge</p>
                        
                        <div class="mt-3">
                            <a href="<?= $fullUrl ?>" download="Receipt_<?= $payment['payment_ref_no'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Download Receipt
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="py-5 bg-light border rounded">
                            <i class="fas fa-receipt fa-4x text-gray-300"></i>
                            <p class="mt-3 text-gray-500 italic">No receipt image uploaded.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label class="text-xs font-weight-bold text-uppercase mb-1">Payment Method</label>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge badge-pill badge-primary">
                                    <i class="fas fa-qrcode"></i> <?= strtoupper($payment['payment_method']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-xs font-weight-bold text-uppercase mb-1">Status</label>
                            <div class="h5 mb-0">
                                <?php if ($payment['verified'] === 'pending'): ?>
                                    <span class="badge badge-warning text-dark animate-pulse">Pending Verification</span>
                                <?php elseif ($payment['verified'] === 'verified'): ?>
                                    <span class="badge badge-success">Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless">
                        <tr><th width="40%">Payment Ref:</th><td><?= $payment['payment_ref_no'] ?></td></tr>
                        <tr><th>Billplz ID:</th><td><?= $payment['billplz_id'] ?: 'N/A' ?></td></tr>
                        <tr><th>Amount Paid:</th><td class="text-success font-weight-bold">RM <?= number_format($payment['amount'], 2) ?></td></tr>
                        <tr><th>Balance After:</th><td class="text-danger font-weight-bold">RM <?= number_format($payment['balance_after'], 2) ?></td></tr>
                        <tr><th>Uploaded On:</th><td><?= date('d M Y, h:i A', strtotime($payment['created_at'])) ?></td></tr>
                    </table>

                    <hr>

                    <?php if ($payment['verified'] === 'pending'): ?>
                        <div class="bg-gray-100 p-4 rounded border">
                            <h6 class="font-weight-bold text-dark">Take Action</h6>
                            <p class="small text-muted mb-3">Please cross-check the receipt amount with your bank statement before verifying.</p>
                            
                            <form action="<?= APP_URL ?>/admin/payments/verify/<?= $payment['id'] ?>" method="POST">
                                <div class="form-group">
                                    <label class="small font-weight-bold">Internal Note / Rejection Reason (Optional):</label>
                                    <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Example: Payment received, looks good!"></textarea>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" name="status" value="verified" class="btn btn-success flex-fill mr-2" onclick="return confirm('Confirm this payment is correct?')">
                                        <i class="fas fa-check"></i> Approve & Mark as Paid
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-danger flex-fill" onclick="return confirm('Are you sure you want to reject this payment?')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle"></i> This payment was <strong><?= $payment['verified'] ?></strong> 
                            <?php if (!empty($payment['rejection_reason'])): ?>
                                <br><small>Note: <?= htmlspecialchars($payment['rejection_reason']) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>