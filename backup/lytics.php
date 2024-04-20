<?php
/*
Plugin Name: Lytics
Description: This is a description of your plugin.
Version: 1.0
Author: markhayden
Author URI: https://profiles.wordpress.org/markjhayden
*/

// Register shortcode
function lytics_greeting_shortcode()
{
  return 'Hello, World!';
}
add_shortcode('lytics_greeting', 'lytics_greeting_shortcode');


function fetchAccountDetails($token)
{
  if (empty($token)) {
    return FALSE;
  }

  $url = 'https://api.lytics.io/api/account';
  $args = array(
    'headers' => array(
      'Authorization' => $token,
    ),
  );

  $response = wp_remote_get($url, $args);

  if (is_wp_error($response)) {
    error_log('Failed to fetch account details: ' . $response->get_error_message());
    return FALSE;
  }

  $response_code = wp_remote_retrieve_response_code($response);
  $body = wp_remote_retrieve_body($response);

  if ($response_code === 200) {
    $data = json_decode($body, TRUE);
    if (isset($data['data']) && count($data['data']) > 0 && isset($data['data'][0]['name'], $data['data'][0]['id'], $data['data'][0]['domain'])) {
      return [
        'name' => $data['data'][0]['name'],
        'id' => $data['data'][0]['id'],
        'domain' => $data['data'][0]['domain'],
      ];
    }
  } else {
    error_log('Failed to fetch account details. Response code: ' . $response_code);
    return FALSE;
  }

  return FALSE;
}

// function lytics_register_settings()
// {
//   register_setting('lytics_settings_group', 'lytics_access_token');
//   register_setting('lytics_settings_group', 'lytics_enable_tag');
//   register_setting('lytics_settings_group', 'lytics_debug_mode');
//   register_setting('lytics_settings_group', 'lytics_ignore_admin_users');
//   register_setting('lytics_settings_group', 'lytics_tag_config');
//   register_setting('lytics_settings_group', 'lytics_account_name');
//   register_setting('lytics_settings_group', 'lytics_account_id');
//   register_setting('lytics_settings_group', 'lytics_aid');
//   register_setting('lytics_settings_group', 'lytics_domain');
// }
// add_action('admin_init', 'lytics_register_settings');

// // Callback function to handle form submission
// function lytics_handle_form_submission()
// {
//   if (!current_user_can('manage_options')) {
//     wp_die('Unauthorized user');
//   }

//   // If that we have an access token
//   if (empty($_POST['access_token'])) {
//     wp_redirect(add_query_arg('error', 'Access Token is required.', admin_url('admin.php?page=lytics_settings')));
//     exit;
//   }

//   // Validate that access token is valid
//   $accountDetails = fetchAccountDetails($_POST['access_token']);
//   if (!$accountDetails) {
//     wp_redirect(add_query_arg('error', 'Invalid Access Token.', admin_url('admin.php?page=lytics_settings')));
//     exit;
//   }

//   // Update settings
//   update_option('lytics_access_token', $_POST['access_token']);
//   update_option('lytics_account_name', $accountDetails['name']);
//   update_option('lytics_account_id', $accountDetails['id']);
//   update_option('lytics_aid', $accountDetails['aid']);
//   update_option('lytics_domain', $accountDetails['domain']);
//   update_option('lytics_enable_tag', isset($_POST['enable_tag']) ? 1 : 0);
//   update_option('lytics_debug_mode', isset($_POST['debug_mode']) ? 1 : 0);
//   update_option('lytics_ignore_admin_users', isset($_POST['ignore_admin_users']) ? 1 : 0);
//   update_option('lytics_tag_config', $_POST['tag_config']);

//   // Redirect back to the settings page
//   wp_redirect(admin_url('admin.php?page=lytics_settings'));
//   exit;
// }
// add_action('admin_post_lytics_process_form', 'lytics_handle_form_submission');

















