// Constants
const DEV_MODE = false;
const DEV_IMAGE_URL =
  "https://img.freepik.com/free-photo/modern-empty-room_23-2150528563.jpg?t=st=1728732147~exp=1728735747~hmac=014440306239606372948ce56acb6e5625ba052fef0338a2a299b569549eef93&w=1800";

// Translation helper
const t = (key) => {
  if (!window.vsaiTranslations) {
    console.warn('Translations not loaded');
    return key;
  }
  
  const keys = key.split(".");
  let value = window.vsaiTranslations;

  for (const k of keys) {
    if (value && value[k]) {
      value = value[k];
    } else {
      console.warn(`Translation missing for key: ${key}`);
      return key;
    }
  }

  return value;
};

// Utility functions
const updateUrlParameter = (key, value) => {
  const url = new URL(window.location.href);
  url.searchParams.set(key, value);
  window.history.replaceState({}, "", url);
};

const getUrlParameter = (name) => {
  const url = new URL(window.location.href);
  return url.searchParams.get(name);
};

const getImageUrl = (url) => (DEV_MODE ? DEV_IMAGE_URL : url);

const ApiService = {
  async checkTokenStatus(token) {
    try {
      const response = await fetch(
        `${vsaiApiSettings.root}token-status?at=${token}`,
        {
          method: "GET",
          headers: {
            "X-WP-Nonce": vsaiApiSettings.nonce,
            "Content-Type": "application/json",
          },
        }
      );
      return await response.json();
    } catch (error) {
      console.error("Error checking token status:", error);
      throw error;
    }
  },

  async uploadImage(formData) {
    try {
      const response = await fetch(`${vsaiApiSettings.root}upload-image`, {
        method: "POST",
        body: formData,
        headers: { "X-WP-Nonce": vsaiApiSettings.nonce },
      });
      return await response.json();
    } catch (error) {
      console.error("Error uploading image:", error);
      throw error;
    }
  },

  async createRender(renderData) {
    try {
      const response = await fetch(`${vsaiApiSettings.root}render/create`, {
        method: "POST",
        body: JSON.stringify(renderData),
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": vsaiApiSettings.nonce,
        },
      });
      return await response.json();
    } catch (error) {
      console.error("Error creating render:", error);
      throw error;
    }
  },
};

// UI Components
class StatusMessage {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.iconElement = this.container.querySelector("#token-status-icon");
    this.textElement = this.container.querySelector("#token-status-text");
  }

  display(type, message) {
    const styles = {
      info: { bg: "#E0F2FE", border: "#38BDF8", color: "#0369A1" },
      success: { bg: "#DCFCE7", border: "#4ADE80", color: "#166534" },
      error: { bg: "#FEE2E2", border: "#F87171", color: "#B91C1C" },
      warning: { bg: "#FEF3C7", border: "#FBBF24", color: "#B45309" },
    };

    const style = styles[type] || styles.info;

    this.container.className =
      "rounded-xl border p-4 transition-all duration-100 flex items-center";
    Object.assign(this.container.style, {
      backgroundColor: style.bg,
      borderColor: style.border,
      color: style.color,
    });

    this.iconElement.style.color = "currentColor";
    this.textElement.textContent = message;
    this.container.classList.remove("hidden");
  }

  hide() {
    this.container.classList.add("hidden");
  }
}

class DropZone {
  constructor(dropZoneId, fileInputId, onFileSelected) {
    this.dropZone = document.getElementById(dropZoneId);
    this.fileInput = document.getElementById(fileInputId);
    this.onFileSelected = onFileSelected;
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
      this.onFileSelected(file);
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
      this.fileInput.files = e.dataTransfer.files;
      this.displayImagePreview(file);
      this.onFileSelected(file);
    }
  }
}

class FurnitureSelector {
  constructor(checkboxId, optionsId, roomTypeId, furnitureStyleId) {
    this.checkbox = document.getElementById(checkboxId);
    this.options = document.getElementById(optionsId);
    this.roomType = document.getElementById(roomTypeId);
    this.furnitureStyle = document.getElementById(furnitureStyleId);
  }

  initialize() {
    this.checkbox.addEventListener("change", () => this.toggleOptions());
    this.roomType.addEventListener("change", () =>
      this.updateSelection("room", this.roomType.value)
    );
    this.furnitureStyle.addEventListener("change", () =>
      this.updateSelection("style", this.furnitureStyle.value)
    );

    const roomParam = getUrlParameter("room");
    const styleParam = getUrlParameter("style");
    if (roomParam) this.roomType.value = roomParam;
    if (styleParam) this.furnitureStyle.value = styleParam;
  }

  toggleOptions() {
    this.options.classList.toggle("hidden", !this.checkbox.checked);
  }

  updateSelection(type, value) {
    updateUrlParameter(type, value);
  }

  getSelections() {
    return {
      roomType: this.roomType.value,
      furnitureStyle: this.furnitureStyle.value,
    };
  }
}

// Main Application Logic
class App {
  constructor() {
    this.statusMessage = new StatusMessage("token-status-message");
    this.dropZone = new DropZone(
      "drop-zone",
      "file-input",
      this.handleFileSelected.bind(this)
    );
    this.furnitureSelector = new FurnitureSelector(
      "add-furniture-checkbox",
      "furniture-options",
      "room-type",
      "furniture-style"
    );
    this.processButton = document.getElementById("process-button");
    this.selectedFile = null;
  }

