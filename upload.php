<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $upload_directory = 'uploads/';
        $upload_path = $upload_directory . basename($file['name']);

        // Move uploaded file to the destination folder
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Call the Python script to process the file
            // Call the Python script to process the file
$command = escapeshellcmd("python3 main.py " . escapeshellarg($upload_path) . " 2>&1");
$output = shell_exec($command);

// Log or display the output for debugging
if ($output === null) {
    echo "Python script did not produce any output.<br>";
} else {
    echo "<pre>$output</pre>";  // Display the raw output from Python
}

            // Check if the output contains the processed file path
            $output_file = trim($output);  // Trim whitespace/newlines

            // Verify if the processed file exists before sending
            if (file_exists($output_file)) {
                // Send the processed image back to the user for download
                header('Content-Type: image/jpeg');  // Assuming JPEG output
                header('Content-Disposition: attachment; filename="' . basename($output_file) . '"');
                readfile($output_file);  // Send the processed file to the user
            } else {
                echo "Error: Processed file not found.";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded.";
    }
} else {
    echo "Invalid request method.";
}
?>
