<?php
// Ensure the code is being called from WordPress
if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

// Add a custom REST API endpoint for pages
function headless_wordpress_register_page_routes()
{
    if (
        get_option("headless_wordpress_enable_api_routes") &&
        get_option("headless_wordpress_enable_pages_api_route")
    ) {
        register_rest_route("headless/v1", "/page/(?P<slug>[a-zA-Z0-9-]+)", [
            "methods" => "GET",
            "callback" => "headless_wordpress_get_page_by_slug",
            "args" => [
                "slug" => [
                    "required" => true,
                    "validate_callback" => function ($param, $request, $key) {
                        return is_string($param);
                    },
                ],
            ],
        ]);
    }
}
add_action("rest_api_init", "headless_wordpress_register_page_routes");

// Callback function to get page by slug
function headless_wordpress_get_page_by_slug($data)
{
    $slug = $data["slug"];
    $page = get_page_by_path($slug);

    if ($page) {
        // Check if the page uses WP Bakery Page Builder
        $content = $page->post_content;
        if (
            has_shortcode($content, "vc_row") ||
            has_shortcode($content, "vc_column")
        ) {
            $content = do_shortcode($content); // Execute shortcodes for WP Bakery content
        } else {
            $content = apply_filters("the_content", $content); // Default WordPress content filter
        }

        $response = [
            "ID" => $page->ID,
            "title" => $page->post_title,
            "content" => $content,
            "slug" => $page->post_name,
            "status" => $page->post_status,
            "date" => $page->post_date,
        ];
        return rest_ensure_response($response);
    } else {
        return new WP_Error("no_page", "Page not found", ["status" => 404]);
    }
}
