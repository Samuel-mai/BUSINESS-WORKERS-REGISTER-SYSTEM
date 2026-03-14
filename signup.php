<?php
$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $sirname = $_POST['sirname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $department = $_POST['department'];
    $status = "pending";
    $unique_id = uniqid("BW-");

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (firstname, sirname, username, email, phone, password, department, status, unique_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstname, $sirname, $username, $email, $phone, $password, $department, $status, $unique_id);

    if ($stmt->execute()) {
        echo "✅ Account created successfully. Waiting for admin approval.";
        echo "<br><a href='Signin.html'>Login Here</a>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>