// Add plugin configuration page
function lytics_settings_page()
{

  // get query param value for error if it exist
  $error = isset($_GET['error']) ? $_GET['error'] : '';
  if ($error) {
    add_settings_error('lytics_core', 'lytics_access_token', $error, 'error');
  }
  settings_errors('lytics_core');

  $lytics_access_token = get_option('lytics_access_token');
  $lytics_account_name = get_option('lytics_account_name');
  $lytics_account_id = get_option('lytics_account_id');
  $lytics_aid = get_option('lytics_aid');
  $lytics_domain = get_option('lytics_domain');
  $lytics_enabled = get_option('lytics_enable_tag');
  $lytics_debug_mode = get_option('lytics_debug_mode');
  $lytics_ignore_admin_users = get_option('lytics_ignore_admin_users');
  $lytics_tag_config = get_option('lytics_tag_config');

  // if token is valid non empty string
  if ($lytics_access_token) {
    $hasValidToken = true;
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

      var jsonEditor = CodeMirror.fromTextArea(document.getElementById("jsonInput"), {
        mode: "application/json",
        lineNumbers: true,
        autoCloseBrackets: true,
        matchBrackets: true,
        theme: "monokai"
      });
    });
  </script>

  <div class="container-fluid p-3">
    <h2>Lytics Settings</h2>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <?php settings_fields('lytics_core'); ?>

      <input type="hidden" name="action" value="lytics_process_form">

      <!-- Credentials -->
      <div class="container-fluid mt-4 border shadow-sm p-0">
        <div class="container-fluid bg-secondary pt-2 pb-2 mb-2" style="--bs-bg-opacity: .2;">
          <h5 class="p-0 m-0">Credentials</h5>
        </div>
        <div class="container-fluid">
          <div class="mb-3">
            <label for="access_token" class="form-label">Access Token</label>
            <input type="text" class="form-control" id="access_token" name="access_token" value="<?php echo esc_attr($lytics_access_token); ?>">
            <p class="pt-2" style="font-size:12px;">Enter Lytics Access Token. Additional guidance on creating and managing Lytics Access Tokens is available in our documentation.
            </p>
          </div>
        </div>
      </div>

      <!-- Account Details -->
      <div class="container-fluid mt-4 border shadow-sm p-0<?= $hasValidToken ? '' : ' d-none' ?>">
        <div class="container-fluid bg-secondary pt-2 pb-2 mb-2" style="--bs-bg-opacity: .2;">
          <h5 class="p-0 m-0">Account Details</h5>
        </div>
        <div class="container-fluid">
          <div class="mb-3">
            <label for="account_name" class="form-label">Account Name</label>
            <input type="text" class="form-control" id="account_name" disabled name="account_name" value="<?php echo esc_attr($lytics_account_name); ?>">
          </div>
          <div class="mb-3">
            <label for="account_id" class="form-label">Account ID</label>
            <input type="text" class="form-control" id="account_id" disabled name="account_id" value="<?php echo esc_attr($lytics_account_id); ?>">
          </div>
          <div class="mb-3">
            <label for="aid" class="form-label">Account ID</label>
            <input type="text" class="form-control" id="aid" disabled name="aid" value="<?php echo esc_attr($lytics_aid); ?>">
          </div>
          <div class="mb-3">
            <label for="domain" class="form-label">Domain</label>
            <input type="text" class="form-control" id="domain" disabled name="domain" value="<?php echo esc_attr($lytics_domain); ?>">
          </div>
        </div>
      </div>

      <!-- Configuration -->
      <div class="container-fluid mt-4 border shadow-sm p-0<?= $hasValidToken ? '' : ' d-none' ?>">
        <div class="container-fluid bg-secondary pt-2 pb-2 mb-3" style="--bs-bg-opacity: .2;">
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
            <input type="checkbox" id="edit-debug-mode" style="width: 1rem; height: 1rem;" class="me-3" name="debug_mode" <?php echo ($lytics_debug_mode == 1) ? 'checked' : ''; ?>>
            <div>
              <label for="edit-debug-mode">Enable Debug Mode</label>
              <p class="mb-0" style="font-size: 12px;">Enable debug mode for extra logging and non-minified Lytics tag.</p>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" id="edit-ignore-admin-users" style="width: 1rem; height: 1rem;" class="me-3" name="ignore_admin_users" <?php echo ($lytics_ignore_admin_users == 1) ? 'checked' : ''; ?>>
            <div>
              <label for="edit-ignore-admin-users">Ignore Admin Users</label>
              <p class="mb-0" style="font-size: 12px;">When activated Lytics will not be installed for users who are actively logged in to Drupal. This may prevent testing of personalization and recommendation features but also may prevent skewing of analytics data.
              </p>
            </div>
          </div>
          <div class="mb-3 form-group">
            <label for="jsonInput">Additional Tag Configuration</label>
            <textarea id="jsonInput" class="form-control" rows="5" name="tag_config"><?php echo stripslashes($lytics_tag_config); ?></textarea>
          </div>
        </div>
      </div>
      <input type="submit" name="submit" value="Save Settings" class="btn btn-primary">
    </form>
  </div>
<?php
}

// // Add link to configuration page in admin menu
// function lytics_add_menu()
// {
//   add_menu_page('Lytics', 'Lytics', 'manage_options', 'lytics_settings', 'lytics_settings_page');
//   add_submenu_page('lytics_settings', 'Settings', 'Settings', 'manage_options', 'lytics_settings', 'lytics_settings_page');
// }
// add_action('admin_menu', 'lytics_add_menu');

// function lytics_plugin_settings_link($links)
// {
//   $settings_link = '<a href="admin.php?page=lytics_settings">Settings</a>'; // Corrected URL
//   array_unshift($links, $settings_link);
//   return $links;
// }
// $plugin = plugin_basename(__FILE__);
// add_filter("plugin_action_links_$plugin", 'lytics_plugin_settings_link');

// // Enqueue Bootstrap and CodeMirror CSS and JavaScript
// function enqueue_admin_styles_and_scripts()
// {
//   // Bootstrap CSS
//   wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

//   // CodeMirror CSS
//   wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css');
//   wp_enqueue_style('codemirror-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css');

//   // Bootstrap JS
//   wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js');

//   // CodeMirror JS
//   wp_enqueue_script('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js');
//   wp_enqueue_script('codemirror-json-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/javascript/javascript.min.js');
// }

// add_action('admin_enqueue_scripts', 'enqueue_admin_styles_and_scripts');
