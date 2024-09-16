<?php
session_start(); // Start session to manage file download visibility

// Clear session file data when user clicks "Check Again"
if (isset($_POST['check_again'])) {
    unset($_SESSION['output_file']);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PII Redactor</title>
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Protest+Guerrilla&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet" />
    <style>
      .hidden { display: none; }
    </style>
  </head>
  <body>
    <!-- Logo Section (Centered at the top) -->
    <div class="logo-container">
      <img src="photos/kprlogo.png" alt="Logo 1" />
      <img src="photos/Picture1.png" alt="Logo 2" />
    </div>

    <div class="content">
      <!-- Left Section for Text and Buttons -->
      <div class="left-section">
        <h1>Hello..! We are <br /><span>Secure Hashers</span></h1>
        <div class="button-group">
          <button onclick="downloadSampleAadhar()">Download Sample Aadhar (.jpg)</button>
          <button onclick="downloadSampleDoc()">Download Sample Document (.docx)</button>
        </div>
        <h2>Download and Use these files to check our prototype</h2>
      </div>

      <!-- Right Section for PII Redactor -->
      <div class="right-section">
        <?php if (!isset($_SESSION['output_file'])): ?>
          <!-- File Upload Section -->
          <div class ="upload-section" id="upload-section">
            <h2>PII Redactor</h2>
            <p style="color: rgb(214, 6, 6)">Note: This is a prototype. Only for checking sample Aadhar card(eg: .jpg,.png) and PII contained documents (eg: .docx,.pdf) .</p>

            <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
              <input type="file" name="file" id="file-input" onchange="onFileChange()" required /><br>

              <!-- Checkbox options for blurring -->
              <div id="checkbox-container" style="display: none;">
                <label>
                  <input type="checkbox" name="blur_aadhaar" /> Blur Aadhaar Number
                </label><br />
                <label>
                  <input type="checkbox" name="blur_name" /> Blur Name
                </label><br />
                <label>
                  <input type="checkbox" name="blur_dob" /> Blur DOB
                </label>
              </div>

              <button type="submit">Upload File</button>
            </form>
          </div>
        <?php else: ?>
          <!-- Download Section -->
          <div class="download-section" id="download-section">
          <h2>PII Redactor</h2>
          <p style="color: rgb(214, 6, 6)">Note: This is a prototype. Only for checking sample Aadhar card and PDF.</p>
            <form method="GET" action="download.php">
              <input type="hidden" name="file" value="<?php echo htmlspecialchars($_SESSION['output_file']); ?>">
              <button type="submit">Download File</button>
            </form>

            <!-- Check Again Button -->
            <form method="POST" action="">
              <button type="submit" name="check_again">Check Again</button>
            </form>
          </div>
        <?php endif; ?>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            // File processing logic
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

                // Extract the file path from the output
                if (preg_match('/Processed (image|text) saved as:\s*(.+\.docx|.+\.pdf|.+\.png|.+\.jpg|.+\.jpeg)/', $output, $matches)) {
                    $output_file = trim($matches[2]);  // Get the full path to the processed file
                    $_SESSION['output_file'] = realpath($output_file);  // Store it in the session to access on reload
                    header("Location: index.php"); // Refresh the page to display the download button
                    exit;
                } else {
                    echo "Error: Processed file not found. Check if the Python script generated a file.<br>";
                }
            } else {
                echo "Error uploading file.";
            }
        }
        ?>
      </div>
    </div>

    <script src="index.js"></script>
  </body>
</html>
