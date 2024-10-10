const DEV_MODE = false;

const DEV_IMAGE_URL =
  "https://img.freepik.com/free-photo/modern-empty-room_23-2150528594.jpg?t=st=1728568656~exp=1728572256~hmac=6ae19b7cfd5cb1a17338bf827123b29825dae441548162a1b0bf85ff47d97514&w=1800";

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
    for (const eventName of ["dragenter", "dragover", "dragleave", "drop"]) {
      this.dropZone.addEventListener(eventName, this.preventDefaults, false);
    }
    this.dropZone.addEventListener("drop", (e) => this.handleDrop(e));
  }

  preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  handleDrop(e) {
    const file = e.dataTransfer.files[0];
    if (file) {
      this.fileInput.files = e.dataTransfer.files; // Set the files to the input
      this.displayImagePreview(file);
    }
  }
}

class ProcessButton {
  constructor(buttonId) {
    this.button = document.getElementById(buttonId);
    this.fileInput =
      document.getElementById("file-input") ||
      document.querySelector('input[type="file"]');
    if (!this.button) {
      console.error("Process button not found");
    }
    if (!this.fileInput) {
      console.error("File input not found");
    }
  }

  initialize() {
    if (this.button) {
      this.button.addEventListener("click", () => this.processPhoto());
    }
  }

  processPhoto() {
    const removeFurniture =
      document.querySelector('input[type="checkbox"][class*="ml-auto"]')
        ?.checked || false;
    const addFurniture =
      document.getElementById("add-furniture-checkbox")?.checked || false;
    const roomType = document.getElementById("room-type")?.value;
    const furnitureStyle = document.getElementById("furniture-style")?.value;

    if (!roomType || !furnitureStyle) {
      console.error("Room type or furniture style not selected");
      return;
    }

    console.log("Remove furniture:", removeFurniture);
    console.log("Add furniture:", addFurniture);
    console.log("Room type:", roomType);
    console.log("Furniture style:", furnitureStyle);

    if (DEV_MODE) {
      console.log("Using development image URL");
      this.createRender(DEV_IMAGE_URL, roomType, furnitureStyle);
      return;
    }

    if (!this.fileInput || !this.fileInput.files.length) {
      console.error("No file selected");
      return;
    }

    const file = this.fileInput.files[0];
    const formData = new FormData();
    formData.append("image", file);

    fetch(`${vsaiApiSettings.root}upload-image`, {
      method: "POST",
      body: formData,
      headers: {
        "X-WP-Nonce": vsaiApiSettings.nonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.url) {
          console.log("Uploaded image URL:", data.url);
          this.createRender(data.url, roomType, furnitureStyle);
        } else {
          console.error("Error: No image URL received");
        }
      })
      .catch((error) => {
        console.error("Error uploading image:", error);
      });
  }

  createRender(imageUrl, roomType, style) {
    const renderData = {
      image_url: imageUrl,
      room_type: roomType,
      style: style,
      wait_for_completion: false,
    };

    console.log("Render payload:", JSON.stringify(renderData));

    fetch(`${vsaiApiSettings.root}render/create`, {
      method: "POST",
      body: JSON.stringify(renderData),
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": vsaiApiSettings.nonce,
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data.render_id) {
          console.log("Render ID:", data.render_id);
          window.location.href = `/virtual-staging-main?render_id=${data.render_id}`;
        } else {
          console.error("Error: No render ID received", data);
        }
      })
      .catch((error) => {
        console.error("Error creating render:", error);
      });
  }

  static enable() {
    const button = document.getElementById("process-button");
    if (button) {
      button.disabled = false;
      button.classList.remove("cursor-not-allowed", "opacity-50");
      button.classList.add("cursor-pointer", "hover:bg-primary-dark");
    }
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
