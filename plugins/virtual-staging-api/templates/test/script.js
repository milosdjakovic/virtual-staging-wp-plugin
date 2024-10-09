jQuery(document).ready(($) => {
  $("#vsai-api-test-form").on("submit", (e) => {
    e.preventDefault();
    const endpoint = $("#vsai-api-endpoint").val();
    const apiUrl = vsaiApiSettings.root + endpoint;

    $("#vsai-api-response").html("Loading...");

    $.ajax({
      url: apiUrl,
      method: "GET",
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", vsaiApiSettings.nonce);
      },
      success: (response) => {
        $("#vsai-api-response").html(
          `<pre>${JSON.stringify(response, null, 2)}</pre>`
        );
      },
      error: (jqXHR, textStatus, errorThrown) => {
        $("#vsai-api-response").html(`Error: ${textStatus} - ${errorThrown}`);
      },
    });
  });
});
