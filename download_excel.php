<?php
$conn = new mysqli("localhost", "root", "", "business_register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=job_entries.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "First Name\tSir Name\tUsername\tEmail\tPhone\tJob Date\tJob Time\n";

$sql = "SELECT 
            users.firstname, 
            users.sirname, 
            users.username, 
            users.email, 
            users.phone, 
            job_entries.job_date, 
            job_entries.job_time 
        FROM job_entries 
        JOIN users ON job_entries.user_id = users.id 
        ORDER BY job_entries.job_date DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['firstname']}\t{$row['sirname']}\t{$row['username']}\t{$row['email']}\t{$row['phone']}\t{$row['job_date']}\t{$row['job_time']}\n";
    }
} else {
    echo "No data available\n";
}

$conn->close();
exit;
?>