const DEV = false;

// DOM Utility Functions
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);
const getById = (id) => document.getElementById(id);
const hideElement = (element) => element.classList.add("hidden");
const showElement = (element) => element.classList.remove("hidden");

// URL and Parameter Utilities
const getUrlParams = () => new URLSearchParams(window.location.search);
const getUrlParam = (name) => getUrlParams().get(name);

// API Functions
const fetchWithAuth = async (url, options = {}) => {
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
};

const pollRenderStatus = async (renderId, onComplete) => {
  while (true) {
    const data = await fetchWithAuth(`render?render_id=${renderId}`);
    if (data.status === "done") {
      onComplete(data);
      break;
    }
    await new Promise((resolve) => setTimeout(resolve, 1500));
  }
};

const createVariation = async (renderId, style, roomType) => {
  const body = JSON.stringify({
    style,
    roomType,
    wait_for_completion: false,
  });
  return fetchWithAuth(`render/create-variation?render_id=${renderId}`, {
    method: "POST",
    body,
  });
};

// UI Update Functions
const updateResultsTitle = (roomType, style) => {
  const resultsTitle = $("#renderPageResultsContainer h3");
  if (resultsTitle) {
    resultsTitle.textContent = `Results (${capitalizeFirstLetter(
      roomType
    )}, ${capitalizeFirstLetter(style)})`;
  }
};

const setOriginalImage = (imageUrl) => {
  const container = $("#renderPageOriginalContainer .group");
  if (container && imageUrl) {
    container.innerHTML = `
      <img src="${decodeURIComponent(imageUrl)}" alt="Original Image" 
           class="h-full w-full bg-gray-100 object-contain transition-opacity group-hover:opacity-70">
    `;
  }
};

// Carousel Class
class Carousel {
  constructor(imageUrls) {
    this.imageUrls = imageUrls;
    this.currentIndex = 0;
    this.mainSlider = getById("main-slider");
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
      this.mainSlider.innerHTML = `
        <div class="slide-container" style="position: relative; width: 100%; height: 100%;">
          ${this.imageUrls
            .map(
              (url, index) => `
            <div class="slide" data-index="${index}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.3s ease;">
              <div class="relative">
                <div class="group w-full overflow-hidden rounded-xl transition-all duration-0 opacity-100 relative">
                  <img class="h-full w-full bg-gray-100 object-contain transition-opacity"
                    src="${url}" alt="Furnished image" loading="lazy">
                </div>
              </div>
            </div>
          `
            )
            .join("")}
        </div>
      `;
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
    $$(".slide").forEach((slide, index) => {
      slide.style.opacity = index === this.currentIndex ? "1" : "0";
      slide.style.zIndex = index === this.currentIndex ? "1" : "0";
    });

    $$(".thumb").forEach((thumb, index) => {
      if (index === this.currentIndex) {
        thumb.classList.add("selected");
        thumb.querySelector("div").classList.remove("opacity-25");
        thumb.querySelector("div").classList.add("opacity-100");
      } else {
        thumb.classList.remove("selected");
        thumb.querySelector("div").classList.remove("opacity-100");
        thumb.querySelector("div").classList.add("opacity-25");
      }
    });
  }

  attachEventListeners() {
    this.thumbnailSlider?.addEventListener("click", (event) => {
      const thumb = event.target.closest(".thumb");
      if (thumb) {
        this.setCurrentImage(Number(thumb.dataset.index));
      }
    });
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
}

// Main Application Logic
const initializeApp = async () => {
  const renderId = getUrlParam("render_id");
  const imageUrl = getUrlParam("image_url");
  const at = getUrlParam("at");

  setOriginalImage(imageUrl);
  setupUploadAnotherImageButton(at);
  setupGenerateVariationButton();

  if (renderId) {
    await handleRenderProcess(renderId);
  } else if (imageUrl) {
    const carousel = new Carousel([imageUrl]);
    await carousel.initialize();
    setupDownloadButton(carousel);
  } else {
    console.error("No render_id or image_url found in URL parameters");
  }
};

const handleRenderProcess = async (renderId) => {
  showLoadingIndicator();
  await pollRenderStatus(renderId, (data) => {
    hideLoadingIndicator();
    updateUIWithRenderResults(data);
  });
};

const updateUIWithRenderResults = (data) => {
  const imageUrl = getUrlParam("image_url");
  const roomType = data.outputs_room_types[0] || "";
  const style = data.outputs_styles[0] || "";
  const reversedOutputs = data.outputs.reverse();
  updateResultsTitle(roomType, style);
  const images = DEV
    ? [...reversedOutputs, ...Array(10).fill(imageUrl)]
    : reversedOutputs;
  const carousel = new Carousel(images);
  carousel.initialize();
  setupDownloadButton(carousel);
};

const setupGenerateVariationButton = () => {
  const button = getById("generateVariationButton");
  button?.addEventListener("click", handleGenerateVariation);
};

const handleGenerateVariation = async () => {
  const renderId = getUrlParam("render_id");
  const roomType = $(".room-type-select").value;
  const style = $(".furniture-style-select").value;

  showLoadingIndicator();
  try {
    const response = await createVariation(renderId, style, roomType);
    if (response.render_id) {
      await handleRenderProcess(response.render_id);
    } else {
      throw new Error("No render_id received from variation creation");
    }
  } catch (error) {
    console.error("Error generating variation:", error);
    hideLoadingIndicator();
    alert("Failed to generate variation. Please try again.");
  }
};

const setupUploadAnotherImageButton = (at) => {
  const button = getById("uploadAnotherImageButton");
  button?.addEventListener("click", () => {
    const currentUrl = new URL(window.location.href);
    const pathSegments = currentUrl.pathname.split("/").filter(Boolean);
    pathSegments[pathSegments.length - 1] = "virtual-staging-upload";
    currentUrl.pathname = `/${pathSegments.join("/")}`;
    if (at) currentUrl.searchParams.set("at", at);
    window.location.href = currentUrl.toString();
  });
};

const setupDownloadButton = (carousel) => {
  const button = getById("download-image-overlay");
  button?.addEventListener("click", () => downloadCurrentImage(carousel));
};

const downloadCurrentImage = async (carousel) => {
  const imageUrl = carousel.getCurrentImageUrl();
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
      carousel.currentIndex + 1
    }.${getFileExtension(blob.type)}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(blobUrl);
  } catch (error) {
    console.error("Error downloading image:", error);
    alert("Failed to download image. Please try again.");
  }
};

// Utility Functions
const capitalizeFirstLetter = (string) =>
  string.charAt(0).toUpperCase() + string.slice(1);
const getFileExtension = (mimeType) =>
  mimeType.split("/")[1]?.split("+")[0] || "jpg";
const showLoadingIndicator = () => showElement(getById("loading-indicator"));
const hideLoadingIndicator = () => hideElement(getById("loading-indicator"));

// Initialize the application
document.addEventListener("DOMContentLoaded", initializeApp);
