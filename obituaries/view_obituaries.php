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

// Function to generate title and description based on content
function generateMetaTags($content, $name) {
    $keywords = extractKeywords($content);
    $title = "Obituary of " . $name;
    $description = "Read the obituary of " . $name . ". " . substr(strip_tags($content), 0, 150) . "...";
    return array("title" => $title, "description" => $description);
}

function extractKeywords($content) {
    // Simple keyword extraction logic
    $content = strtolower(strip_tags($content));
    $words = preg_split('/\s+/', $content);
    $stopwords = array("and", "or", "but", "if", "while", "to", "a", "the", "in", "of", "with", "for", "on", "at", "by");
    $keywords = array_diff($words, $stopwords);
    $keywords = array_count_values($keywords);
    arsort($keywords);
    return array_keys(array_slice($keywords, 0, 5));
}

// Check if obituary ID is provided
if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT name, date_of_birth, date_of_death, content, author FROM obituaries WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $dob, $dod, $content, $author);
    $stmt->fetch();
    $stmt->close();

    if ($name) {
        // Generate meta tags
        $metaTags = generateMetaTags($content, $name);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="description" content="<?php echo $metaTags['description']; ?>">
            <title><?php echo $metaTags['title']; ?></title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <div class="name-card">
                <h1><?php echo htmlspecialchars($name); ?></h1>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($dob); ?></p>
                <p><strong>Date of Death:</strong> <?php echo htmlspecialchars($dod); ?></p>
                <p><strong>Content:</strong> <?php echo nl2br(htmlspecialchars($content)); ?></p>
                <p><strong>Author:</strong> <?php echo htmlspecialchars($author); ?></p>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Obituary not found.";
    }
} else {
    echo "Invalid obituary ID.";
}

$conn->close();
?>

