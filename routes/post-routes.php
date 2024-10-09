<?php
// Ensure the code is being called from WordPress
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add custom REST API endpoints for posts
function headless_wordpress_register_post_routes() {
    if (get_option('headless_wordpress_enable_api_routes') && get_option('headless_wordpress_enable_posts_api_route')) {
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

/**
 * Register custom REST routes for each custom post type, including ACF fields.
 */
function headless_register_custom_post_type_routes() {
    // Get all registered custom post types
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

    foreach ($post_types as $post_type) {
        // Register a REST route to get all posts of the custom post type
        register_rest_route('headless/v1', '/' . $post_type->name, array(
            'methods' => 'GET',
            'callback' => function($request) use ($post_type) {
                $args = array(
                    'post_type' => $post_type->name,
                    'posts_per_page' => -1,
                );
                $query = new WP_Query($args);

                if (!$query->have_posts()) {
                    return new WP_Error('no_posts', __('No posts found'), array('status' => 404));
                }

                $posts = array();
                while ($query->have_posts()) {
                    $query->the_post();
                    // Get the custom fields using ACF's get_fields()
                    $acf_fields = function_exists('get_fields') ? get_fields(get_the_ID()) : array();

                    $posts[] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'slug' => get_post_field('post_name', get_the_ID()), // Get the slug
                        'link' => get_permalink(),
                        'acf_fields' => $acf_fields // Add ACF fields to the response
                    );
                }
                wp_reset_postdata();

                return rest_ensure_response($posts);
            },
            'permission_callback' => '__return_true',
        ));

        // Register a REST route to get a single post by slug for the custom post type
        register_rest_route('headless/v1', '/' . $post_type->name . '/(?P<slug>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => function($request) use ($post_type) {
                $slug = $request['slug'];

                // Query to get the post by slug
                $args = array(
                    'name' => $slug,
                    'post_type' => $post_type->name,
                    'posts_per_page' => 1,
                );
                $query = new WP_Query($args);

                if (!$query->have_posts()) {
                    return new WP_Error('no_post', __('No post found'), array('status' => 404));
                }

                $query->the_post();
                $acf_fields = function_exists('get_fields') ? get_fields(get_the_ID()) : array();

                $post_data = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'slug' => get_post_field('post_name', get_the_ID()), // Get the slug
                    'link' => get_permalink(),
                    'acf_fields' => $acf_fields // Add ACF fields to the response
                );
                wp_reset_postdata();

                return rest_ensure_response($post_data);
            },
            'permission_callback' => '__return_true',
        ));
    }
}
add_action('rest_api_init', 'headless_register_custom_post_type_routes');