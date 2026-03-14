<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Home</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h1>

    <div class="nav-buttons">
        <button onclick="location.href='worker_dashboard.php'">📝 Submit Job Entry</button>
        <button onclick="location.href='worker_profile.php'">👤 Update Profile</button>
        <button onclick="location.href='logout.php'">🚪 Logout</button>
    </div>
</body>
</html>