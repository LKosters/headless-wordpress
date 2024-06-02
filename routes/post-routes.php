<?php
// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add a custom REST API endpoint for posts by slug
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

    register_rest_route('headless/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'headless_wordpress_get_all_posts',
        'args' => array(
            'number' => array(
                'required' => false,
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                },
                'default' => 10,
            ),
        ),
    ));
}
add_action('rest_api_init', 'headless_wordpress_register_post_routes');

// Callback function to get post by slug
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

// Callback function to get all posts with a limit
function headless_wordpress_get_all_posts($data) {
    $number = (int) $data['number'];
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => $number
    ));

    if (!empty($posts)) {
        $response = array();
        foreach ($posts as $post) {
            $response[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'content' => apply_filters('the_content', $post->post_content),
                'slug' => $post->post_name,
                'status' => $post->post_status,
                'date' => $post->post_date,
            );
        }
        return rest_ensure_response($response);
    } else {
        return new WP_Error('no_posts', 'No posts found', array('status' => 404));
    }
}
