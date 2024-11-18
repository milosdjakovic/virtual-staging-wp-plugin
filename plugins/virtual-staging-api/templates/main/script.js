// Constants and translation helper
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
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);
const getById = (id) => document.getElementById(id);
const hideElement = (element) => element.classList.add("hidden");
const showElement = (element) => element.classList.remove("hidden");

const getUrlParams = () => new URLSearchParams(window.location.search);
const getUrlParam = (name) => getUrlParams().get(name);

const updateUrlParameter = (key, value) => {
  const url = new URL(window.location.href);
  url.searchParams.set(key, value);
  window.history.replaceState({}, "", url);
};

const formatTitleString = (string) => {
  return string
    .split("_")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
};

const capitalizeFirstLetter = (string) =>
  string.charAt(0).toUpperCase() + string.slice(1);
const getFileExtension = (mimeType) =>
  mimeType.split("/")[1]?.split("+")[0] || "jpg";

function getLabelForValue(value, selectId) {
  const select = document.getElementById(selectId);
  if (!select) return value;

  // Get from translations first
  const roomTypeTranslation = value && t("select_options.roomTypes")[value];
  const styleTranslation = value && t("select_options.styles")[value];

  if (roomTypeTranslation) return roomTypeTranslation;
  if (styleTranslation) return styleTranslation;

  // Fallback to select option text
  const option = select.querySelector(`option[value="${value}"]`);
  return option ? option.textContent : value;
}

// API Service
const ApiService = {
  async fetchWithAuth(url, options = {}) {
    const defaultOptions = {
      headers: {
        "X-WP-Nonce": vsaiApiSettings.nonce,
        "Content-Type": "application/json",
      },
    };
    const response = await fetch(`${vsaiApiSettings.root}${url}`, {
      ...defaultOptions,
      ...options,
    });
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
  },

  async pollRenderStatus(renderId, onComplete) {
    while (true) {
      const data = await this.fetchWithAuth(`render?render_id=${renderId}`);
      if (data.status === "done") {
        onComplete(data);
        break;
      }
      await new Promise((resolve) => setTimeout(resolve, 1500));
    }
  },

  async createVariation(renderId, style, roomType) {
    const body = JSON.stringify({
      style,
      roomType,
      wait_for_completion: false,
    });
    return this.fetchWithAuth(`render/create-variation?render_id=${renderId}`, {
      method: "POST",
      body,
    });
  },

  async checkTokenStatus(at) {
    return this.fetchWithAuth(`token-status?at=${at}`);
  },
};

// UI Components
class StatusMessage {
  constructor(containerId) {
    this.container = getById(containerId);
  }

  display(message) {
    if (this.container) {
      this.container.textContent = message;
    }
  }

  async updateRenderCount(data) {
    if (data.renders_left !== undefined) {
      const message = t("upload-form.uploads-status")
        .replace("%1$s", data.renders_left)
        .replace("%2$s", data.renders_left !== 1 ? "s" : "")
        .replace("%3$s", data.limit);
      this.display("info", message);
    }
  }
}

class Carousel {
  constructor(imageUrls, roomTypes, styles) {
    this.imageUrls = imageUrls;
    this.roomTypes = roomTypes;
    this.styles = styles;
    this.currentIndex = 0;
    this.mainSlider = getById("main-carousel-image");
    this.thumbnailSlider = getById("thumbnail-slider");
  }

  async initialize() {
    await this.preloadImages();
    this.render();
    this.attachEventListeners();
    this.showDownloadOverlay();
  }

