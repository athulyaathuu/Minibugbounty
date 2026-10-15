<?php
include('header.php');
include('../connection.php');

/* Fetch all users */
$q = mysqli_query($con, "SELECT id, name, email, phone FROM users ORDER BY id DESC");
?>

<main class="main-content" style="margin-top:72px;">

    <div class="page-header">
        <h2><i class="fas fa-users"></i> Users List</h2>
        <p class="subheading">View all registered users</p>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>

            <?php if (mysqli_num_rows($q) > 0) { ?>
                <?php while ($u = mysqli_fetch_assoc($q)) { ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['phone']); ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" style="text-align:center;">No users found</td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

</main>

<?php include('footer.php'); ?>
