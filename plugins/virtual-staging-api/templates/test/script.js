jQuery(document).ready(function ($) {
  $("#vsai-api-test-form").on("submit", function (e) {
    e.preventDefault();
    var endpoint = $("#vsai-api-endpoint").val();
    var apiUrl = vsaiApiSettings.root + endpoint;

    $("#vsai-api-response").html("Loading...");

    $.ajax({
      url: apiUrl,
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", vsaiApiSettings.nonce);
      },
      success: function (response) {
        $("#vsai-api-response").html(
          "<pre>" + JSON.stringify(response, null, 2) + "</pre>"
        );
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#vsai-api-response").html(
          "Error: " + textStatus + " - " + errorThrown
        );
      },
    });
  });
});
