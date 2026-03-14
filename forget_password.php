<?php
$conn = new mysqli("localhost", "root", "", "business_register");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $identifier = $_POST['identifier'];

    if (!empty($identifier)) {

        $stmt = $conn->prepare("SELECT email FROM users WHERE email=? OR username=?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {

            $stmt->bind_result($email);
            $stmt->fetch();

            header("Location: reset_password.php?email=" . urlencode($email));
            exit();

        } else {
            $message = "❌ No account found with that email or username.";
        }

        $stmt->close();
    } else {
        $message = "❌ Please enter your email or username.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="Style.css">
</head>

<body>

<h1 style="text-align:center;">Forgot Password</h1>

<form method="POST" style="text-align:center;">

<input type="text" name="identifier" placeholder="Enter Email or Username" required>

<br><br>

<button type="submit">Submit</button>

</form>

<p style="text-align:center;color:red;">
<?php echo $message; ?>
</p>

<p style="text-align:center;">
<a href="signin.html">Back to Login</a>
</p>

</body>
</html>