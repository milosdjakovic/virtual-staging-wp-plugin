const DEV_MODE = false;

const DEV_IMAGE_URL =
  "https://static.vecteezy.com/system/resources/previews/005/727/726/non_2x/minimalist-empty-room-with-gray-wall-and-wood-floor-3d-rendering-free-photo.jpg";

document.addEventListener("DOMContentLoaded", initializeApp);

function initializeApp() {
  checkTokenStatus();
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

function getUrlParameter(name) {
  const paramName = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  const regex = new RegExp(`[\\?&]${paramName}=([^&#]*)`);
  const results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function checkTokenStatus() {
  const token = getUrlParameter("at");
  if (!token) {
    displayTokenStatus(
      "error",
      "No access token provided. Please check your access link."
    );
    return;
  }

  fetch(`${vsaiApiSettings.root}token-status?at=${token}`, {
    method: "GET",
    headers: {
      "X-WP-Nonce": vsaiApiSettings.nonce,
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.code === "invalid_token") {
        displayTokenStatus(
          "error",
          "Your access token is no longer valid. Please request a new one."
        );
      } else if (data.renders_left <= 0) {
        displayTokenStatus(
          "warning",
          "You have reached your render limit. No more renders are available."
        );
      } else {
        displayTokenStatus(
          "info",
          `You have ${data.renders_left} render${
            data.renders_left !== 1 ? "s" : ""
          } remaining.`
        );
      }
    })
    .catch((error) => {
      console.error("Error checking token status:", error);
      displayTokenStatus(
        "error",
        "An error occurred while checking your token status. Please try again later."
      );
    });
}

function displayTokenStatus(severity, message) {
  const statusElement = document.getElementById("token-status-message");
  const iconElement = document.getElementById("token-status-icon");
  const textElement = document.getElementById("token-status-text");

  // Reset classes
  statusElement.className = "rounded-xl border p-4 transition-all duration-100";
  statusElement.style.display = "flex";
  statusElement.style.alignItems = "center";

  // Set color and background based on severity
  switch (severity) {
    case "error":
      statusElement.style.backgroundColor = "#FEE2E2";
      statusElement.style.borderColor = "#F87171";
      statusElement.style.color = "#B91C1C";
      break;
    case "warning":
      statusElement.style.backgroundColor = "#FEF3C7";
      statusElement.style.borderColor = "#FBBF24";
      statusElement.style.color = "#B45309";
      break;
    default:
      statusElement.style.backgroundColor = "#E0F2FE";
      statusElement.style.borderColor = "#38BDF8";
      statusElement.style.color = "#0369A1";
  }

  iconElement.style.color = "currentColor";
  textElement.textContent = message;

  statusElement.classList.remove("hidden");
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
    const token = getUrlParameter("at");
    if (!token) {
      alert(
        "Token error: Access token is not present. Please check your access link."
      );
      return;
    }

    const roomType = document.getElementById("room-type")?.value;
    const furnitureStyle = document.getElementById("furniture-style")?.value;

    if (!roomType || !furnitureStyle) {
      alert("Please select both room type and furniture style.");
      return;
    }

    if (!this.fileInput || !this.fileInput.files.length) {
      alert("Please select an image to upload.");
      return;
    }

    const file = this.fileInput.files[0];
    const formData = new FormData();
    formData.append("image", file);
    formData.append("token", token);

    this.uploadImage(formData, roomType, furnitureStyle, token);
  }

  uploadImage(formData, roomType, furnitureStyle, token) {
    fetch(`${vsaiApiSettings.root}upload-image`, {
      method: "POST",
      body: formData,
      headers: {
        "X-WP-Nonce": vsaiApiSettings.nonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.url) {
          console.log("Uploaded image URL:", data.url);
          const imageUrl = DEV_MODE ? DEV_IMAGE_URL : data.url;
          this.createRender(imageUrl, roomType, furnitureStyle, token);
        } else {
          this.handleError(data.code, data.message);
        }
      })
      .catch((error) => {
        console.error("Error uploading image:", error);
        alert(
          "An unexpected error occurred while uploading the image. Please try again."
        );
      });
  }

  createRender(imageUrl, roomType, style, token) {
    const renderData = {
      image_url: imageUrl,
      room_type: roomType,
      style: style,
      wait_for_completion: false,
      token: token,
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
      .then((response) => response.json())
      .then((data) => {
        if (data.render_id) {
          console.log("Render ID:", data.render_id);
          const finalImageUrl = DEV_MODE ? DEV_IMAGE_URL : imageUrl;
          const nextPageUrl =
            vsaiApiSettings.nextPageUrl || "/virtual-staging-main";
          window.location.href = `${nextPageUrl}?render_id=${
            data.render_id
          }&image_url=${encodeURIComponent(finalImageUrl)}&at=${token}`;
        } else if (data.code && data.message) {
          this.handleError(data.code, data.message);
        } else {
          throw new Error("Unexpected response format");
        }
      })
      .catch((error) => {
        console.error("Error creating render:", error);
        alert(
          "An unexpected error occurred while creating the render. Please try again."
        );
      });
  }

  handleError(code, message) {
    switch (code) {
      case "invalid_token":
      case "missing_token":
        alert(
          "Authentication error: Your access token is invalid or missing. Please request a new access link."
        );
        break;
      case "limit_breached":
        alert(
          "Upload limit reached: You have reached your maximum number of uploads. Please contact support for more information."
        );
        break;
      case "missing_image":
        alert("No image selected: Please select an image to upload.");
        break;
      case "upload_error":
        alert(
          `Error uploading image: ${message}. Please try again or contact support if the problem persists.`
        );
        break;
      default:
        alert(message || "An unexpected error occurred. Please try again.");
    }
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
