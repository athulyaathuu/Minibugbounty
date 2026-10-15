<?php
include('header.php');
include('../connection.php');

/* ===== Severity counts ===== */
$critical = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities WHERE assigned_severity='Critical'"
));

$high = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities WHERE assigned_severity='High'"
));

$medium = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities WHERE assigned_severity='Medium'"
));

$low = mysqli_num_rows(mysqli_query($con,
    "SELECT id FROM vulnerabilities WHERE assigned_severity='Low'"
));

/* ===== Fetch all vulnerabilities ===== */
$where = " WHERE 1 ";

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $where .= " AND (v.title LIKE '%$search%' OR v.description LIKE '%$search%')";
}

if (!empty($_GET['severity'])) {
    $severity = mysqli_real_escape_string($con, $_GET['severity']);
    $where .= " AND (v.assigned_severity='$severity' OR v.suggested_severity='$severity')";
}

if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($con, $_GET['status']);
    $where .= " AND v.status='$status'";
}

$vuln_q = mysqli_query($con,"
    SELECT v.*, u.name
    FROM vulnerabilities v
    JOIN users u ON v.user_id = u.id
    $where
    ORDER BY v.created_at DESC
");

echo "SELECT v.*, u.name
    FROM vulnerabilities v
    JOIN users u ON v.user_id = u.id
    $where
    ORDER BY v.created_at DESC";



?>


    <main class="main-content" style="margin-top: 72px;">
        <div class="page-header">
			<h2><i class="fas fa-bug"></i> Vulnerabilities Management</h2>
			<p class="subheading">
				Monitor all reported vulnerabilities, review proofs of concept, and manage severity and status for each submission.
			</p>
		</div>

        <div class="stats-bar">
			<div class="stat-item critical">
				<i class="fas fa-skull-crossbones"></i>
				<div class="stat-text">
					<div class="stat-number"><?php echo $critical; ?></div>
					<div class="stat-label">Critical</div>
				</div>
			</div>

			<div class="stat-item high">
				<i class="fas fa-exclamation-triangle"></i>
				<div class="stat-text">
					<div class="stat-number"><?php echo $high; ?></div>
					<div class="stat-label">High</div>
				</div>
			</div>

			<div class="stat-item medium">
				<i class="fas fa-exclamation-circle"></i>
				<div class="stat-text">
					<div class="stat-number"><?php echo $medium; ?></div>
					<div class="stat-label">Medium</div>
				</div>
			</div>

			<div class="stat-item low">
				<i class="fas fa-check-circle"></i>
				<div class="stat-text">
					<div class="stat-number"><?php echo $low; ?></div>
					<div class="stat-label">Low</div>
				</div>
			</div>
		</div>


        <div class="filter-section">
			<form method="GET">
				<div class="filter-row">

					<div class="filter-group">
						<label>Search</label>
						<input type="text" name="search" class="form-control"
							   placeholder="Search vulnerabilities..."
							   value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
					</div>

					<div class="filter-group">
						<label>Severity</label>
						<select name="severity" class="form-select">
							<option value="">All Severities</option>
							<?php
							$sevs = array('Critical','High','Medium','Low');
							foreach ($sevs as $s) {
								$sel = (isset($_GET['severity']) && $_GET['severity']==$s) ? 'selected' : '';
								echo "<option value='$s' $sel>$s</option>";
							}
							?>
						</select>
					</div>

					<div class="filter-group">
						<label>Status</label>
						<select name="status" class="form-select">
							<option value="">All Statuses</option>
							<?php
							$sts = array('Pending','Under Review','Accepted','Fixed','Rejected');
							foreach ($sts as $st) {
								$sel = (isset($_GET['status']) && $_GET['status']==$st) ? 'selected' : '';
								echo "<option value='$st' $sel>$st</option>";
							}
							?>
						</select>
					</div>

					<div class="filter-group">
						<label>&nbsp;</label>
						<div style="display:flex; gap:0.5rem;">
							<button class="btn-filter" type="submit">
								<i class="fas fa-filter"></i> Filter
							</button>
							<a href="vulnerabilities.php" class="btn-reset">
								<i class="fas fa-redo"></i> Reset
							</a>
						</div>
					</div>

				</div>
			</form>
			</div>


        <div class="vulnerabilities-grid">

		<?php if (mysqli_num_rows($vuln_q) > 0) { ?>
		<?php while ($v = mysqli_fetch_assoc($vuln_q)) { ?>

		<div class="vuln-card <?php
			echo strtolower(
				!empty($v['assigned_severity'])
				? $v['assigned_severity']
				: $v['suggested_severity']
			);
		?>">
			<div class="vuln-header">
				<div>
					<div class="vuln-id">#VUL-<?php echo $v['id']; ?></div>
					<h3 class="vuln-title"><?php echo htmlspecialchars($v['title']); ?></h3>
				</div>
			</div>

			<p class="vuln-description">
				<?php echo substr(strip_tags($v['description']), 0, 150); ?>...
			</p>

			<div class="vuln-meta">
				<span class="badge badge-<?php echo strtolower($v['assigned_severity'] ?: $v['suggested_severity']); ?>">
					<?php echo !empty($v['assigned_severity']) ? $v['assigned_severity'] : $v['suggested_severity']; ?>
				</span>

				<span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $v['status'])); ?>">
					<?php echo $v['status']; ?>
				</span>
			</div>

			<div class="vuln-footer">
				<div class="reporter-info">
					<div class="reporter-avatar">
						<?php echo strtoupper(substr($v['name'],0,2)); ?>
					</div>
					<div class="reporter-details">
						<div class="reporter-name"><?php echo $v['name']; ?></div>
						<div class="report-date">
							<?php echo date('M d, Y', strtotime($v['created_at'])); ?>
						</div>
					</div>
				</div>

				<div class="vuln-actions">
					<a href="view_vulnerability.php?id=<?php echo $v['id']; ?>" class="btn-icon" title="View">
						<i class="fas fa-eye"></i>
					</a>
				</div>
			</div>
		</div>

		<?php } } else { ?>
		<p>No vulnerabilities found.</p>
		<?php } 
		?>

		</div>

	</main>