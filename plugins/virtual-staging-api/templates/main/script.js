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

const formatTitleString = (string) => {
  return string
    .split("_")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
};

const updateResultsTitle = (roomType, style) => {
  const resultsTitle = $("#renderPageResultsContainer h3");
  if (resultsTitle) {
    resultsTitle.textContent = `Results (${formatTitleString(
      roomType
    )}, ${formatTitleString(style)})`;
  }
};

const setOriginalImage = (imageUrl) => {
  const container = $("#renderPageOriginalContainer .group");
  if (container && imageUrl) {
    container.innerHTML = `
      <img src="${decodeURIComponent(
        imageUrl
      )}" alt="Original Image" class="h-full w-full bg-gray-100 transition-opacity group-hover:opacity-70" style="object-fit: cover;">
    `;
  }
};

// Carousel Class
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
      if (index === this.currentIndex) {
        slide.style.opacity = "1";
        slide.style.zIndex = "1";
      } else {
        slide.style.opacity = "0";
        slide.style.zIndex = "0";
      }
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
const initializeApp = async () => {
  const renderId = getUrlParam("render_id");
  const imageUrl = getUrlParam("image_url");
  const at = getUrlParam("at");

  setOriginalImage(imageUrl);
  setupUploadAnotherImageButton(at);
  setupGenerateVariationButton();

  if (renderId) {
    await handleInitialRenderProcess(renderId);
  } else if (imageUrl) {
    const carousel = new Carousel([imageUrl]);
    await carousel.initialize();
    setupDownloadButton(carousel);
  } else {
    console.error("No render_id or image_url found in URL parameters");
  }
};

const handleInitialRenderProcess = async (renderId) => {
  showLoadingIndicator();
  const generateButton = getById("generateVariationButton");
  const buttonSpan = generateButton.querySelector("span");
  const originalText = buttonSpan.textContent;

  let initialDataLoaded = false;

  while (true) {
    const data = await fetchWithAuth(`render?render_id=${renderId}`);

    if (!initialDataLoaded && data.outputs && data.outputs.length > 0) {
      updateUIWithRenderResults(data, true); // Pass true for isInitialLoad
      hideLoadingIndicator();
      initialDataLoaded = true;

      if (data.status !== "done") {
        buttonSpan.textContent = "Generating...";
      } else {
        generateButton.disabled = false;
        break; // Exit the loop if status is done
      }
    }

    if (data.status === "done") {
      if (!initialDataLoaded) {
        updateUIWithRenderResults(data, true); // Only update UI if not already loaded
      }
      setTimeout(() => {
        hideLoadingIndicator();
        generateButton.disabled = false;
        buttonSpan.textContent = originalText;
      }, 500);
      break;
    }

    await new Promise((resolve) => setTimeout(resolve, 1500));
  }
};

const updateUIWithRenderResults = (data, isInitialLoad = false) => {
  console.log(
    `Updating UI with render results. Initial load: ${isInitialLoad}`
  );

  const imageUrl = getUrlParam("image_url");
  const reversedOutputs = [...data.outputs].reverse();
  const reversedRoomTypes = [...data.outputs_room_types].reverse();
  const reversedStyles = [...data.outputs_styles].reverse();

  const images = DEV
    ? [...reversedOutputs, ...Array(10).fill(imageUrl)]
    : reversedOutputs;
  const roomTypes = DEV
    ? [...reversedRoomTypes, ...Array(10).fill("")]
    : reversedRoomTypes;
  const styles = DEV
    ? [...reversedStyles, ...Array(10).fill("")]
    : reversedStyles;

  if (isInitialLoad || !window.currentCarousel) {
    console.log("Creating new carousel");
    window.currentCarousel = new Carousel(images, roomTypes, styles);
    window.currentCarousel.initialize();
    setupDownloadButton(window.currentCarousel);
  } else {
    console.log("Updating existing carousel");
    window.currentCarousel.updateImages(images, roomTypes, styles);
  }
};

const setupGenerateVariationButton = () => {
  const button = getById("generateVariationButton");
  button?.addEventListener("click", handleGenerateVariation);
};

const handleGenerateVariation = async () => {
  const button = getById("generateVariationButton");
  const buttonSpan = button.querySelector("span");
  const originalText = buttonSpan.textContent;
  buttonSpan.textContent = "Generating...";
  button.disabled = true;

  const renderId = getUrlParam("render_id");
  const roomType = $(".room-type-select").value;
  const style = $(".furniture-style-select").value;

  try {
    const response = await createVariation(renderId, style, roomType);
    if (response.render_id) {
      await pollRenderStatus(response.render_id, (data) => {
        if (data.status === "done") {
          updateUIWithRenderResults(data);
          setTimeout(() => {
            buttonSpan.textContent = originalText;
            button.disabled = false;
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
  }
};

const setupUploadAnotherImageButton = (at) => {
  const button = getById("uploadAnotherImageButton");
  button?.addEventListener("click", () => {
    const currentUrl = new URL(window.location.href);
    const pathSegments = currentUrl.pathname.split("/").filter(Boolean);
    pathSegments[pathSegments.length - 1] = "virtual-staging-upload";

    // Create a new URL with only the path
    const newUrl = new URL(pathSegments.join("/"), currentUrl.origin);

    // Add only the 'at' parameter if it exists
    if (at) {
      newUrl.searchParams.set("at", at);
    }

    window.location.href = newUrl.toString();
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
