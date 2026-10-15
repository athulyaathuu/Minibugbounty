<?php
include('header.php');

include('../connection.php');

/* Total vulnerabilities */
$total = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities"
));

/* Severity-wise counts (assigned first, fallback suggested) */
$low = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities 
     WHERE IFNULL(assigned_severity, suggested_severity) = 'Low'"
));

$high = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities 
     WHERE IFNULL(assigned_severity, suggested_severity) = 'High'"
));

$critical = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities 
     WHERE IFNULL(assigned_severity, suggested_severity) = 'Critical'"
));


/* Severity counts */
$sev_q = mysqli_query($con,"
    SELECT IFNULL(assigned_severity, suggested_severity) AS severity, COUNT(*) AS total
    FROM vulnerabilities
    GROUP BY severity
");

$severityData = array(
    'Low' => 0,
    'Medium' => 0,
    'High' => 0,
    'Critical' => 0
);

while ($row = mysqli_fetch_assoc($sev_q)) {
    if (isset($severityData[$row['severity']])) {
        $severityData[$row['severity']] = $row['total'];
    }
}

/* Status counts */
$status_q = mysqli_query($con,"
    SELECT status, COUNT(*) AS total
    FROM vulnerabilities
    GROUP BY status
");

$statusData = array();

while ($row = mysqli_fetch_assoc($status_q)) {
    $statusData[$row['status']] = $row['total'];
}
?>


    <main class="main-content" style="margin-top: 72px;">
        <div class="dashboard-header">
            <h2><i class="fas fa-chart-pie me-2"></i>Dashboard Overview</h2>
            <p>Monitor and manage security vulnerabilities</p>
        </div>

        <div class="row">

			<div class="col-lg-3 col-md-6 mb-4">
				<div class="stat-card">
					<div class="stat-icon">
						<i class="fas fa-bug"></i>
					</div>
					<div class="stat-number"><?php echo $total; ?></div>
					<div class="stat-label">Total Vulnerabilities</div>
				</div>
			</div>

			<div class="col-lg-3 col-md-6 mb-4">
				<div class="stat-card low">
					<div class="stat-icon">
						<i class="fas fa-check-circle"></i>
					</div>
					<div class="stat-number"><?php echo $low; ?></div>
					<div class="stat-label">Low Severity</div>
				</div>
			</div>

			<div class="col-lg-3 col-md-6 mb-4">
				<div class="stat-card high">
					<div class="stat-icon">
						<i class="fas fa-exclamation-triangle"></i>
					</div>
					<div class="stat-number"><?php echo $high; ?></div>
					<div class="stat-label">High Severity</div>
				</div>
			</div>

			<div class="col-lg-3 col-md-6 mb-4">
				<div class="stat-card critical">
					<div class="stat-icon">
						<i class="fas fa-skull-crossbones"></i>
					</div>
					<div class="stat-number"><?php echo $critical; ?></div>
					<div class="stat-label">Critical Severity</div>
				</div>
			</div>

		</div>


        <h3 class="section-title"><i class="fas fa-chart-bar"></i> Analytics</h3>

		<div class="row">

			<!-- Severity Chart -->
			<div class="col-lg-6 mb-4">
				<div class="chart-container">
					<h5 class="text-center mb-3">
						<i class="fas fa-chart-pie"></i> Severity Distribution
					</h5>
					<div class="chart-box">
						<canvas id="severityChart"></canvas>
					</div>
				</div>
			</div>

			<!-- Status Chart -->
			<div class="col-lg-6 mb-4">
				<div class="chart-container">
					<h5 class="text-center mb-3">
						<i class="fas fa-chart-bar"></i> Vulnerability Status
					</h5>
					<div class="chart-box">
						<canvas id="statusChart"></canvas>
					</div>
					
				</div>
			</div>

		</div>

		
		<?php


$recent_q = mysqli_query($con,"
    SELECT v.id, v.title, v.status, v.created_at,
           IFNULL(v.assigned_severity, v.suggested_severity) AS severity,
           u.email
    FROM vulnerabilities v
    JOIN users u ON v.user_id = u.id
    ORDER BY v.created_at DESC
    LIMIT 5
");
?>


        <h3 class="section-title"><i class="fas fa-list"></i> Recent Vulnerabilities</h3>

		<div class="table-container">
			<h4><i class="fas fa-table me-2"></i> Latest Reports</h4>

			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Severity</th>
							<th>Status</th>
							<th>Reported By</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
					<?php if (mysqli_num_rows($recent_q) > 0) { ?>
						<?php while ($v = mysqli_fetch_assoc($recent_q)) { ?>
							<tr>
								<td>#VUL-<?php echo $v['id']; ?></td>

								<td><?php echo htmlspecialchars($v['title']); ?></td>

								<td>
									<span class="badge badge-<?php echo strtolower($v['severity']); ?>">
										<?php echo $v['severity']; ?>
									</span>
								</td>

								<td>
									<span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $v['status'])); ?>">
										<?php echo $v['status']; ?>
									</span>
								</td>

								<td><?php echo htmlspecialchars($v['email']); ?></td>

								<td><?php echo date('Y-m-d', strtotime($v['created_at'])); ?></td>

								<td>
									<a href="view_vulnerability.php?id=<?php echo $v['id']; ?>" class="btn-view">
										View
									</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="7" style="text-align:center;">No vulnerabilities found</td>
						</tr>
					<?php } ?>
					</tbody>

				</table>
			</div>
		</div>

    </main>

<script>
/* Severity Pie Chart */
new Chart(document.getElementById('severityChart'), {
    type: 'pie',
    data: {
        labels: ['Low', 'Medium', 'High', 'Critical'],
        datasets: [{
            data: [
                <?php echo $severityData['Low']; ?>,
                <?php echo $severityData['Medium']; ?>,
                <?php echo $severityData['High']; ?>,
                <?php echo $severityData['Critical']; ?>
            ],
            backgroundColor: [
                '#22c55e',
                '#facc15',
                '#f97316',
                '#ef4444'
            ]
        }]
    }
});

/* Status Bar Chart */
new Chart(document.getElementById('statusChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($statusData)); ?>,
        datasets: [{
            label: 'Vulnerabilities',
            data: <?php echo json_encode(array_values($statusData)); ?>,
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>


<?php
include('footer.php');
?>