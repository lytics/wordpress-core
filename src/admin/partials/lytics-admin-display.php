<?php

/**
 * Core admin area functionality of the plugin.
 */
?>

<?php
if (isset($_GET['error']) && isset($_GET['lyticswp_settings_save_nonce'])) {
  $nonce = sanitize_text_field($_GET['lyticswp_settings_save_nonce']);
  if (!wp_verify_nonce($nonce, 'lyticswp_settings_save_nonce')) {
    wp_die('Security check failed');
  } else {
    $error = sanitize_text_field($_GET['error']);
    add_settings_error('lyticswp_core', 'lyticswp_access_token', $error, 'error');
  }
}
settings_errors('lyticswp_core');

$lyticswp_access_token = get_option('lyticswp_access_token');
$lyticswp_account_name = get_option('lyticswp_account_name');
$lyticswp_account_id = get_option('lyticswp_account_id');
$lyticswp_aid = get_option('lyticswp_aid');
$lyticswp_domain = get_option('lyticswp_domain');
$lytics_enabled = get_option('lyticswp_enable_tag');
$lyticswp_debug_mode = get_option('lyticswp_debug_mode');
$lyticswp_ignore_admin_users = get_option('lyticswp_ignore_admin_users');
$lyticswp_tag_config = get_option('lyticswp_tag_config');

// if token is valid non empty string
if ($lyticswp_access_token) {
  $hasValidToken = true;
}

// get current account details wordpress
$account_details = $this->get_lytics_account_details($lyticswp_access_token);

$promo_message = '';

// if there is not currently an access token set 
if (!$lyticswp_access_token) {
  $promo_message = 'Don\'t have a Lytics account yet? Begin with our <a
href="https://www.lytics.com/personalization-engine-pantheon/" target="_blank">FREE Pantheon-sponsored developer
tier</a>. Start risk-free, today!.';
}

// if account details package contains key "developer_free" change message to say woo free
if (isset($account_details['packages']) && array_key_exists('developer_free', $account_details['packages'])) {
  $promo_message = 'You are using our FREE developer tier. Upgrade to the growth tier and unlock custom attributes, real-time segmentation, and hundreds of sources and destinations to supercharge your capabilities.';
}

?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }

    document.querySelector("form").addEventListener("submit", function() {
      scrollToTop();
    });
  });
</script>

