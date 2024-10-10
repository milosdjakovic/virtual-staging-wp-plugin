document.addEventListener("DOMContentLoaded", initializeApp);

function initializeApp() {
  const urlParams = new URLSearchParams(window.location.search);
  const renderId = urlParams.get("render_id");
  const imageUrl = urlParams.get("image_url");

  if (renderId) {
    pollRenderStatus(renderId);
  } else {
    console.error("No render_id found in URL parameters");
  }

  if (imageUrl) {
    setOriginalImage(imageUrl);
  }
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
  new Carousel(combinedImages);
}

function updateResultsTitle(data) {
  const resultsTitle = document.querySelector("#renderPageResultsContainer h3");
  if (resultsTitle) {
    const roomType = capitalizeFirstLetter(data.outputs_room_types[0] || "");
    const style = capitalizeFirstLetter(data.outputs_styles[0] || "");
    resultsTitle.textContent = `Results ${roomType}, ${style} style`;
  }
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

class Carousel {
  constructor(imageUrls) {
    this.imageUrls = imageUrls;
    this.updateMainCarousel();
    this.updateThumbnails();
  }

  updateMainCarousel() {
    const mainSlider = document.getElementById("main-slider");
    if (mainSlider) {
      mainSlider.innerHTML = this.imageUrls
        .map(
          (imageUrl, index) => `
            <li class="slide${index === 0 ? " selected" : ""}">
                <div class="relative">
                    <div class="group w-full overflow-hidden rounded-xl transition-all duration-0 opacity-100 relative">
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

  updateThumbnails() {
    const thumbnailSlider = document.getElementById("thumbnail-slider");
    if (thumbnailSlider) {
      thumbnailSlider.innerHTML = this.imageUrls
        .map(
          (imageUrl, index) => `
            <li class="thumb${
              index === 0 ? " selected" : ""
            }" aria-label="slide item ${index + 1}" role="button" tabindex="0">
                <div class="relative w-24 transition-all duration-300 ${
                  index === 0 ? "opacity-100" : "opacity-25 hover:opacity-100"
                }">
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

  addImages(newImageUrls) {
    this.imageUrls = [...this.imageUrls, ...newImageUrls];
    this.updateMainCarousel();
    this.updateThumbnails();
  }
}

// Test function to populate carousel with duplicate images
function testCarouselWithDuplicateImages(imageUrl, count = 10) {
  const duplicateImages = Array(count).fill(imageUrl);
  new Carousel(duplicateImages);
}

// Uncomment the line below and call this function to test the carousel
// testCarouselWithDuplicateImages('https://example.com/test-image.jpg');
