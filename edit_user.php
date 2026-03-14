<?php
$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Get user_id from query string
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("❌ Invalid user ID.");
}

$user_id = intval($_GET['user_id']);

// 2. If form submitted, update user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname  = $_POST['firstname'];
    $sirname    = $_POST['sirname'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $status     = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users 
                            SET firstname=?, sirname=?, username=?, email=?, phone=?, department=?, status=? 
                            WHERE id=?");
    $stmt->bind_param("sssssssi", $firstname, $sirname, $username, $email, $phone, $department, $status, $user_id);

    if ($stmt->execute()) {
        echo "✅ User updated successfully. <a href='admin_dashboard.php'>Go back</a>";
        exit();
    } else {
        echo "❌ Error updating user: " . $stmt->error;
    }
}

// 3. Fetch user details
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
if (!$result || $result->num_rows === 0) {
    die("❌ User not found.");
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Edit User</h1>
    <form method="POST">
        <label>First Name:</label><br>
        <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required><br><br>

        <label>Surname:</label><br>
        <input type="text" name="sirname" value="<?= htmlspecialchars($user['sirname']) ?>" required><br><br>

        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br><br>

        <label>Department:</label><br>
        <input type="text" name="department" value="<?= htmlspecialchars($user['department']) ?>" required><br><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $user['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select><br><br>

        <button type="submit">💾 Save Changes</button>
        <button type="button" onclick="window.location.href='admin_dashboard.php'">⬅ Back to Dashboard</button>
    </form>
</body>
</html>