  async preloadImages() {
    const loadImage = (url) =>
      new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = resolve;
        img.onerror = reject;
        img.src = url;
      });
    await Promise.all(this.imageUrls.map(loadImage));
  }

  render() {
    this.renderMainSlider();
    this.renderThumbnails();
    this.updateActiveSlide();
  }

  renderMainSlider() {
    if (this.mainSlider) {
      this.mainSlider.innerHTML = this.imageUrls
        .map(
          (url, index) => `
        <img 
          src="${url}" 
          alt="Furnished image ${index + 1}" 
          class="rounded-xl h-full absolute inset-0 transition-opacity duration-300 ease-in-out " 
          style="opacity: ${
            index === this.currentIndex ? "1" : "0"
          }; z-index: ${index === this.currentIndex ? "1" : "0"};
          object-fit: cover;"
        >
      `
        )
        .join("");
    }
  }

  renderThumbnails() {
    if (this.thumbnailSlider) {
      this.thumbnailSlider.innerHTML = this.imageUrls
        .map(
          (url, index) => `
        <li class="thumb" data-index="${index}" aria-label="slide item ${
            index + 1
          }" role="button" tabindex="0">
          <div class="relative w-24 transition-all duration-300">
            <div class="group w-full overflow-hidden rounded-xl relative">
              <img class="h-full w-full bg-gray-100 object-contain transition-opacity"
                src="${url}" alt="Furnished image" loading="lazy">
            </div>
          </div>
        </li>
      `
        )
        .join("");
    }
  }

  updateActiveSlide() {
    const slides = this.mainSlider.querySelectorAll("img");
    slides.forEach((slide, index) => {
      slide.style.opacity = index === this.currentIndex ? "1" : "0";
      slide.style.zIndex = index === this.currentIndex ? "1" : "0";
    });

    $$(".thumb").forEach((thumb, index) => {
      const div = thumb.querySelector("div");
      if (index === this.currentIndex) {
        thumb.classList.add("selected");
        div.classList.remove("opacity-25");
        div.classList.add("opacity-100");
      } else {
        thumb.classList.remove("selected");
        div.classList.remove("opacity-100");
        div.classList.add("opacity-25");
      }
    });

    this.updateTitle();
  }

  attachEventListeners() {
    this.thumbnailSlider?.addEventListener("click", (event) => {
      const thumb = event.target.closest(".thumb");
      if (thumb) {
        this.setCurrentImage(Number(thumb.dataset.index));
      }
    });
  }

  updateTitle() {
    const roomType = this.roomTypes[this.currentIndex] || "";
    const style = this.styles[this.currentIndex] || "";
    updateResultsTitle(roomType, style);
  }

  setCurrentImage(index) {
    if (index >= 0 && index < this.imageUrls.length) {
      this.currentIndex = index;
      this.updateActiveSlide();
    }
  }

  getCurrentImageUrl() {
    return this.imageUrls[this.currentIndex];
  }

  showDownloadOverlay() {
    showElement(getById("download-image-overlay"));
  }

  updateImages(newImageUrls, newRoomTypes, newStyles) {
    this.imageUrls = newImageUrls;
    this.roomTypes = newRoomTypes;
    this.styles = newStyles;
    this.currentIndex = 0;
    this.renderMainSlider();
    this.renderThumbnails();
    this.updateActiveSlide();
  }
}

// Main Application Logic
class App {
  constructor() {
    this.statusMessage = new StatusMessage("tokenStatusDisplay");
    this.carousel = null;
  }

  async initialize() {
    const renderId = getUrlParam("render_id");
    const imageUrl = getUrlParam("image_url");
    const at = getUrlParam("at");
    const room = getUrlParam("room");
    const style = getUrlParam("style");

    this.setOriginalImage(imageUrl);
    this.setupUploadAnotherImageButton(at);
    this.setupGenerateVariationButton();
    await this.updateTokenStatus(at);

    this.setInitialRoomAndStyle(room, style);

    if (renderId) {
      await this.handleInitialRenderProcess(renderId);
    } else if (imageUrl) {
      this.carousel = new Carousel([imageUrl], [], []);
      await this.carousel.initialize();
      this.setupDownloadButton();
    } else {
      console.error("No render_id or image_url found in URL parameters");
    }
  }

  setInitialRoomAndStyle(room, style) {
    const roomTypeSelect = document.getElementById("room-type-select");
    const furnitureStyleSelect = document.getElementById("furniture-style-select");

    if (room && roomTypeSelect) {
      roomTypeSelect.value = room;
    }
    if (style && furnitureStyleSelect) {
      furnitureStyleSelect.value = style;
    }

    if (roomTypeSelect) {
      roomTypeSelect.addEventListener("change", (e) =>
        updateUrlParameter("room", e.target.value)
      );
    }
    if (furnitureStyleSelect) {
      furnitureStyleSelect.addEventListener("change", (e) =>
        updateUrlParameter("style", e.target.value)
      );
    }
  }

  setOriginalImage(imageUrl) {
    const container = $("#renderPageOriginalContainer .group");
    if (container && imageUrl) {
      container.innerHTML = `
        <img src="${decodeURIComponent(
          imageUrl
        )}" alt="Original Image" class="h-full w-full bg-gray-100 transition-opacity group-hover:opacity-70" style="object-fit: cover;">
      `;
    }
  }

  setupUploadAnotherImageButton(at) {
    const button = getById("uploadAnotherImageButton");
    button?.addEventListener("click", () => {
      const nextPageUrl =
        vsaiApiSettings.nextPageUrl || "/virtual-staging-upload";
      const url = new URL(nextPageUrl, window.location.origin);

      // Add the access token
      if (at) url.searchParams.set("at", at);

      // Get current values of room and style
      const currentRoom = document.getElementById("room-type-select")?.value;
      const currentStyle = document.getElementById("furniture-style-select")?.value;

      // Add current room and style to URL if they exist
      if (currentRoom) url.searchParams.set("room", currentRoom);
      if (currentStyle) url.searchParams.set("style", currentStyle);

      window.location.href = url.toString();
    });
  }

  setupGenerateVariationButton() {
    const button = getById("generateVariationButton");
    button?.addEventListener("click", () => this.handleGenerateVariation());
  }

  async updateTokenStatus(at) {
    if (!at) {
      console.error("No access token found in URL parameters");
      return;
    }

    try {
      const response = await ApiService.checkTokenStatus(at);
      if (response.renders_left !== undefined) {
        this.statusMessage.display(`(${response.renders_left} left)`);
      }
    } catch (error) {
      console.error("Error fetching token status:", error);
    }
  }

