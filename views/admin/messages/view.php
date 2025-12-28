<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>
<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Messages Details</h1>
        <a href="<?= APP_URL . '/admin/messages' ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <?php Flash::display(); ?>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle mr-2"></i>Sender Profile</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light d-inline-block rounded-circle p-3 mb-2">
                            <i class="fas fa-user fa-3x text-gray-400"></i>
                        </div>
                        <h5 class="font-weight-bold text-gray-800 mb-0"><?= htmlspecialchars($message['name']) ?></h5>
                        <p class="text-muted small">Sent on <?= date('j F Y, g:i a', strtotime($message['created_at'])) ?></p>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted small font-weight-bold">EMAIL</span>
                            <span class="text-primary"><?= htmlspecialchars($message['email']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted small font-weight-bold">PHONE</span>
                            <span><?= htmlspecialchars($message['phone']) ?></span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <a href="<?= $whatsappLink ?>" class="btn btn-success btn-block shadow-sm" target="_blank">
                            <i class="fab fa-whatsapp mr-2"></i> Chat on WhatsApp
                        </a>
                        <!-- <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-outline-primary btn-block shadow-sm">
                            <i class="fas fa-envelope mr-2"></i> Reply via Email
                        </a> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-comment-alt mr-2"></i>Message Content</h6>
                    <span class="badge badge-light border text-muted px-3 py-2">
                        <i class="far fa-clock mr-1"></i> Received <?= date('H:i', strtotime($message['created_at'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="p-4 bg-light rounded border" style="min-height: 250px;">
                        <p class="lead text-gray-800 mb-0" style="white-space: pre-wrap; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= APP_URL . '/admin/messages' ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-chevron-left mr-1"></i> Return to Inbox
                        </a>
                        <a href="<?= APP_URL . '/admin/messages/delete/' . $message['id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this message?')">
                            <i class="fas fa-trash-alt mr-1"></i> Delete Message
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>