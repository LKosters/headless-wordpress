<?php
// Ensure the code is being called from WordPress
if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

// Register a new REST API route for processing shortcodes in content
function headless_wordpress_register_shortcode_processor_route()
{
    register_rest_route("headless/v1", "/process-shortcodes", [
        "methods" => "POST",
        "callback" => "headless_wordpress_process_shortcodes",
        "permission_callback" => "__return_true", // Adjust permissions as needed
        "args" => [
            "content" => [
                "required" => true,
                "validate_callback" => function ($param, $request, $key) {
                    return is_string($param);
                },
            ],
        ],
    ]);
}
add_action(
    "rest_api_init",
    "headless_wordpress_register_shortcode_processor_route"
);

// Callback function to process shortcodes
function headless_wordpress_process_shortcodes($data)
{
    // Get the content from the request
    $content = $data["content"];

    // Apply do_shortcode to the content
    $processed_content = do_shortcode($content);

    // Return the processed content as a response
    return rest_ensure_response([
        "original_content" => $content,
        "processed_content" => $processed_content,
    ]);
}
