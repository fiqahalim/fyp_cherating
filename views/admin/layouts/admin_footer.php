<?php
$base_url = '/fyp_cherating';
$isAdmin = isset($_SESSION['admin_id']); // check if admin logged in
?>

      </div> <!-- End of Main Content -->
    </div> <!-- End of Content Wrapper -->
  </div> <!-- End of Page Wrapper -->
  
  <footer class="sticky-footer bg-white text-center">
    <small>&copy; <?= date('Y') ?> Cherating - GuestHouse. All rights reserved. Design by <a href=""> Amelia</small>
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
            <a class="btn btn-primary" href="<?= $base_url ?>/admin/logout">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="<?= $base_url ?>/vendor/jquery/jquery.min.js"></script>
  <script src="<?= $base_url ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?= $base_url ?>/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
   <script src="<?= $base_url ?>/assets/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="<?= $base_url ?>/vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="<?= $base_url ?>/assets/js/demo/chart-area-demo.js"></script>
  <script src="<?= $base_url ?>/assets/js/demo/chart-pie-demo.js"></script>
</body>
</html>