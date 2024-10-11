const DEV = false; // Set this to true for development mode

// DOM-related functions
const getDOMElement = (id) => document.getElementById(id);
const querySelector = (selector) => document.querySelector(selector);
const hideElement = (element) => element.classList.add("hidden");
const showElement = (element) => element.classList.remove("hidden");
const getRoomType = () => document.querySelector(".room-type-select").value;
const getFurnitureStyle = () =>
  document.querySelector(".furniture-style-select").value;

// URL-related functions
const getUrlParams = () => new URLSearchParams(window.location.search);
const getUrlParam = (params, key) => params.get(key);

// API-related functions
const fetchRenderStatus = async (renderId) => {
  const response = await fetch(
    `${vsaiApiSettings.root}render?render_id=${renderId}`,
    {
      headers: { "X-WP-Nonce": vsaiApiSettings.nonce },
    }
  );
  return response.json();
};

// Image-related functions
const preloadImage = (url) => {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = resolve;
    img.onerror = reject;
    img.src = url;
  });
};

const preloadImages = async (imageUrls) => {
  const promises = imageUrls.map(preloadImage);
  await Promise.all(promises);
};

// Utility functions
const capitalizeFirstLetter = (string) =>
  string.charAt(0).toUpperCase() + string.slice(1);

// Main application logic
const initializeApp = async () => {
  const urlParams = getUrlParams();
  const renderId = getUrlParam(urlParams, "render_id");
  const imageUrl = getUrlParam(urlParams, "image_url");
  const at = getUrlParam(urlParams, "at");

  if (renderId) {
    await pollRenderStatus(renderId);
  } else if (imageUrl) {
    setOriginalImage(imageUrl);
    const carousel = new Carousel([imageUrl]);
    setupDownloadButton(carousel);
  } else {
    console.error("No render_id or image_url found in URL parameters");
  }

  setupUploadAnotherImageButton(at);
};

const pollRenderStatus = async (renderId) => {
  const loadingIndicator = getDOMElement("loading-indicator");

  while (true) {
    const data = await fetchRenderStatus(renderId);
    if (data.status === "done") {
      hideElement(loadingIndicator);
      updateUI(data);
      break;
    }
    await new Promise((resolve) => setTimeout(resolve, 1500));
  }
};

const updateUI = (data) => {
  updateResultsTitle(data);
  const imageUrl = getUrlParam(getUrlParams(), "image_url");
  setOriginalImage(imageUrl);
  const images = DEV
    ? [...data.outputs, ...Array(10).fill(imageUrl)]
    : data.outputs;
  const carousel = new Carousel(images);
  setupDownloadButton(carousel);
};

const updateResultsTitle = (data) => {
  const resultsTitle = querySelector("#renderPageResultsContainer h3");
  if (resultsTitle) {
    const roomType = capitalizeFirstLetter(data.outputs_room_types[0] || "");
    const style = capitalizeFirstLetter(data.outputs_styles[0] || "");
    resultsTitle.textContent = `Results (${roomType}, ${style})`;
  }
};

const setOriginalImage = (imageUrl) => {
  if (!imageUrl) {
    console.error("No image URL provided for original image");
    return;
  }
  const container = querySelector("#renderPageOriginalContainer .group");
  if (container) {
    const img = document.createElement("img");
    img.src = decodeURIComponent(imageUrl);
    img.alt = "Original Image";
    img.className =
      "h-full w-full bg-gray-100 object-contain transition-opacity group-hover:opacity-70";
    container.innerHTML = "";
    container.appendChild(img);
  } else {
    console.error("Original image container not found");
  }
};

class Carousel {
  constructor(imageUrls) {
    this.imageUrls = imageUrls;
    this.currentIndex = 0;
    this.mainSlider = getDOMElement("main-slider");
    this.thumbnailSlider = getDOMElement("thumbnail-slider");
    this.initializeCarousel();
  }

  async initializeCarousel() {
    await preloadImages(this.imageUrls);
    this.renderMainSlider();
    this.renderThumbnails();
    this.updateActiveSlide();
    this.attachEventListeners();
    this.showDownloadOverlay();
  }

