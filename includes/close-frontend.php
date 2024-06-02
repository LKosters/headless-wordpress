<?php
// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to block front-end access and redirect to login page
function headless_wordpress_close_frontend() {
    if (get_option('headless_wordpress_close_frontend')) {
        if (!is_admin() && !defined('DOING_AJAX')) {
            // Redirect to login page if not logged in
            if (!is_user_logged_in()) {
                wp_redirect(wp_login_url());
                exit;
            }
        }
    }
}
add_action('template_redirect', 'headless_wordpress_close_frontend');
