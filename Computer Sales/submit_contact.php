<?php
$servername = "localhost";
$username = "root"; // Update if needed
$password = ""; // Update if needed
$dbname = "computer_store"; // Use the same database as before

// Connect to MySQL
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database explicitly
$conn->select_db($dbname);

// Create table if it doesn't exist
$tableQuery = "CREATE TABLE IF NOT EXISTS customer_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    computer_brand VARCHAR(50) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($tableQuery) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Initialize variables
$name = $email = $phone = $computer_brand = "";

// Insert data if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['computer'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $computer_brand = $conn->real_escape_string($_POST['computer']);

    $insertQuery = "INSERT INTO customer_inquiries (name, email, phone, computer_brand) 
                    VALUES ('$name', '$email', '$phone', '$computer_brand')";

    if ($conn->query($insertQuery) === TRUE) {
        $last_id = $conn->insert_id;
    } else {
        die("Error inserting data: " . $conn->error);
    }
}

// Fetch last inserted record only if a submission has occurred
$lastRecord = null;
if (!empty($name)) {
    $lastRecordQuery = "SELECT * FROM customer_inquiries ORDER BY id DESC LIMIT 1";
    $result = $conn->query($lastRecordQuery);
    $lastRecord = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f4f4f4; color: #333; }
        .navbar { background-color: #333; overflow: hidden; display: flex; justify-content: center; padding: 15px 0; }
        .navbar a { color: white; text-decoration: none; padding: 14px 20px; display: block; }
        .navbar a:hover { background-color: #575757; }
        .container { width: 50%; margin: 30px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px; text-align: center; }
        h1 { color: #0056b3; margin-bottom: 15px; }
        p { margin-bottom: 10px; font-size: 18px; }
        .footer { background-color: #333; color: white; text-align: center; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.html">Home</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact Us</a>
    </div>

    <div class="container">
        <?php if ($lastRecord): ?>
            <h1>Thank You for Your Inquiry!</h1>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($lastRecord['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($lastRecord['email']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($lastRecord['phone']); ?></p>
            <p><strong>Computer Brand:</strong> <?php echo htmlspecialchars($lastRecord['computer_brand']); ?></p>
            <p><strong>Submitted At:</strong> <?php echo htmlspecialchars($lastRecord['submitted_at']); ?></p>
        <?php else: ?>
            <h1>No recent submissions found.</h1>
            <p>Please fill out the form on the contact page.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2025 Info IT Solutions. All Rights Reserved.</p>
    </div>

</body>
</html>

<?php $conn->close(); ?>
