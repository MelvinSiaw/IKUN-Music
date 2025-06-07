<?php
if (isset($_GET['path'])) {
    // Decode and sanitize the path
    $path = urldecode($_GET['path']);

    // Define allowed directories
    $allowedDirectories = [
        realpath(__DIR__ . '/uploads/mp3'),
        realpath(__DIR__ . '/uploads/profile'),
        realpath(__DIR__ . '/uploads/background')
    ];

    // Function to check if path is in allowed directories
    function isAllowedPath($path, $allowedDirectories) {
        foreach ($allowedDirectories as $dir) {
            if (strpos(realpath($path), $dir) === 0) {
                return true;
            }
        }
        return false;
    }

    // Check if the path is allowed and if the file exists
    if (isAllowedPath($path, $allowedDirectories) && file_exists($path)) {
        // Get the MIME type of the file
        $mimeType = mime_content_type($path);

        // Set the appropriate Content-Type header
        header('Content-Type: ' . $mimeType);

        // Output the file content
        readfile($path);
        exit;
    } else {
        // Output debug information or error message
        http_response_code(404);
        echo 'File not found or access denied.';
    }
} else {
    // Output error if no file path is specified
    http_response_code(400);
    echo 'No file specified';
}
?>
