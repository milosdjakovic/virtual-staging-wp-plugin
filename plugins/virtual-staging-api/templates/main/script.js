document.addEventListener("DOMContentLoaded", initializeApp);

function initializeApp() {
  const urlParams = new URLSearchParams(window.location.search);
  const renderId = urlParams.get("render_id");
  const imageUrl = urlParams.get("image_url");
  const at = urlParams.get("at");

  if (renderId) {
    pollRenderStatus(renderId);
  } else if (imageUrl) {
    // If there's no render_id but there is an imageUrl, we can still show the original image
    setOriginalImage(imageUrl);
    const carousel = new Carousel([imageUrl]);
    setupDownloadButton(carousel);
  } else {
    console.error("No render_id or image_url found in URL parameters");
  }

  setupUploadAnotherImageButton(at);
}

function setOriginalImage(imageUrl) {
  const originalImageContainer = document.querySelector(
    "#renderPageOriginalContainer .group"
  );
  if (originalImageContainer) {
    originalImageContainer.innerHTML = "";
    const img = document.createElement("img");
    img.src = decodeURIComponent(imageUrl);
    img.alt = "Original Image";
    img.className =
      "h-full w-full bg-gray-100 object-contain transition-opacity group-hover:opacity-70";
    originalImageContainer.appendChild(img);
  } else {
    console.error("Original image container not found");
  }
}

function pollRenderStatus(renderId) {
  const pollInterval = setInterval(() => {
    fetch(`${vsaiApiSettings.root}render?render_id=${renderId}`, {
      headers: {
        "X-WP-Nonce": vsaiApiSettings.nonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "done") {
          clearInterval(pollInterval);
          updateUI(data);
        }
      })
      .catch((error) => {
        console.error("Error polling render status:", error);
      });
  }, 1500);
}

function updateUI(data) {
  updateResultsTitle(data);
  const imageUrl = new URLSearchParams(window.location.search).get("image_url");
  const combinedImages = [...data.outputs, ...Array(10).fill(imageUrl)];
  const carousel = new Carousel(combinedImages);
  setupDownloadButton(carousel);
}

function updateResultsTitle(data) {
  const resultsTitle = document.querySelector("#renderPageResultsContainer h3");
  if (resultsTitle) {
    const roomType = capitalizeFirstLetter(data.outputs_room_types[0] || "");
    const style = capitalizeFirstLetter(data.outputs_styles[0] || "");
    resultsTitle.textContent = `Results (${roomType}, ${style})`;
  }
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

class Carousel {
  constructor(imageUrls) {
    this.imageUrls = imageUrls;
    this.currentIndex = 0;
    this.mainSlider = document.getElementById("main-slider");
    this.thumbnailSlider = document.getElementById("thumbnail-slider");
    this.preloadImages();
    this.attachEventListeners();
  }

  preloadImages() {
    const imagePromises = this.imageUrls.map((url) => {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = resolve;
        img.onerror = reject;
        img.src = url;
      });
    });

    Promise.all(imagePromises)
      .then(() => {
        this.initializeCarousel();
      })
      .catch((error) => {
        console.error("Error preloading images:", error);
        this.initializeCarousel(); // Still initialize even if some images fail to load
      });
  }

  initializeCarousel() {
    this.renderMainSlider();
    this.renderThumbnails();
    this.updateActiveSlide();
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

    slides.forEach((slide, index) => {
      if (index === this.currentIndex) {
        slide.style.opacity = "1";
        slide.style.zIndex = "1";
      } else {
        slide.style.opacity = "0";
        slide.style.zIndex = "0";
      }
    });

    thumbs.forEach((thumb, index) => {
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

  addImages(newImageUrls) {
    this.imageUrls = [...this.imageUrls, ...newImageUrls];
    this.preloadImages();
    this.initializeCarousel();
  }

  getCurrentImageUrl() {
    return this.imageUrls[this.currentIndex];
  }

  showDownloadOverlay() {
    const downloadOverlay = document.getElementById("download-image-overlay");
    if (downloadOverlay) {
      downloadOverlay.classList.remove("hidden");
    }
  }
}

function setupUploadAnotherImageButton(at) {
  const uploadButton = document.getElementById("uploadAnotherImageButton");
  if (uploadButton) {
    uploadButton.addEventListener("click", () => {
      // Get the current URL
      const currentUrl = new URL(window.location.href);

      // Split the pathname into segments
      const pathSegments = currentUrl.pathname
        .split("/")
        .filter((segment) => segment !== "");

      // Replace the last segment with 'virtual-staging-upload'
      pathSegments[pathSegments.length - 1] = "virtual-staging-upload";

      // Reconstruct the URL
      currentUrl.pathname = `/${pathSegments.join("/")}`;

      // Ensure the 'at' parameter is in the URL if it exists
      if (at) {
        currentUrl.searchParams.set("at", at);
      }

      // Redirect to the new URL
      window.location.href = currentUrl.toString();
    });
  } else {
    console.error("Upload Another Image button not found");
  }
}

function setupDownloadButton(carousel) {
  const downloadButton = document.getElementById("download-image-overlay");
  if (downloadButton) {
    downloadButton.addEventListener("click", () => {
      const imageUrl = carousel.getCurrentImageUrl();
      const currentIndex = carousel.currentIndex;
      if (imageUrl) {
        // Fetch the image data
        fetch(imageUrl)
          .then((response) => response.blob())
          .then((blob) => {
            // Create a blob URL
            const blobUrl = window.URL.createObjectURL(blob);

            // Determine the file extension based on the blob's type
            let fileExtension = "jpg"; // Default to jpg
            if (blob.type) {
              const mimeType = blob.type.split("/")[1];
              if (mimeType) {
                fileExtension = mimeType.split("+")[0]; // Handle cases like 'image/jpeg+xml'
              }
            }

            // Create a filename with the index
            const filename = `virtual_staging_image_${
              currentIndex + 1
            }.${fileExtension}`;

            // Create a temporary anchor element
            const link = document.createElement("a");
            link.href = blobUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Release the blob URL
            window.URL.revokeObjectURL(blobUrl);
          })
          .catch((error) => {
            console.error("Error downloading image:", error);
          });
      } else {
        console.error("No image selected for download");
      }
    });
  } else {
    console.error("Download button not found");
  }
}
