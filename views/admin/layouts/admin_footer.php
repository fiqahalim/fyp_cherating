<?php
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
$isAdmin = isset($_SESSION['admin_id']);
?>

        </div> <!-- End of Main Content -->
      </div> <!-- End of Content Wrapper -->
  </div> <!-- End of Page Wrapper -->

  <!-- Footer -->
  <footer class="sticky-footer bg-white text-center">
    <small>&copy; <?= date('Y') ?> Cherating - Guest House. All rights reserved. Design by <a href="#">Amelia</a></small>
  </footer>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
  </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="<?= $base_url ?>/auth/logout">Logout</a>
            </div>
        </div>
    </div>
    </div>

  <!-- JavaScript Core -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= $base_url ?>/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= $base_url ?>/assets/js/sb-admin-2.min.js"></script>
<script src="<?= $base_url ?>/vendor/chart.js/Chart.min.js"></script>
<script>
window.addEventListener('beforeunload', e => {
  // Cancel any forced redirect
  e.stopImmediatePropagation();
});
</script>

</body>
</html>