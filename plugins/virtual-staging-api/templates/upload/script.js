const DEV_MODE = false;

const DEV_IMAGE_URL =
  "https://img.freepik.com/free-photo/modern-empty-room_23-2150528563.jpg?t=st=1728732147~exp=1728735747~hmac=014440306239606372948ce56acb6e5625ba052fef0338a2a299b569549eef93&w=1800";

class StatusMessage {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.iconElement = this.container.querySelector("#token-status-icon");
    this.textElement = this.container.querySelector("#token-status-text");
  }

  display(type, message) {
    this.container.className =
      "rounded-xl border p-4 transition-all duration-100 flex items-center";

    const styles = {
      info: {
        bg: "#E0F2FE",
        border: "#38BDF8",
        color: "#0369A1",
      },
      success: {
        bg: "#DCFCE7",
        border: "#4ADE80",
        color: "#166534",
      },
      error: {
        bg: "#FEE2E2",
        border: "#F87171",
        color: "#B91C1C",
      },
      warning: {
        bg: "#FEF3C7",
        border: "#FBBF24",
        color: "#B45309",
      },
    };

    const style = styles[type] || styles.info;

    this.container.style.backgroundColor = style.bg;
    this.container.style.borderColor = style.border;
    this.container.style.color = style.color;

    this.iconElement.style.color = "currentColor";
    this.textElement.textContent = message;

    this.container.classList.remove("hidden");
  }

  hide() {
    this.container.classList.add("hidden");
  }
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
      statusMessage.display(
        "error",
        "Token error: Access token is not present. Please check your access link."
      );
      return;
    }

    const roomType = document.getElementById("room-type")?.value;
    const furnitureStyle = document.getElementById("furniture-style")?.value;

    if (!roomType || !furnitureStyle) {
      statusMessage.display(
        "error",
        "Please select both room type and furniture style."
      );
      return;
    }

    if (!this.fileInput || !this.fileInput.files.length) {
      statusMessage.display("error", "Please select an image to upload.");
      return;
    }

    // Disable the button
    this.disableButton();

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
          this.handleError(
            "upload_error",
            "We couldn't upload your image at this time."
          );
          this.enableButton();
        }
      })
      .catch((error) => {
        console.error("Error uploading image:", error);
        this.handleError(
          "upload_error",
          "We encountered an issue while uploading your image."
        );
        this.enableButton();
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
        } else {
          this.handleError(
            "render_error",
            "We couldn't process your image at this moment."
          );
          this.enableButton();
        }
      })
      .catch((error) => {
        console.error("Error creating render:", error);
        this.handleError(
          "render_error",
          "We encountered an issue while processing your image."
        );
        this.enableButton();
      });
  }

  handleError(code, defaultMessage) {
    console.error(`Error code: ${code}, Message: ${defaultMessage}`);

    const errorMessages = {
      invalid_token:
        "Your session has expired. Please refresh the page and try again.",
      missing_token:
        "There's an issue with your access. Please try reloading the page.",
      limit_breached:
        "You've reached the maximum number of uploads for now. Please try again later or contact support for more information.",
      missing_image: "Please select an image before proceeding.",
      upload_error:
        "We couldn't upload your image at this time. Please try again or use a different image.",
      render_error:
        "We're having trouble processing your image right now. Please try again in a few moments.",
    };

    const message = errorMessages[code] || defaultMessage;
    const fullMessage = `${message} If this problem persists, please contact our support team.`;

    statusMessage.display("error", fullMessage);
  }

  disableButton() {
    if (this.button) {
      this.button.disabled = true;
      this.button.classList.add("cursor-not-allowed", "opacity-50");
      this.button.classList.remove("cursor-pointer", "hover:bg-primary-dark");
    }
  }

  enableButton() {
    if (this.button) {
      this.button.disabled = false;
      this.button.classList.remove("cursor-not-allowed", "opacity-50");
      this.button.classList.add("cursor-pointer", "hover:bg-primary-dark");
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
    statusMessage.display(
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
        statusMessage.display(
          "error",
          "Your access token is no longer valid. Please request a new one."
        );
      } else if (data.renders_left <= 0) {
        statusMessage.display("warning", "You have reached your upload limit.");
      } else {
        statusMessage.display(
          "info",
          `You have ${data.renders_left} upload${
            data.renders_left !== 1 ? "s" : ""
          } remaining out of ${data.limit}.`
        );
      }
    })
    .catch((error) => {
      console.error("Error checking token status:", error);
      statusMessage.display(
        "error",
        "An error occurred while checking your token status. Please try again later."
      );
    });
}

const statusMessage = new StatusMessage("token-status-message");

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

  // Display initial message
  statusMessage.display("info", "Please upload an image to begin.");
}

document.addEventListener("DOMContentLoaded", initializeApp);