  renderMainSlider() {
    if (this.mainSlider) {
      this.mainSlider.innerHTML = `
        <div class="slide-container" style="position: relative; width: 100%; height: 100%;">
          ${this.imageUrls
            .map(
              (imageUrl, index) => `
            <div class="slide" data-index="${index}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.3s ease;">
              <div class="relative">
                <div class="group w-full overflow-hidden rounded-xl transition-all duration-0 opacity-100 relative">
                  <img class="h-full w-full bg-gray-100 object-contain transition-opacity"
                    src="${imageUrl}" alt="Furnished image" loading="lazy">
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
          (imageUrl, index) => `
        <li class="thumb" data-index="${index}" aria-label="slide item ${
            index + 1
          }" role="button" tabindex="0">
          <div class="relative w-24 transition-all duration-300">
            <div class="group w-full overflow-hidden rounded-xl relative">
              <img class="h-full w-full bg-gray-100 object-contain transition-opacity"
                src="${imageUrl}" alt="Furnished image" loading="lazy">
            </div>
          </div>
        </li>
      `
        )
        .join("");
    }
  }

  updateActiveSlide() {
    const slides = this.mainSlider.querySelectorAll(".slide");
    const thumbs = this.thumbnailSlider.querySelectorAll(".thumb");

    for (const [index, slide] of slides.entries()) {
      if (index === this.currentIndex) {
        slide.style.opacity = "1";
        slide.style.zIndex = "1";
      } else {
        slide.style.opacity = "0";
        slide.style.zIndex = "0";
      }
    }

    for (const [index, thumb] of thumbs.entries()) {
      if (index === this.currentIndex) {
        thumb.classList.add("selected");
        thumb.querySelector("div").classList.remove("opacity-25");
        thumb.querySelector("div").classList.add("opacity-100");
      } else {
        thumb.classList.remove("selected");
        thumb.querySelector("div").classList.remove("opacity-100");
        thumb.querySelector("div").classList.add("opacity-25");
      }
    }
  }

  attachEventListeners() {
    if (this.thumbnailSlider) {
      this.thumbnailSlider.addEventListener("click", (event) => {
        const thumb = event.target.closest(".thumb");
        if (thumb) {
          const index = Number.parseInt(thumb.dataset.index, 10);
          this.setCurrentImage(index);
        }
      });
    }
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
    const downloadOverlay = getDOMElement("download-image-overlay");
    if (downloadOverlay) {
      showElement(downloadOverlay);
    }
  }
}

const setupUploadAnotherImageButton = (at) => {
  const uploadButton = getDOMElement("uploadAnotherImageButton");
  if (uploadButton) {
    uploadButton.addEventListener("click", () => {
      const currentUrl = new URL(window.location.href);
      const pathSegments = currentUrl.pathname
        .split("/")
        .filter((segment) => segment !== "");
      pathSegments[pathSegments.length - 1] = "virtual-staging-upload";
      currentUrl.pathname = `/${pathSegments.join("/")}`;
      if (at) {
        currentUrl.searchParams.set("at", at);
      }
      window.location.href = currentUrl.toString();
    });
  } else {
    console.error("Upload Another Image button not found");
  }
};

const setupDownloadButton = (carousel) => {
  const downloadButton = getDOMElement("download-image-overlay");
  if (downloadButton) {
    downloadButton.addEventListener("click", async () => {
      const imageUrl = carousel.getCurrentImageUrl();
      const currentIndex = carousel.currentIndex;
      if (imageUrl) {
        try {
          const response = await fetch(imageUrl);
          const blob = await response.blob();
          const blobUrl = window.URL.createObjectURL(blob);
          const fileExtension = blob.type.split("/")[1]?.split("+")[0] || "jpg";
          const filename = `virtual_staging_image_${
            currentIndex + 1
          }.${fileExtension}`;
          const link = document.createElement("a");
          link.href = blobUrl;
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(blobUrl);
        } catch (error) {
          console.error("Error downloading image:", error);
        }
      } else {
        console.error("No image selected for download");
      }
    });
  } else {
    console.error("Download button not found");
  }
};

document.addEventListener("DOMContentLoaded", initializeApp);
