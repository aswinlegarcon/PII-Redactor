

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PII Redactor</title>
  </head>
  <body>
    <h1>PII Redactor</h1>
    <form action="uploadtest.php" method="POST" enctype="multipart/form-data">
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
          Aadhaar Number </label
        ><br />
        <label>
          <input type="checkbox" name="blur_name" id="blur_name" /> Blur Name </label
        ><br />
        <label>
          <input type="checkbox" name="blur_dob" id="blur_dob" /> Blur DOB
        </label>
      </div>
      <button type="submit">Upload File</button>
    </form>
    <a href="processed_output.docx" download="image-name" target="_blank" class="download-button">
       <button type="submit">Download File</button></a>
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
  </body>
</html>
