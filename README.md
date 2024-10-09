# Headless WordPress (This plugin is still in development)

## API routes

### WP post types

#### Retrieving a Page by Slug
GET http://example.com/wp-json/headless/v1/page/sample-page

#### Retrieving a Post by Slug
GET http://example.com/wp-json/headless/v1/post/sample-post

### Custom Post Types

#### All of post type
GET http://example.com/wp-json/headless/v1/{custom_post_type}

#### Single post of post type by slug
GET http://example.com/wp-json/headless/v1/{custom_post_type}/{slug}

### Menus

#### Get all menus

GET http://example.com/wp-json/headless/v1/menus
