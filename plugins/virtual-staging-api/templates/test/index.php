<div class="vsai-test-form">
  <h2>VSAI API Test</h2>
  <form id="vsai-api-test-form">
    <select id="vsai-api-endpoint" name="endpoint">
      <option value="options">Get Options</option>
      <option value="ping">Ping</option>
      <!-- Add more endpoints here as needed -->
    </select>
    <button type="submit">Test API</button>
  </form>
  <div id="vsai-api-response"></div>
</div>

<script>
  var vsaiApiSettings = {
    root: '<?php echo esc_url_raw(rest_url('vsai/v1/')); ?>',
    nonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
  };
</script>