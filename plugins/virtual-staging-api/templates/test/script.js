jQuery(document).ready(function ($) {
  var renderId = null;

  $("#vsai-upload-form").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      url: vsaiApiSettings.root + "upload-image",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", vsaiApiSettings.nonce);
      },
      success: function (response) {
        if (response.url) {
          $("#image_url").val(response.url);
          $("#vsai-upload-status").html("Image uploaded successfully");
          $("#vsai-render-form").show();
        } else {
          $("#vsai-upload-status").html("Error: No image URL received");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#vsai-upload-status").html(
          "Error: " + textStatus + " - " + errorThrown
        );
      },
    });
  });

  $("#vsai-render-form").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      url: vsaiApiSettings.root + "render/create",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", vsaiApiSettings.nonce);
      },
      success: function (response) {
        if (response.render_id) {
          renderId = response.render_id;
          $("#vsai-render-id").html("Render ID: " + renderId);
          $("#check-status").show();
        } else {
          $("#vsai-render-status").html("Error: No render ID received");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#vsai-render-status").html(
          "Error: " + textStatus + " - " + errorThrown
        );
      },
    });
  });

  $("#check-status").on("click", function () {
    if (renderId) {
      checkRenderStatus(renderId);
    } else {
      $("#vsai-render-status").html("No render ID available");
    }
  });

  function checkRenderStatus(renderId) {
    $.ajax({
      url: vsaiApiSettings.root + "render",
      method: "GET",
      data: { render_id: renderId },
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", vsaiApiSettings.nonce);
      },
      success: function (response) {
        if (response.status === "rendering") {
          $("#vsai-render-status").html(
            "Rendering: " + (response.progress * 100).toFixed(2) + "%"
          );
        } else if (response.status === "done") {
          displayResults(response);
        } else {
          $("#vsai-render-status").html(
            "Unexpected status: " + response.status
          );
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#vsai-render-status").html(
          "Error: " + textStatus + " - " + errorThrown
        );
      },
    });
  }

  function displayResults(response) {
    var resultHtml = "<h3>Render Complete</h3>";
    resultHtml += "<p>Styles: " + response.outputs_styles.join(", ") + "</p>";
    resultHtml +=
      "<p>Room Types: " + response.outputs_room_types.join(", ") + "</p>";
    resultHtml += '<div class="render-images">';
    response.outputs.forEach(function (imageUrl) {
      resultHtml += '<img src="' + imageUrl + '" alt="Rendered Image">';
    });
    resultHtml += "</div>";
    $("#vsai-render-result").html(resultHtml);
    $("#vsai-render-status").html("Render complete!");
  }
});
