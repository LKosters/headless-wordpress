<?php
/*
Plugin Name: Headless WordPress
Description: This plugin provides functionalities for using WordPress as a headless CMS.
Version: DEV
Author: Laurens Kosters
*/


// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Routes
require_once plugin_dir_path(__FILE__) . 'routes/page-routes.php';
require_once plugin_dir_path(__FILE__) . 'routes/post-routes.php';

// Includes
require_once plugin_dir_path(__FILE__) . 'includes/close-frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
