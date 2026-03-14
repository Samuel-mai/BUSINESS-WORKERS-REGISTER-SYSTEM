<?php
$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$identifier = trim($_POST['identifier']);
if (empty($identifier)) {
    die("❌ Please provide email or phone number.");
}

// check if input is email or phone
if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
    $sql = "SELECT * FROM users WHERE email = ?";
} else {
    $sql = "SELECT * FROM users WHERE phone = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // ✅ Instead of sending link, redirect directly to reset form
    header("Location: reset_password.php?user_id=" . $user['id']);
    exit();
} else {
    echo "❌ No account found with that email or phone.";
}