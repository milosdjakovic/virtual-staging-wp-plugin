document.addEventListener("DOMContentLoaded", () => {
  const dropZone = document.getElementById("drop-zone");
  const fileInput = document.getElementById("file-input");
  const processButton = document.getElementById("process-button");

  dropZone.addEventListener("click", () => {
    fileInput.click(); // Trigger the file input click
  });

  // Add change event listener to the file input
  fileInput.addEventListener("change", (event) => {
    const file = event.target.files[0]; // Get the selected file
    if (file) {
      handleFile(file);
    }
  });

  // Function to handle the selected file
  function handleFile(file) {
    console.log("Selected file:", file.name);

    // Display image preview
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = document.createElement("img");
      img.src = e.target.result;
      img.style.maxWidth = "100%";
      img.style.maxHeight = "100%";
      dropZone.innerHTML = ""; // Clear existing content
      dropZone.appendChild(img);

      // Enable the process button
      enableProcessButton();
    };
    reader.readAsDataURL(file);
  }

  // Function to enable the process button
  function enableProcessButton() {
    processButton.disabled = false;
    processButton.classList.remove("cursor-not-allowed", "opacity-50");
    processButton.classList.add("cursor-pointer", "hover:bg-primary-dark");
  }

  // Prevent default drag behaviors
  for (const eventName of ["dragenter", "dragover", "dragleave", "drop"]) {
    dropZone.addEventListener(eventName, preventDefaults, false);
  }

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  // Handle drop
  dropZone.addEventListener("drop", (e) => {
    const dt = e.dataTransfer;
    const file = dt.files[0];
    handleFile(file);
  });

  // Add click event listener to the process button
  processButton.addEventListener("click", () => {
    // Add your processing logic here
    console.log("Processing photo...");
  });

  initializeFurnitureSelection();
});

function initializeFurnitureSelection() {
  const addFurnitureCheckbox = document.getElementById(
    "add-furniture-checkbox"
  );
  const furnitureOptions = document.getElementById("furniture-options");

  function toggleFurnitureOptions() {
    if (addFurnitureCheckbox.checked) {
      furnitureOptions.classList.remove("hidden");
    } else {
      furnitureOptions.classList.add("hidden");
    }
  }

  addFurnitureCheckbox.addEventListener("change", toggleFurnitureOptions);
}
