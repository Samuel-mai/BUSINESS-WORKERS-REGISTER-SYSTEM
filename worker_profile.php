<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// ✅ Handle update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname  = $_POST['firstname'];
    $sirname    = $_POST['sirname'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $password   = $_POST['password'];

    if (!empty($password)) {
        // update including password
        $stmt = $conn->prepare("UPDATE users SET firstname=?, sirname=?, username=?, email=?, phone=?, department=?, password=? WHERE id=?");
        $stmt->bind_param("sssssssi", $firstname, $sirname, $username, $email, $phone, $department, $password, $user_id);
    } else {
        // update without password change
        $stmt = $conn->prepare("UPDATE users SET firstname=?, sirname=?, username=?, email=?, phone=?, department=? WHERE id=?");
        $stmt->bind_param("ssssssi", $firstname, $sirname, $username, $email, $phone, $department, $user_id);
    }

    if ($stmt->execute()) {
        $success = "✅ Profile updated successfully!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Fetch current user data
$stmt = $conn->prepare("SELECT firstname, sirname, username, email, phone, department FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($firstname, $sirname, $username, $email, $phone, $department);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Profile</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Update Profile</h1>

    <form method="POST">
        Firstname: <input type="text" name="firstname" value="<?= htmlspecialchars($firstname) ?>" required><br><br>
        Sirname: <input type="text" name="sirname" value="<?= htmlspecialchars($sirname) ?>" required><br><br>
        Username: <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required><br><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br><br>
        Phone: <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required><br><br>
        Department: <input type="text" name="department" value="<?= htmlspecialchars($department) ?>"><br><br>

        Password (leave blank to keep current): 
        <input type="password" name="password"><br><br>

        <button type="submit">Update</button>
        <button type="button" onclick="window.location.href='worker_dashboard.php'">⬅ Back</button>
    </form>

    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>