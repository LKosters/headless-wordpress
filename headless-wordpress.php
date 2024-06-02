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

// Add a custom REST API endpoint for pages
function headless_wordpress_register_page_routes() {
    register_rest_route('headless/v1', '/page/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'headless_wordpress_get_page_by_slug',
        'args' => array(
            'slug' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));
}
add_action('rest_api_init', 'headless_wordpress_register_page_routes');

// Add a custom REST API endpoint for posts
function headless_wordpress_register_post_routes() {
    register_rest_route('headless/v1', '/post/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'headless_wordpress_get_post_by_slug',
        'args' => array(
            'slug' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));
}
add_action('rest_api_init', 'headless_wordpress_register_post_routes');

// Page by slug
function headless_wordpress_get_page_by_slug($data) {
    $slug = $data['slug'];
    $page = get_page_by_path($slug);

    if ($page) {
        $response = array(
            'ID' => $page->ID,
            'title' => $page->post_title,
            'content' => apply_filters('the_content', $page->post_content),
            'slug' => $page->post_name,
            'status' => $page->post_status,
            'date' => $page->post_date,
        );
        return rest_ensure_response($response);
    } else {
        return new WP_Error('no_page', 'Page not found', array('status' => 404));
    }
}

// Post by slug
function headless_wordpress_get_post_by_slug($data) {
    $slug = $data['slug'];
    $post = get_posts(array(
        'name' => $slug,
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 1
    ));

    if (!empty($post)) {
        $post = $post[0];
        $response = array(
            'ID' => $post->ID,
            'title' => $post->post_title,
            'content' => apply_filters('the_content', $post->post_content),
            'slug' => $post->post_name,
            'status' => $post->post_status,
            'date' => $post->post_date,
        );
        return rest_ensure_response($response);
    } else {
        return new WP_Error('no_post', 'Post not found', array('status' => 404));
    }
}
