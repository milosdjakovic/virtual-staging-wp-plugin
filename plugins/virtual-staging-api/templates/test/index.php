<div class="vsai-test-form">
  <h2>VSAI Render Test</h2>
  <form id="vsai-upload-form" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Upload Image</button>
  </form>
  <div id="vsai-upload-status"></div>

  <form id="vsai-render-form" style="display:none;">
    <input type="hidden" name="image_url" id="image_url">
    <select name="room_type" required>
      <option value="bed">Bedroom</option>
      <option value="living">Living Room</option>
      <!-- Add more room types as needed -->
    </select>
    <select name="style" required>
      <option value="standard">Standard</option>
      <option value="modern">Modern</option>
      <!-- Add more styles as needed -->
    </select>
    <button type="submit">Create Render</button>
  </form>
  <div id="vsai-render-id"></div>
  <button id="check-status" style="display:none;">Check Status</button>
  <div id="vsai-render-status"></div>
  <div id="vsai-render-result"></div>
</div>

<script>
  var vsaiApiSettings = {
    root: '<?php echo esc_url_raw(rest_url('vsai/v1/')); ?>',
    nonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
  };
</script>