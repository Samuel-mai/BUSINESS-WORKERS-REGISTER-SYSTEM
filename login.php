<?php
session_start();

$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($username) && !empty($password)) {
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Check password
        if ($password === $row['password']) {
            // Check approval status
            if (trim(strtolower($row['status'])) === 'approved') {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

               header("Location: worker_home.php");
                exit();
            } else {
                echo "⏳ Your account is pending approval by the admin.";
            }
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Username not found.";
    }
} else {
    echo "❌ Please enter both username and password.";
}

$conn->close();
?>