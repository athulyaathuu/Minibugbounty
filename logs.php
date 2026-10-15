<?php
include('header.php');
include('../connection.php');

$logs_q = mysqli_query($con, "
    SELECT l.*, u.email 
    FROM activity_logs l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 20
");

$total_logs = mysqli_num_rows(mysqli_query($con, "SELECT id FROM activity_logs"));
?>

<main class="main-content" style="margin-top:72px;">

    <div class="page-header">
        <h2><i class="fas fa-history"></i> Activity Logs</h2>
        <p class="subheading">Track system events and admin actions</p>
    </div>

    <div class="logs-container">
        <div class="logs-header">
            <h4><i class="fas fa-stream me-2"></i> Recent Activity</h4>
            <span class="logs-count">Showing <?php echo mysqli_num_rows($logs_q); ?> of <?php echo $total_logs; ?> logs</span>
        </div>

        <div class="timeline">

        <?php if (mysqli_num_rows($logs_q) > 0) { ?>
        <?php while ($log = mysqli_fetch_assoc($logs_q)) { ?>

            <div class="timeline-item">
                <div class="timeline-marker <?php echo $log['action_type']; ?>"></div>

                <div class="log-card">
                    <div class="log-header">
                        <div class="log-type">
                            <div class="log-icon <?php echo $log['action_type']; ?>">
                                <i class="fas 
                                <?php
                                    echo ($log['action_type']=='danger') ? 'fa-exclamation-triangle' :
                                         (($log['action_type']=='success') ? 'fa-check-circle' :
                                         (($log['action_type']=='warning') ? 'fa-sign-in-alt' : 'fa-info-circle'));
                                ?>"></i>
                            </div>

                            <div class="log-details">
                                <h5><?php echo $log['action_title']; ?></h5>
                                <div class="log-meta">
                                    <?php if (!empty($log['reference_id'])) { ?>
                                        <span><i class="fas fa-layer-group"></i> <?php echo $log['reference_id']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="log-timestamp">
                            <i class="fas fa-clock"></i>
                            <?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?>
                        </div>
                    </div>

                    <p class="log-description">
                        <?php echo $log['action_description']; ?>
                    </p>

                    <div class="log-footer">
                        <div class="log-user">
                            <div class="user-avatar">
                                <?php
                                echo $log['email']
                                    ? strtoupper(substr($log['email'],0,2))
                                    : 'SY';
                                ?>
                            </div>
                            <span class="user-name">
                                <?php echo $log['email'] ? $log['email'] : 'System'; ?>
                            </span>
                        </div>
                        <span class="log-ip"><?php echo $log['ip_address']; ?></span>
                    </div>
                </div>
            </div>

        <?php } } else { ?>
            <p>No activity logs found.</p>
        <?php } ?>

        </div>
    </div>
</main>

<?php include('footer.php'); ?>
