<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PII Redactor</title>
  </head>
  <body>
    <h1>PII Redactor</h1>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        // Process the uploaded file
        $file = $_FILES['file'];
        $upload_directory = 'uploads/';
        $upload_path = $upload_directory . basename($file['name']);
        
        // Ensure the upload directory exists
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        // Move uploaded file to the destination folder
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Get blur options
            $blur_aadhaar = isset($_POST['blur_aadhaar']) ? 1 : 0;
            $blur_name = isset($_POST['blur_name']) ? 1 : 0;
            $blur_dob = isset($_POST['blur_dob']) ? 1 : 0;

            // Prepare the Python command
            $command = "bash -c 'source myenv/bin/activate && python3 main.py " . escapeshellarg($upload_path) . " " . intval($blur_aadhaar) . " " . intval($blur_name) . " " . intval($blur_dob) . "'";

            // Execute the Python script
            $output = shell_exec($command . " 2>&1");

            // Debug: Check the Python script output
            // echo "<pre>Python script output: $output</pre>";

            // Extract the file path from the output (improved regex for capturing file paths)
            $output_file = null;
            if (preg_match('/Processed (image|text) saved as:\s*(.+\.docx|.+\.pdf|.+\.png|.+\.jpg|.+\.jpeg)/', $output, $matches)) {
                $output_file = trim($matches[2]);  // Get the file path from the regex match
                // echo "<pre>Extracted file path: $output_file</pre>"; // Debugging line
            }

            // Verify if the processed file exists before sending
            if ($output_file && file_exists($output_file)) {
                // Store the output file path and provide the download button
                echo '<form method="GET" action="download.php">';
                echo '<input type="hidden" name="file" value="' . htmlspecialchars($output_file) . '">';
                echo '<button type="submit">Download File</button>';
                echo '</form>';
            } else {
                // Debugging line for file existence issue
                echo "<pre>File does not exist at: $output_file</pre>";
                echo "Error: Processed file not found. <br>";
                echo "Check if the Python script generated a file.<br>";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
    ?>

    <!-- Only show the form when no file has been uploaded yet -->
    <form action="" method="POST" enctype="multipart/form-data">
      <input
        type="file"
        name="file"
        id="file-input"
        onchange="onFileChange()"
        required
      />
      <div id="checkbox-container" style="display: none">
        <label>
          <input type="checkbox" name="blur_aadhaar" id="blur_aadhaar" /> Blur
          Aadhaar Number
        </label><br />
        <label>
          <input type="checkbox" name="blur_name" id="blur_name" /> Blur Name
        </label><br />
        <label>
          <input type="checkbox" name="blur_dob" id="blur_dob" /> Blur DOB
        </label>
      </div>
      <button type="submit">Upload File</button>
    </form>

    <script>
      function onFileChange() {
        const fileInput = document.getElementById("file-input");
        const file = fileInput.files[0];
        const checkboxContainer = document.getElementById("checkbox-container");

        if (file && (file.type === "image/png" || file.type === "image/jpeg")) {
          checkboxContainer.style.display = "block";
        } else {
          checkboxContainer.style.display = "none";
        }
      }
    </script>

    <?php
    }
    ?>
  </body>
</html>
