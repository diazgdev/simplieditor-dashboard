<?php
/**
* Plugin Name: SimpliEditor Dashboard
* Description: This plugin replaces the WordPress dashboard for 'Editor' users with a custom message and contact information.
* Version: 1.0
* Author: Guillermo Díaz
* Author URI: http://diazg.dev/
*/

function custom_dashboard_page() {
  // Only show for 'editor' users
  if (current_user_can('editor') && !current_user_can('administrator')) {
      // Remove default dashboard menu
      remove_menu_page('index.php');

      // Add a new submenu under DASHBOARD
      add_menu_page( 'Dashboard', 'Dashboard', 'read', 'custom-dashboard', 'custom_dashboard_message', 'dashicons-admin-home', 2 );
  }
}

function custom_dashboard_message() {
  echo "<div style='margin:20px;'>";
  echo "<h1 style='color: #0073aa; font-size: 2.8em;'>Welcome to your custom dashboard</h1>";
  echo "<p style='font-size: 1.2em;'>If you need any assistance with your website such as updates, backups, fixes, or anything else, please feel free to get in touch with us.</p>";
  echo "<p style='font-size: 1.2em;'>Our contact information: </p>";
  echo "<p style='font-size: 1.2em;'>Email: <a href='mailto:support@something.com'>support@something.com</a></p>";
  echo "<p style='font-size: 1.2em;'>Phone: <a href='tel:1234567890'>123-456-7890</a></p>";
  echo "</div>";
}

function redirect_after_login($redirect_to, $requested_redirect_to, $user) {
  if (isset($user->roles) && is_array($user->roles)) {
      if (in_array('editor', $user->roles)) {
          return admin_url('admin.php?page=custom-dashboard');
      } else {
          return admin_url();
      }
  }
  return $redirect_to;
}

function restrict_access_to_dashboard() {
  if (current_user_can('editor') && !current_user_can('administrator')) {
      global $pagenow;
      if ('index.php' == $pagenow) {
          wp_redirect(admin_url('admin.php?page=custom-dashboard'));
          exit;
      }
  }
}

add_action('admin_menu', 'custom_dashboard_page');
add_filter('login_redirect', 'redirect_after_login', 10, 3);
add_action('admin_init', 'restrict_access_to_dashboard');

?>