<div id="lytics-settings">
  <div class="admin-header">
    <h1>Lytics Personalization Engine</h1>
    <p class="subhead">Connect your website to the Lytics Personalization Engine for powerful real-time web
      personalization. Deliver custom messages based on hundreds of attributes and turnkey features. Gain insights into
      content engagement directly within WordPress and more.</p>
  </div>

  <?php if ($promo_message !== "") : ?>
    <div class="promo-banner">
      <?php echo wp_kses_post($promo_message); ?>
    </div>
  <?php endif; ?>

  <div class="container-fluid p-3 m-0">
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <?php wp_nonce_field('lyticswp_config_form_action', 'lyticswp_config_form_nonce'); ?>
      <?php settings_fields('lyticswp_core'); ?>

      <input type="hidden" name="action" value="lytics_process_form">

      <!-- Credentials -->
      <div class="container-fluid mt-4 border rounded shadow-sm p-0">
        <div class="container-fluid pt-2 pb-2 mb-2 d-flex section-head">
          <img class="me-2" src="<?php echo esc_attr(plugin_dir_url(dirname(__FILE__))) . '/img/key-icon.svg'; ?>" alt="Icon of a key.">
          <h5>Credentials</h5>
        </div>

        <div class="container-fluid">
          <div class="mb-3">
            <label for="access_token" class="form-label">Access Token</label>
            <input type="password" class="form-control" id="access_token" name="access_token" value="<?php echo esc_attr($lyticswp_access_token); ?>">
            <p class="pt-2" style="font-size:12px;">Enter Lytics Access Token. Additional guidance on creating and
              managing Lytics Access Tokens is available in our documentation.
            </p>
          </div>
        </div>
      </div>

      <!-- Account Details -->
      <div class="container-fluid mt-4 border rounded shadow-sm p-0<?php echo $hasValidToken ? '' : ' d-none' ?>">
        <div class="container-fluid pt-2 pb-2 mb-2 d-flex section-head">
          <img class="me-2" src="<?php echo esc_attr(plugin_dir_url(dirname(__FILE__))) . '/img/profile-icon.svg'; ?>" alt="Icon of a user outline and some lines representing details.">
          <h5>Account Details</h5>
        </div>
        <div class="container-fluid">
          <div class="mb-3">
            <label for="account_name" class="form-label">Account Name</label>
            <input type="text" class="form-control" id="account_name" disabled name="account_name" value="<?php echo esc_attr($lyticswp_account_name); ?>">
          </div>
          <div class="mb-3">
            <label for="account_id" class="form-label">Account ID</label>
            <input type="text" class="form-control" id="account_id" disabled name="account_id" value="<?php echo esc_attr($lyticswp_account_id); ?>">
          </div>
          <div class="mb-3">
            <label for="aid" class="form-label">Account AID</label>
            <input type="text" class="form-control" id="aid" disabled name="aid" value="<?php echo esc_attr($lyticswp_aid); ?>">
          </div>
          <div class="mb-3">
            <label for="domain" class="form-label">Domain</label>
            <input type="text" class="form-control" id="domain" disabled name="domain" value="<?php echo esc_attr($lyticswp_domain); ?>">
          </div>
        </div>
      </div>

      <!-- Configuration -->
      <div class="container-fluid mt-4 border rounded shadow-sm p-0<?php echo $hasValidToken ? '' : ' d-none' ?>">
        <div class="container-fluid pt-2 pb-2 mb-2 d-flex section-head">
          <img class="me-2" src="<?php echo esc_attr(plugin_dir_url(dirname(__FILE__))) . '/img/config-icon.svg'; ?>" alt="Icon of a code bracket for configuration.">
          <h5 class="p-0 m-0">Configuration</h5>
        </div>
        <div class="container-fluid">
          <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" id="edit-enable-tag" style="width: 1rem; height: 1rem;" class="me-3" name="enable_tag" <?php echo ($lytics_enabled == 1) ? 'checked' : ''; ?>>
            <div>
              <label for="edit-enable-tag">Enable Tag</label>
              <p class="mb-0" style="font-size: 12px;">Enable the Lytics JavaScript tag.</p>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" id="edit-debug-mode" style="width: 1rem; height: 1rem;" class="me-3" name="debug_mode" <?php echo ($lyticswp_debug_mode == 1) ? 'checked' : ''; ?>>
            <div>
              <label for="edit-debug-mode">Enable Debug Mode</label>
              <p class="mb-0" style="font-size: 12px;">Enable debug mode for extra logging and non-minified Lytics tag.
              </p>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" id="edit-ignore-admin-users" style="width: 1rem; height: 1rem;" class="me-3" name="ignore_admin_users" <?php echo ($lyticswp_ignore_admin_users == 1) ? 'checked' : ''; ?>>
            <div>
              <label for="edit-ignore-admin-users">Ignore Admin Users</label>
              <p class="mb-0" style="font-size: 12px;">When activated Lytics will not be installed for users who are
                actively logged in to WordPress. This may prevent testing of personalization and recommendation features
                but also may prevent skewing of analytics data.
              </p>
            </div>
          </div>
          <div class="mb-3 form-group">
            <label for="jsonInput">Additional Tag Configuration</label>
            <textarea id="jsonInput" class="form-control" rows="5" name="tag_config"><?php echo esc_attr(stripslashes($lyticswp_tag_config)); ?></textarea>
          </div>
        </div>
      </div>
      <div class="container-fluid mt-3 d-flex justify-content-between">
        <a href="<?php echo esc_url(admin_url('admin-post.php')); ?>?action=delete_lyticswp_settings" onclick="return confirm('Are you sure you want to delete all Lytics related settings? This action cannot be undone.');" class="btn btn-danger">Reset</a>
        <input type="submit" name="submit" value="Save Settings" class="btn btn-primary">
      </div>
    </form>
  </div>
</div>