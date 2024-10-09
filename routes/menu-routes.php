<?php
// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register custom REST routes to get all WordPress menus and their items.
 */
function headless_register_menu_routes() {
    // Register a REST route to get all menus
    register_rest_route('headless/v1', '/menus', array(
        'methods' => 'GET',
        'callback' => 'headless_get_menus',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'headless_register_menu_routes');

/**
 * Callback function to get all WordPress menus and their items.
 */
function headless_get_menus() {
    // Get all menus
    $menus = wp_get_nav_menus();

    if (empty($menus)) {
        return new WP_Error('no_menus', __('No menus found'), array('status' => 404));
    }

    // Prepare array to hold menus and their items
    $all_menus = array();

    foreach ($menus as $menu) {
        // Get all menu items for the current menu
        $menu_items = wp_get_nav_menu_items($menu->term_id);

        // Prepare menu data
        $menu_data = array(
            'id' => $menu->term_id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'items' => array()
        );

        // Add each menu item with its details
        if (!empty($menu_items)) {
            foreach ($menu_items as $item) {
                $menu_data['items'][] = array(
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'slug' => sanitize_title($item->title),
                    'parent' => $item->menu_item_parent,  // Parent item ID if any
                );
            }
        }

        // Add the menu to the list
        $all_menus[] = $menu_data;
    }

    return rest_ensure_response($all_menus);
}