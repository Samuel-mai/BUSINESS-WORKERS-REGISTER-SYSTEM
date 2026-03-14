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

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $job_date = date("Y-m-d");   // today’s date
    $job_time = date("H:i:s");   // current time

    $stmt = $conn->prepare("INSERT INTO job_entries (user_id, job_date, job_time) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $job_date, $job_time);

    if ($stmt->execute()) {
        $message = "✅ Job entry submitted successfully! Redirecting to Home...";
        // Redirect after 3 seconds
        echo "<script>
                alert('✅ Job entry submitted successfully!');
                setTimeout(function(){
                    window.location.href = 'index.html';
                }, 2000);
              </script>";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h1>

    <form method="POST">
        <button type="submit">Submit Job Entry (Now)</button>
    </form>

    <p><?php echo $message; ?></p>
</body>
</html>