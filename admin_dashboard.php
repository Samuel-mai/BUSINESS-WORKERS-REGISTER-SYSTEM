<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===========================
   1. Fetch Users
   =========================== */
$users_sql = "SELECT id, firstname, sirname, username, email, phone, department, status 
              FROM users 
              ORDER BY id DESC";

$users_result = $conn->query($users_sql);

/* ===========================
   2. Fetch Job Entries
   =========================== */
$jobs_sql = "SELECT job_entries.id, job_entries.job_date, job_entries.job_time, 
                    users.firstname, users.sirname, users.username, 
                    users.email, users.phone, users.department
             FROM job_entries
             JOIN users ON job_entries.user_id = users.id
             ORDER BY job_entries.job_date DESC, job_entries.job_time ASC";

$jobs_result = $conn->query($jobs_sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="Style.css">

<style>
table{
border-collapse:collapse;
width:95%;
margin:20px auto;
}

th,td{
border:1px solid #ccc;
padding:8px;
text-align:left;
}

th{
background:#0078d7;
color:white;
}

h2{
text-align:center;
margin-top:30px;
}

.btn-approve{background:#28a745;color:white;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;}
.btn-reject{background:#dc3545;color:white;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;}
.btn-delete{background:#ff0000;color:white;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;}
.btn-edit{background:#0078d7;color:white;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;}

</style>
</head>

<body>

<h1 style="text-align:center;">Admin Dashboard</h1>

<!-- Download Excel -->
<form action="download_excel.php" method="post" style="text-align:center;margin-bottom:20px;">
<button type="submit" style="background:#28a745;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;">
📥 Download as Excel
</button>
</form>


<!-- =========================
     USERS TABLE
========================= -->

<h2>Registered Users</h2>

<table>

<tr>
<th>ID</th>
<th>Firstname</th>
<th>Surname</th>
<th>Username</th>
<th>Email</th>
<th>Phone</th>
<th>Department</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php
if ($users_result && $users_result->num_rows > 0){

while($user = $users_result->fetch_assoc()){
?>

<tr>

<td><?= $user['id'] ?></td>

<td><?= htmlspecialchars($user['firstname']) ?></td>

<td><?= htmlspecialchars($user['sirname']) ?></td>

<td><?= htmlspecialchars($user['username']) ?></td>

<td><?= htmlspecialchars($user['email']) ?></td>

<td><?= htmlspecialchars($user['phone']) ?></td>

<td><?= htmlspecialchars($user['department']) ?></td>

<td><?= htmlspecialchars($user['status']) ?></td>

<td>

<?php if($user['status'] !== 'approved'){ ?>
<form method="POST" action="update_user_status.php" style="display:inline;">
<input type="hidden" name="user_id" value="<?= $user['id'] ?>">
<input type="hidden" name="status" value="approved">
<button class="btn-approve">Approve</button>
</form>
<?php } ?>

<?php if($user['status'] !== 'rejected'){ ?>
<form method="POST" action="update_user_status.php" style="display:inline;">
<input type="hidden" name="user_id" value="<?= $user['id'] ?>">
<input type="hidden" name="status" value="rejected">
<button class="btn-reject">Reject</button>
</form>
<?php } ?>

<form method="GET" action="edit_user.php" style="display:inline;">
<input type="hidden" name="user_id" value="<?= $user['id'] ?>">
<button class="btn-edit">Edit</button>
</form>

</td>

</tr>

<?php
}
}
else{
echo "<tr><td colspan='9'>No users found</td></tr>";
}
?>

</table>


<!-- =========================
     JOB ENTRIES
========================= -->

<h2>Job Entries (Grouped by Day)</h2>

<?php

if ($jobs_result && $jobs_result->num_rows > 0){

$currentDate = "";

while($job = $jobs_result->fetch_assoc()){

$jobDate = $job['job_date'];
$dayName = date("l", strtotime($jobDate));

if($jobDate !== $currentDate){

if($currentDate !== "") echo "</table><br>";

echo "<h3 style='text-align:center;'>$dayName - $jobDate</h3>";

echo "<table>
<tr>
<th>First Name</th>
<th>Surname</th>
<th>Username</th>
<th>Email</th>
<th>Phone</th>
<th>Department</th>
<th>Date</th>
<th>Time</th>
<th>Action</th>
</tr>";

$currentDate = $jobDate;

}
?>

<tr>

<td><?= htmlspecialchars($job['firstname']) ?></td>

<td><?= htmlspecialchars($job['sirname']) ?></td>

<td><?= htmlspecialchars($job['username']) ?></td>

<td><?= htmlspecialchars($job['email']) ?></td>

<td><?= htmlspecialchars($job['phone']) ?></td>

<td><?= htmlspecialchars($job['department']) ?></td>

<td><?= htmlspecialchars($job['job_date']) ?></td>

<td><?= htmlspecialchars($job['job_time']) ?></td>

<td>

<form method="POST" action="delete_entry.php"
onsubmit="return confirm('Delete this entry?');">

<input type="hidden" name="job_id" value="<?= $job['id'] ?>">

<button class="btn-delete">Delete</button>

</form>

</td>

</tr>

<?php
}

echo "</table>";
}
else{
echo "<p style='text-align:center;'>No job entries found</p>";
}

$conn->close();
?>

</body>
</html>