  async initialize() {
    this.dropZone.initialize();
    this.furnitureSelector.initialize();
    this.processButton.addEventListener("click", () => this.processPhoto());
    await this.checkTokenStatus();
  }

  async checkTokenStatus() {
    const token = getUrlParameter("at");
    if (!token) {
      this.statusMessage.display(
        "error",
        t("upload-form.token-error")
      );
      return;
    }

    try {
      const data = await ApiService.checkTokenStatus(token);
      if (data.code === "invalid_token") {
        this.statusMessage.display(
          "error",
          t("upload-form.invalid-token")
        );
      } else if (data.renders_left <= 0) {
        this.statusMessage.display(
          "warning",
          t("upload-form.upload-limit-reached")
        );
      } else {
        const plural = data.renders_left !== 1 ? "a" : "e"; // Serbian plural form
        this.statusMessage.display(
          "info",
          t("upload-form.uploads-status")
            .replace("%1$s", data.renders_left)
            .replace("%2$s", data.renders_left !== 1 ? "s" : "")
            .replace("%3$s", data.limit)
        );
      }
    } catch (error) {
      this.statusMessage.display(
        "error",
        t("upload-form.token-status-error")
      );
    }
  }

  handleFileSelected(file) {
    this.selectedFile = file;
    this.processButton.disabled = false;
    this.processButton.classList.remove("cursor-not-allowed", "opacity-50");
    this.processButton.classList.add("cursor-pointer", "hover:bg-primary-dark");
  }

  async processPhoto() {
    const token = getUrlParameter("at");
    if (!token) {
      this.statusMessage.display(
        "error",
        t("upload-form.token-error")
      );
      return;
    }

    const { roomType, furnitureStyle } = this.furnitureSelector.getSelections();
    if (!roomType || !furnitureStyle) {
      this.statusMessage.display(
        "error",
        t("upload-form.select-room-style")
      );
      return;
    }

    if (!this.selectedFile) {
      this.statusMessage.display("error", t("upload-form.select-image"));
      return;
    }

    this.disableProcessButton();

    try {
      const uploadedImageData = await this.uploadImage(
        this.selectedFile,
        token
      );
      if (uploadedImageData.success && uploadedImageData.url) {
        await this.createRender(
          uploadedImageData.url,
          roomType,
          furnitureStyle,
          token
        );
      } else {
        throw new Error("Upload failed");
      }
    } catch (error) {
      this.handleError(
        "upload_error",
        t("upload-form.upload-error")
      );
      this.enableProcessButton();
    }
  }

  async uploadImage(file, token) {
    const formData = new FormData();
    formData.append("image", file);
    formData.append("token", token);
    return await ApiService.uploadImage(formData);
  }

  async createRender(imageUrl, roomType, style, token) {
    console.log(
      "🚀 ~ App ~ createRender ~ getImageUrl(imageUrl):",
      getImageUrl(imageUrl)
    );
    const renderData = {
      image_url: getImageUrl(imageUrl),
      room_type: roomType,
      style,
      wait_for_completion: false,
      token,
    };
    try {
      const data = await ApiService.createRender(renderData);
      if (data.render_id) {
        this.redirectToNextPage(
          data.render_id,
          imageUrl,
          token,
          roomType,
          style
        );
      } else {
        throw new Error("No render_id received");
      }
    } catch (error) {
      this.handleError(
        "render_error",
        t("upload-form.render-error")
      );
      this.enableProcessButton();
    }
  }

  redirectToNextPage(renderId, imageUrl, token, roomType, style) {
    const nextPageUrl = vsaiApiSettings.nextPageUrl || "/virtual-staging-main";
    const url = new URL(nextPageUrl, window.location.origin);
    url.searchParams.set("render_id", renderId);
    url.searchParams.set(
      "image_url",
      encodeURIComponent(getImageUrl(imageUrl))
    );
    url.searchParams.set("at", token);
    url.searchParams.set("room", roomType);
    url.searchParams.set("style", style);
    window.location.href = url.toString();
  }

  handleError(code, defaultMessage) {
    console.error(`Error code: ${code}, Message: ${defaultMessage}`);
    const errorMessages = {
      invalid_token:
        t("upload-form.invalid-token"),
      missing_token:
        t("upload-form.missing-token"),
      limit_breached:
        t("upload-form.limit-breached"),
      missing_image: t("upload-form.missing-image"),
      upload_error:
        t("upload-form.upload-error"),
      render_error:
        t("upload-form.render-error"),
    };
    const message = errorMessages[code] || defaultMessage;
    this.statusMessage.display(
      "error",
      `${message} ${t("upload-form.contact-support")}`
    );
  }

  disableProcessButton() {
    this.processButton.disabled = true;
    this.processButton.classList.add("cursor-not-allowed", "opacity-50");
    this.processButton.classList.remove(
      "cursor-pointer",
      "hover:bg-primary-dark"
    );
  }

  enableProcessButton() {
    this.processButton.disabled = false;
    this.processButton.classList.remove("cursor-not-allowed", "opacity-50");
    this.processButton.classList.add("cursor-pointer", "hover:bg-primary-dark");
  }
}

// Initialize the application
document.addEventListener("DOMContentLoaded", async () => {
  const app = new App();
  await app.initialize();
});
