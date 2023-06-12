<?php
/**
* Plugin Name: SimpliEditor Dashboard
* Description: This plugin replaces the WordPress dashboard for 'Editor' users with a custom message and contact information.
* Version: 1.0
* Author: Guillermo Díaz
* Author URI: http://diazg.dev/
*/

function custom_dashboard_page() {
  if (current_user_can('editor') && !current_user_can('administrator')) {
    remove_menu_page('index.php');
    global $my_plugin_page;
    $my_plugin_page = add_menu_page( 'Dashboard',
                 'Dashboard',
                 'read',
                 'custom-dashboard',
                 'custom_dashboard_message',
                 'dashicons-admin-home', 2 );
  }
}

function custom_dashboard_message() {
  if(isset($_POST['submit_contact_form'])) {
      process_contact_form();
  } else {
      display_dashboard_info();
  }
}

function display_dashboard_info() {
  $current_user = wp_get_current_user();
  $user_name = esc_html( $current_user->user_login );
  $user_email = esc_html( $current_user->user_email );

  $site_name = get_bloginfo('name');
  echo "<div class='custom-dashboard'>";
  echo "<h1>Welcome to {$site_name}</h1>";
  echo "<p>If you need any assistance with your website, please use the form below to get in touch with us:</p>";

  render_contact_form($user_name, $user_email);

  render_faqs();

  render_contact_info();

  echo "</div>";
}

function render_contact_form($user_name, $user_email) {
  echo "<div class='contact-form'>";
  echo '<form method="post">
      <input type="text" id="name" name="name" value="'. $user_name .'" required><br>
      <input type="email" id="email" name="email" value="'. $user_email .'" required><br>
      <textarea id="message" name="message" rows="4" cols="50" placeholder="Message" required></textarea><br>
      <input type="submit" name="submit_contact_form" value="Submit">
  </form>';
  echo "</div>";
}

function render_faqs() {
    echo "<h2>FAQs</h2>";
    echo "<ul>
        <li><strong>How do I update my website content?</strong><br>You can update your posts from <a href='". get_admin_url(null, 'edit.php') ."'>here</a> or your pages from <a href='". get_admin_url(null, 'edit.php?post_type=page') ."'>here</a>.</li>
        <li><strong>What do I do if my website goes down?</strong><br>Contact us immediately via email or WhatsApp and we will resolve the issue as soon as possible.</li>
        <li><strong>How can I add more functionality to my website?</strong><br>Just get in touch with us to discuss your needs. We are always ready to help you make the most of your website.</li>
    </ul>";
}

function render_contact_info() {
  echo "<h2>Contact Information</h2>";
  echo "<table>
      <tr>
          <td>Website:</td>
          <td><a href='https://website.com'>website.com</a></td>
      </tr>
      <tr>
          <td>Email:</td>
          <td><a href='mailto:info@website.com'>info@website.com</a></td>
      </tr>
      <tr>
          <td>WhatsApp:</td>
          <td><a href='https://wa.me/yourwhatsappnumber'>+1 234 567 8901</a></td>
      </tr>
  </table>";
}

function process_contact_form() {
  $name = sanitize_text_field($_POST['name']);
  $email = sanitize_email($_POST['email']);
  $message = sanitize_textarea_field($_POST['message']);
  $site_name = get_bloginfo('name');

  $to = 'your-email@example.com';
  $subject = "New contact request from {$site_name}";
  $headers = 'From: '. $email . "\r\n" .
  'Reply-To: ' . $email . "\r\n";

  if(wp_mail($to, $subject, $message, $headers)) {
      echo "<p class='success'>Thanks for contacting us! We'll get back to you soon.</p>";
  } else {
      echo "<p class='error'>Sorry, an error occurred. Please try again.</p>";
  }
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

function load_custom_dashboard_styles($hook) {
  global $my_plugin_page;

  if($hook != $my_plugin_page)
    return;

  wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Quicksand:400,700', array(), null);

  wp_enqueue_style('custom_dashboard_styles', plugin_dir_url(__FILE__) . 'custom_dashboard_styles.css');
}

add_action('admin_enqueue_scripts', 'load_custom_dashboard_styles');
add_action('admin_menu', 'custom_dashboard_page');
add_filter('login_redirect', 'redirect_after_login', 10, 3);
add_action('admin_init', 'restrict_access_to_dashboard');

?>
