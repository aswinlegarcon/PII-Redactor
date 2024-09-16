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

function downloadSampleAadhar() {
  const link = document.createElement("a");
  link.href = "files/sampaadhar.jpg"; // Add the path to the sample Aadhar file
  link.download = "SampleAadharcard.jpg";
  link.click();
}

function downloadSampleDoc() {
  const link = document.createElement("a");
  link.href = "files/sample_pii_document.docx"; // Add the path to the sample document
  link.download = "SamplePIIDocument.docx";
  link.click();
}