  async handleInitialRenderProcess(renderId) {
    showLoadingIndicator();
    const generateButton = getById("generateVariationButton");
    const buttonSpan = generateButton.querySelector("span");
    const originalText = buttonSpan.textContent;

    let initialDataLoaded = false;

    while (true) {
      const data = await ApiService.fetchWithAuth(`render?render_id=${renderId}`);

      if (!initialDataLoaded && data.outputs && data.outputs.length > 0) {
        this.updateUIWithRenderResults(data, true);
        hideLoadingIndicator();
        initialDataLoaded = true;

        if (data.status !== "done") {
          buttonSpan.textContent = t("main.generating");
          this.showGeneratingVariationIndicator();
        } else {
          generateButton.disabled = false;
          break;
        }
      }

      if (data.status === "done") {
        if (!initialDataLoaded) {
          this.updateUIWithRenderResults(data, true);
        }
        setTimeout(() => {
          hideLoadingIndicator();
          generateButton.disabled = false;
          buttonSpan.textContent = originalText;
          this.hideGeneratingVariationIndicator();
        }, 500);
        break;
      }

      await new Promise((resolve) => setTimeout(resolve, 1500));
    }
  }

  updateUIWithRenderResults(data, isInitialLoad = false) {
    const imageUrl = getUrlParam("image_url");
    const reversedOutputs = [...data.outputs].reverse();
    const reversedRoomTypes = [...data.outputs_room_types].reverse();
    const reversedStyles = [...data.outputs_styles].reverse();

    const images = DEV_MODE
      ? [...reversedOutputs, ...Array(10).fill(imageUrl)]
      : reversedOutputs;
    const roomTypes = DEV_MODE
      ? [...reversedRoomTypes, ...Array(10).fill("")]
      : reversedRoomTypes;
    const styles = DEV_MODE
      ? [...reversedStyles, ...Array(10).fill("")]
      : reversedStyles;

    if (isInitialLoad || !this.carousel) {
      console.log("Creating new carousel");
      this.carousel = new Carousel(images, roomTypes, styles);
      this.carousel.initialize();
      this.setupDownloadButton();
    } else {
      console.log("Updating existing carousel");
      this.carousel.updateImages(images, roomTypes, styles);
    }

    if (roomTypes.length > 0 && styles.length > 0) {
      updateResultsTitle(roomTypes[0], styles[0]);
    }
  }

  async handleGenerateVariation() {
    const button = getById("generateVariationButton");
    const buttonSpan = button.querySelector("span");
    const originalText = buttonSpan.textContent;
    buttonSpan.textContent = t("main.generating");
    button.disabled = true;
    this.showGeneratingVariationIndicator();

    const renderId = getUrlParam("render_id");
    const roomType = document.getElementById("room-type-select").value;
    const style = document.getElementById("furniture-style-select").value;

    try {
      const response = await ApiService.createVariation(
        renderId,
        style,
        roomType
      );
      if (response.render_id) {
        await ApiService.pollRenderStatus(response.render_id, (data) => {
          if (data.status === "done") {
            this.updateUIWithRenderResults(data);
            setTimeout(() => {
              buttonSpan.textContent = originalText;
              button.disabled = false;
              this.hideGeneratingVariationIndicator();
            }, 500);
          }
        });
      } else {
        throw new Error("No render_id received from variation creation");
      }
    } catch (error) {
      console.error("Error generating variation:", error);
      alert("Failed to generate variation. Please try again.");
      buttonSpan.textContent = originalText;
      button.disabled = false;
      this.hideGeneratingVariationIndicator();
    }
  }

  setupDownloadButton() {
    const button = getById("download-image-overlay");
    button?.addEventListener("click", () => this.downloadCurrentImage());
  }

  async downloadCurrentImage() {
    const imageUrl = this.carousel.getCurrentImageUrl();
    if (!imageUrl) {
      console.error("No image selected for download");
      return;
    }

    try {
      const response = await fetch(imageUrl);
      const blob = await response.blob();
      const blobUrl = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = blobUrl;
      link.download = `virtual_staging_image_${
        this.carousel.currentIndex + 1
      }.${getFileExtension(blob.type)}`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(blobUrl);
    } catch (error) {
      console.error("Error downloading image:", error);
      alert("Failed to download image. Please try again.");
    }
  }

  showGeneratingVariationIndicator() {
    showElement(getById("generating-variation-indicator"));
  }

  hideGeneratingVariationIndicator() {
    hideElement(getById("generating-variation-indicator"));
  }
}

const updateResultsTitle = (roomType, style) => {
  const resultsTitle = $("#renderPageResultsContainer h3");
  if (resultsTitle) {
    const roomLabel = getLabelForValue(roomType, "room-type-select");
    const styleLabel = getLabelForValue(style, "furniture-style-select");
    resultsTitle.textContent = `Results (${roomLabel}, ${styleLabel})`;
  }
};

const showLoadingIndicator = () => showElement(getById("loading-indicator"));
const hideLoadingIndicator = () => hideElement(getById("loading-indicator"));

// Initialize the application
document.addEventListener("DOMContentLoaded", () => {
  const app = new App();
  app.initialize();
});
