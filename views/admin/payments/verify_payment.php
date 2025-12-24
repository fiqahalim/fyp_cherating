<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Verify Payment for Booking Ref: <?= $payment['booking_id'] ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center border-right">
                    <h5>Customer Receipt</h5>
                    <img src="<?= APP_URL ?>/uploads/receipts/<?= $payment['receipt_image'] ?>" 
                         class="img-fluid border rounded shadow-sm mb-3" 
                         style="max-height: 600px; cursor: zoom-in;" 
                         onclick="window.open(this.src)">
                    <p class="text-muted small">Click image to enlarge</p>
                </div>

                <div class="col-md-6">
                    <form action="<?= APP_URL ?>/admin/payments/update_status" method="POST" id="verificationForm">
                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                        
                        <div class="alert alert-info">
                            <strong>Payment Details:</strong><br>
                            Amount: RM <?= number_format($payment['amount'], 2) ?><br>
                            Method: <?= strtoupper($payment['payment_method']) ?>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Internal Remarks / Rejection Reason</label>
                            <textarea name="reason" id="rejection_reason" class="form-control" rows="4" 
                                placeholder="If rejecting, please explain why (e.g., Image too blurry, incorrect amount)..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" name="status" value="rejected" class="btn btn-danger btn-icon-split" onclick="return confirmReject()">
                                <span class="icon text-white-50"><i class="fas fa-times"></i></span>
                                <span class="text">Reject Receipt</span>
                            </button>

                            <button type="submit" name="status" value="approved" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                                <span class="text">Approve Payment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmReject() {
    const reason = document.getElementById('rejection_reason').value.trim();
    if (reason === "") {
        alert("Please provide a reason for rejection so the customer knows what to fix.");
        document.getElementById('rejection_reason').focus();
        return false;
    }
    return confirm("Are you sure you want to reject this payment?");
}
</script>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>