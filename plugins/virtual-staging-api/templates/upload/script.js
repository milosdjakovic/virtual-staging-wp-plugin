document.addEventListener("DOMContentLoaded", initializeApp);

function initializeApp() {
  const dropZone = new DropZone("drop-zone", "file-input");
  const processButton = new ProcessButton("process-button");
  const furnitureSelector = new FurnitureSelector(
    "add-furniture-checkbox",
    "furniture-options"
  );

  dropZone.initialize();
  processButton.initialize();
  furnitureSelector.initialize();
}

class DropZone {
  constructor(dropZoneId, fileInputId) {
    this.dropZone = document.getElementById(dropZoneId);
    this.fileInput = document.getElementById(fileInputId);
  }

  initialize() {
    this.dropZone.addEventListener("click", () => this.fileInput.click());
    this.fileInput.addEventListener("change", (event) =>
      this.handleFileSelection(event)
    );
    this.setupDragAndDrop();
  }

  handleFileSelection(event) {
    const file = event.target.files[0];
    if (file) {
      this.displayImagePreview(file);
    }
  }

  displayImagePreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = document.createElement("img");
      img.src = e.target.result;
      img.style.maxWidth = "100%";
      img.style.maxHeight = "100%";
      this.dropZone.innerHTML = "";
      this.dropZone.appendChild(img);
      ProcessButton.enable();
    };
    reader.readAsDataURL(file);
  }

  setupDragAndDrop() {
    ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
      this.dropZone.addEventListener(eventName, this.preventDefaults, false);
    });
    this.dropZone.addEventListener("drop", (e) => this.handleDrop(e));
  }

  preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  handleDrop(e) {
    const file = e.dataTransfer.files[0];
    this.displayImagePreview(file);
  }
}

class ProcessButton {
  constructor(buttonId) {
    this.button = document.getElementById(buttonId);
  }

  initialize() {
    this.button.addEventListener("click", () => this.processPhoto());
  }

  processPhoto() {
    const removeFurniture = document.querySelector(
      'input[type="checkbox"][class*="ml-auto"]'
    ).checked;
    const addFurniture = document.getElementById(
      "add-furniture-checkbox"
    ).checked;
    const roomType = document.getElementById("room-type").value;
    const furnitureStyle = document.getElementById("furniture-style").value;

    console.log("Processing photo...");
    console.log("Remove furniture:", removeFurniture);
    console.log("Add furniture:", addFurniture);
    console.log("Room type:", roomType);
    console.log("Furniture style:", furnitureStyle);

    // Add your processing logic here
  }

  static enable() {
    const button = document.getElementById("process-button");
    button.disabled = false;
    button.classList.remove("cursor-not-allowed", "opacity-50");
    button.classList.add("cursor-pointer", "hover:bg-primary-dark");
  }
}

class FurnitureSelector {
  constructor(checkboxId, optionsId) {
    this.checkbox = document.getElementById(checkboxId);
    this.options = document.getElementById(optionsId);
  }

  initialize() {
    this.checkbox.addEventListener("change", () => this.toggleOptions());
  }

  toggleOptions() {
    if (this.checkbox.checked) {
      this.options.classList.remove("hidden");
    } else {
      this.options.classList.add("hidden");
    }
  }
}
