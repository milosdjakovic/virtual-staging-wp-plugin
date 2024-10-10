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
    this.currentIndex = 0;
    this.mainSlider = document.getElementById("main-slider");
    this.thumbnailSlider = document.getElementById("thumbnail-slider");
    this.initializeCarousel();
    this.attachEventListeners();
  }

  initializeCarousel() {
    this.renderMainSlider();
    this.renderThumbnails();
    this.updateActiveSlide();
  }

  renderMainSlider() {
    if (this.mainSlider) {
      this.mainSlider.innerHTML = this.imageUrls
        .map(
          (imageUrl, index) => `
            <li class="slide" data-index="${index}">
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
        slide.classList.add("selected");
        slide.style.display = "block";
      } else {
        slide.classList.remove("selected");
        slide.style.display = "none";
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
          const index = parseInt(thumb.dataset.index, 10);
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
    this.initializeCarousel();
  }
}
