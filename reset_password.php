<?php
$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_GET['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        echo "❌ Passwords do not match.";
    } elseif (!empty($email) && !empty($new_password)) {
        // 🚨 For real systems, hash the password:
        // $new_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
            echo "✅ Password reset successfully. <a href='Signin.html'>Login here</a>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Email or password is missing.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h2>Reset Your Password</h2>
    <form method="POST">
        <!-- Hidden email field -->
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

        <label for="new_password">New Password:</label><br>
        <input type="password" name="new_password" required><br><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>