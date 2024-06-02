<?php
// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add admin menu item
function headless_wordpress_add_admin_menu() {
    add_menu_page(
        'Headless WordPress Settings',
        'Headless WP',
        'manage_options',
        'headless-wordpress-settings',
        'headless_wordpress_settings_page',
        'dashicons-admin-generic'
    );
}
add_action('admin_menu', 'headless_wordpress_add_admin_menu');

// Register settings
function headless_wordpress_register_settings() {
    // Close Frontend Access
    register_setting('headless_wordpress_settings', 'headless_wordpress_close_frontend');

    // Enable API Routes
    register_setting('headless_wordpress_settings', 'headless_wordpress_enable_api_routes');

    // Enable Posts API Route
    register_setting('headless_wordpress_settings', 'headless_wordpress_enable_posts_api_route');

    // Enable Pages API Route
    register_setting('headless_wordpress_settings', 'headless_wordpress_enable_pages_api_route');

    // Add more options for other types of routes if needed
}
add_action('admin_init', 'headless_wordpress_register_settings');

// Settings page content
function headless_wordpress_settings_page() {
    ?>
    <div class="wrap">
        <h1>Headless WordPress Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('headless_wordpress_settings');
            do_settings_sections('headless_wordpress_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Close Frontend Access</th>
                    <td>
                        <input type="checkbox" name="headless_wordpress_close_frontend" value="1" <?php checked(1, get_option('headless_wordpress_close_frontend'), true); ?> />
                        <label for="headless_wordpress_close_frontend">Close front-end access</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable API Routes</th>
                    <td>
                        <input type="checkbox" name="headless_wordpress_enable_api_routes" value="1" <?php checked(1, get_option('headless_wordpress_enable_api_routes'), true); ?> />
                        <label for="headless_wordpress_enable_api_routes">Enable custom API routes</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Posts API Route</th>
                    <td>
                        <input type="checkbox" name="headless_wordpress_enable_posts_api_route" value="1" <?php checked(1, get_option('headless_wordpress_enable_posts_api_route'), true); ?> />
                        <label for="headless_wordpress_enable_posts_api_route">Enable posts API route</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Pages API Route</th>
                    <td>
                        <input type="checkbox" name="headless_wordpress_enable_pages_api_route" value="1" <?php checked(1, get_option('headless_wordpress_enable_pages_api_route'), true); ?> />
                        <label for="headless_wordpress_enable_pages_api_route">Enable pages API route</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
