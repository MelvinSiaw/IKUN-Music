<?php
session_start();

// Include your database connection script
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path as necessary

// Initialize variables
$search_results = '';

// Process search query if form submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search_query = isset($_GET['query']) ? $_GET['query'] : '';

    if (!empty($search_query)) {
        // Prepare and bind
        $stmt = $conn->prepare("SELECT Songs.id, Songs.song_title, Songs.profile_picture_upload, Artist.artist_name FROM Songs LEFT JOIN Artist ON Songs.artist_id = Artist.artist_id WHERE Songs.song_title LIKE ?");
        if ($stmt === false) {
            die('MySQL prepare error: ' . htmlspecialchars($conn->error));
        }
        $search_term = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_term);

        // Execute the statement
        if (!$stmt->execute()) {
            die('MySQL execute error: ' . htmlspecialchars($stmt->error));
        }

        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Build search results
            $search_results .= '<div class="content-section">';
            $search_results .= '<div class="section-header">Search Results</div>';
            $search_results .= '<div class="songs scroll-container">';
            while($row = $result->fetch_assoc()) {
                $search_results .= '<div class="song-card">';
                $search_results .= '<img src="' . htmlspecialchars($row['profile_picture_upload']) . '" alt="Song Image">';
                $search_results .= '<div class="song">';
                $search_results .= '<span>' . htmlspecialchars($row['song_title']) . '</span>';
                $search_results .= '<span>Artist: ' . htmlspecialchars($row['artist_name']) . '</span>';
                $search_results .= '<a href="song_page.php?id=' . urlencode($row['id']) . '" class="action-link">Listen</a>';
                $search_results .= '</div></div>';
            }
            $search_results .= '</div></div>';
        } else {
            $search_results = '<div class="content-section"><div class="section-header">Search Results</div><p>No results found.</p></div>';
        }

        // Close statement
        $stmt->close();
    } else {
        $search_results = '<div class="content-section"><div class="section-header">Search Results</div><p>Please enter a search query.</p></div>';
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/pic/background.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .content-section {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            width: 80%;
            max-width: 800px;
            text-align: center; 
        }
        .section-header {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .songs {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center; 
        }
        .song-card {
            width: calc(33.33% - 20px);
            background-color: #f0f0f0;
            padding: 1px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .song-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .song {
            padding: 10px;
        }
        .song span {
            display: block;
            margin-bottom: 5px;
        }
        .action-link {
            display: inline-block;
            padding: 8px 20px;
            margin-top: 5px;
            font-size: 14px;
            color: #fff;
            background-color: #007BFF; 
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .action-link:hover {
            background-color: #0056b3;
        }
        .bottom-right {
        position: fixed;
        bottom: 20px;
        right: 20px;
        text-align: center;
        }
    </style>
</head>
<body>
    <h1>Search Songs</h1>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div style="display: flex; justify-content: center;">
        <input type="text" name="query" placeholder="Search for songs..." style="padding: 10px; width: 550px; border: 1px solid #ccc; border-radius: 4px 0 0 4px;">
        <button type="submit" style="background-color: #007BFF; color: white; border: 1px solid #007BFF; border-radius: 0 4px 4px 0; padding: 10px 20px; cursor: pointer;">Search</button>
    </div>
    </form>
    
    <?php echo $search_results; ?>

    <div class="bottom-right">
    <a href="User_Home.php" class="action-link">Go back to Home</a>
    </div>
</body>
</html>
