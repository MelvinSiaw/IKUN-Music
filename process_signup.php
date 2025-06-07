<?php
require_once 'db_connection.php';

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate and sanitize form inputs
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    // Validate inputs (example checks, adjust as per your requirements)
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required");
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into database
    $sql = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("SQL prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $name, $email, $password_hash);
    
    if ($stmt->execute()) {
        // Success: Send response back to JavaScript indicating success
        echo "User registered successfully";
    } else {
        // Check for duplicate email error (1062 error code for MySQL)
        if ($conn->errno === 1062) {
            echo "Email address already taken";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    
    $stmt->close();
} else {
    // Handle cases where the script is accessed directly without POST data
    die("Invalid request");
}

$conn->close(); // Close the database connection
?>
