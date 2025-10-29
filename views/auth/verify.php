<div class="container mt-5">
  <div class="col-md-6 mx-auto">
    <div class="card shadow-sm">
      <div class="card-header bg-success text-white text-center">
        <h4>Verify Your WhatsApp</h4>
      </div>
      <div class="card-body">
        <p class="text-center">Enter the 6-digit code sent to your WhatsApp.</p>
        <form action="<?= APP_URL ?>/auth/verify" method="POST">
          <div class="mb-3">
            <label for="verification_code" class="form-label">Verification Code</label>
            <input type="text" class="form-control" id="verification_code" name="verification_code" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Verify</button>
        </form>
      </div>
    </div>
  </div>
</div>
