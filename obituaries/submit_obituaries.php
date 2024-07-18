<?php
// Database connection details (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obituary_platform";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize inputs
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $name = sanitizeInput($_POST["name"]);
    $dob = sanitizeInput($_POST["date_of_birth"]);
    $dod = sanitizeInput($_POST["date_of_death"]);
    $content = sanitizeInput($_POST["content"]);
    $author = sanitizeInput($_POST["author"]);

    // Validate form data
    if (empty($name) || empty($dob) || empty($dod) || empty($content) || empty($author)) {
        echo "All fields are required.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO obituaries (name, date_of_birth, date_of_death, content, author) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $dob, $dod, $content, $author);

        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $stmt->close();

            // Redirect to view_obituaries.php with the obituary ID
            header("Location: view_obituaries.php?id=" . $insert_id);
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

$conn->close();

