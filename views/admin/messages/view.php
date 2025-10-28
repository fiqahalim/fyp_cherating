<div class="container mt-5">
    <?php Flash::display(); ?>

    <a href="<?= APP_URL . '/admin/messages' ?>" class="btn btn-light btn-icon-split mt-3">
        <span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Back to List</span>
    </a>

    <div class="card shadow mt-3 mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Message Details</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <p><strong>Name:</strong> <?= htmlspecialchars($message['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($message['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($message['phone']) ?></p>
                    <p><strong>Date Send:</strong> <?= date('j F Y', strtotime($message['created_at'])) ?></p>
                    <p><strong>Message:</strong> <?= htmlspecialchars($message['message']) ?></p>
                </div>
                <div class="col-md-12">
                    <p>
                        <a href="<?= $whatsappLink ?>" class="btn btn-success btn-icon-split" target="_blank" rel="noopener noreferrer">
                            <span class="icon text-white-50">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                            <span class="text">Send WhatsApp Message</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>