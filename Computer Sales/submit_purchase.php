<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if your MySQL has a password
$dbname = "computer_store";

// Connect to MySQL Server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Check if Database Exists, Create if Not
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Step 2: Check if Table Exists, Create if Not
$tableQuery = "CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    computer_model VARCHAR(100) NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($tableQuery) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Step 3: Insert Data into the Table
$last_inserted_id = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $conn->real_escape_string($_POST["customer_name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $computer_model = $conn->real_escape_string($_POST["computer_model"]);

    $insertQuery = "INSERT INTO purchases (customer_name, email, phone, computer_model) 
                    VALUES ('$customer_name', '$email', '$phone', '$computer_model')";

    if ($conn->query($insertQuery) === TRUE) {
        $last_inserted_id = $conn->insert_id;
    } else {
        die("Error: " . $conn->error);
    }
}

// Step 4: Retrieve and Display Last Inserted Record
$last_purchase = null;
if ($last_inserted_id) {
    $result = $conn->query("SELECT * FROM purchases WHERE id = $last_inserted_id");
    if ($result->num_rows > 0) {
        $last_purchase = $result->fetch_assoc();
    }
}

// Close Connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
            text-align: center;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center;
            padding: 15px 0;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: block;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            color: #0056b3;
            margin-bottom: 10px;
        }
        p {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact Us</a>
    </div>

    <!-- Purchase Confirmation -->
    <div class="container">
        <?php if ($last_purchase): ?>
            <h2>Order Submitted Successfully</h2>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($last_purchase["customer_name"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($last_purchase["email"]); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($last_purchase["phone"]); ?></p>
            <p><strong>Computer Model:</strong> <?php echo htmlspecialchars($last_purchase["computer_model"]); ?></p>
            <p><strong>Purchase Date:</strong> <?php echo $last_purchase["purchase_date"]; ?></p>
        <?php else: ?>
            <h2>Error</h2>
            <p>Unable to retrieve purchase details.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 Info IT Solutions. All Rights Reserved.</p>
    </div>

</body>
</html>
