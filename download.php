<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // Check if the file exists
    if (file_exists($file)) {
        // Get the file extension and set the appropriate content type
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($file_extension) {
            case 'pdf':
                $content_type = 'application/pdf';
                break;
            case 'docx':
                $content_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'jpg':
            case 'jpeg':
                $content_type = 'image/jpeg';
                break;
            case 'png':
                $content_type = 'image/png';
                break;
            default:
                $content_type = 'application/octet-stream';
                break;
        }

        // Set headers for file download
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}
?>
