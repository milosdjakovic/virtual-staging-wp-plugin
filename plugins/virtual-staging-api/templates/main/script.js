document.addEventListener("DOMContentLoaded", initializeApp);

function initializeApp() {
  const urlParams = new URLSearchParams(window.location.search);
  const renderId = urlParams.get("render_id");

  if (renderId) {
    pollRenderStatus(renderId);
  } else {
    console.error("No render_id found in URL parameters");
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
  }, 1000);
}

function updateUI(data) {
  updateResultsTitle(data);
  updateMainCarousel(data);
  updateThumbnails(data);
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

function updateMainCarousel(data) {
  const mainSlider = document.getElementById("main-slider");
  if (mainSlider) {
    mainSlider.innerHTML = data.outputs
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

function updateThumbnails(data) {
  const thumbnailSlider = document.getElementById("thumbnail-slider");
  if (thumbnailSlider) {
    thumbnailSlider.innerHTML = data.outputs
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
