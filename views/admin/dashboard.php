<?php include_once __DIR__ . '/layouts/admin_header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                RM <?= number_format($totalRevenue, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalBookings ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Rooms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalRooms ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMessages ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview (<?= $selectedYear ?>)</h6>

                    <div class="form-group mb-0">
                        <select class="form-control form-control-sm" id="yearFilter">
                            <?php 
                            // Fallback in case there are no bookings yet
                            if(empty($availableYears)) $availableYears = [date('Y')];
                            
                            foreach($availableYears as $year): ?>
                                <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Inquiries</h6>
                </div>
                <div class="card-body">
                    <?php foreach(array_slice($recentMessages, 0, 3) as $msg): ?>
                        <div class="mb-3 border-bottom pb-2">
                            <div class="small text-gray-500"><?= date('d M Y', strtotime($msg['created_at'])) ?></div>
                            <span class="font-weight-bold"><?= htmlspecialchars($msg['name']) ?></span>
                            <p class="text-truncate mb-0 small"><?= htmlspecialchars($msg['message']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    <a href="<?= APP_URL ?>/admin/messages" class="btn btn-sm btn-primary btn-block">View All Messages</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Action Required: Unpaid Bookings</h6>
                    <span class="badge badge-danger"><?= count($unpaidBookings) ?> Pending</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Ref No</th>
                                    <th>Customer</th>
                                    <th>Check In</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($unpaidBookings as $booking): ?>
                                <tr>
                                    <td><?= $booking['booking_ref_no'] ?></td>
                                    <td><?= $booking['full_name'] ?></td>
                                    <td><?= date('d M Y', strtotime($booking['check_in'])) ?></td>
                                    <td>RM <?= number_format($booking['total_amount'], 2) ?></td>
                                    <td><span class="badge badge-warning"><?= ucfirst($booking['payment_status']) ?></span></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/admin/bookings/view/<?= $booking['id'] ?>" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Debugging: Check the console (Press F12) to see if data exists
    const revenueData = <?= json_encode($revenueChartValues) ?>;
    console.log("Revenue Data:", revenueData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Revenue (RM)",
                data: revenueData,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return 'RM ' + value; }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
// Handle Year Change
document.getElementById('yearFilter').addEventListener('change', function() {
    const year = this.value;
    // Redirect to the dashboard with the year parameter
    window.location.href = "<?= APP_URL ?>/auth/dashboard?year=" + year;
});
</script>
<?php include_once __DIR__ . '/layouts/admin_footer.php'; ?>