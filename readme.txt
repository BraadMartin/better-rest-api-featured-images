=== Better REST API Featured Images ===
Contributors: Braad
Donate link: http://braadmartin.com/
Tags: featured, images, post, thumbnail, rest, api, better
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhances the featured image data returned on the post object by the REST API to include urls for all available sizes and other useful image data.

== Description ==

The REST API returns a `featured_image` field on the post object by default, but this field is simply the image ID.

This plugin adds a `better_featured_image` field to the post object that contains the available image sizes and urls, allowing you to get this information without making a second request.

It takes this:

`
"featured_image": 13,
`

And turns it into this:

`
"featured_image": 13,
"better_featured_image": {
    "id": 13,
    "alt_text": "Hot Air Balloons",
    "caption": "The event featured hot air balloon rides",
    "description": "The hot air balloons from the big event",
    "media_type": "image",
    "media_details": {
      "width": 5760,
      "height": 3840,
      "file": "2015/09/balloons.jpg",
      "sizes": {
        "thumbnail": {
          "file": "balloons-150x150.jpg",
          "width": 150,
          "height": 150,
          "mime-type": "image/jpeg",
          "source_url": "http://api.example.com/wp-content/uploads/2015/09/balloons-150x150.jpg"
        },
        "medium": {
          "file": "balloons-300x200.jpg",
          "width": 300,
          "height": 200,
          "mime-type": "image/jpeg",
          "source_url": "http://api.example.com/wp-content/uploads/2015/09/balloons-300x200.jpg"
        },
        "large": {
          "file": "balloons-1024x683.jpg",
          "width": 1024,
          "height": 683,
          "mime-type": "image/jpeg",
          "source_url": "http://api.example.com/wp-content/uploads/2015/09/balloons-1024x683.jpg"
        },
        "post-thumbnail": {
          "file": "balloons-825x510.jpg",
          "width": 825,
          "height": 510,
          "mime-type": "image/jpeg",
          "source_url": "http://api.example.com/wp-content/uploads/2015/09/balloons-825x510.jpg"
        }
      },
      "image_meta": {
        "aperture": 6.3,
        "credit": "",
        "camera": "Canon EOS 5D Mark III",
        "caption": "",
        "created_timestamp": 1433110262,
        "copyright": "",
        "focal_length": "50",
        "iso": "100",
        "shutter_speed": "0.004",
        "title": "",
        "orientation": 1
      }
    },
    "post": null,
    "source_url": "http://api.example.com/wp-content/uploads/2015/09/balloons.jpg"
},
`

The format of the response is nearly identical to what you would get sending a request to `/wp-json/wp/v2/media/13`. When no featured image has been set on the post the `better_featured_image` field will have a value of `null`.

I've done some basic performance tests that indicate the difference in response times with and without this plugin to be about 10-15ms for a collection of 10 posts and 0-5ms for a single post. For me this is much faster than making a second request to `/media/`, especially for multiple posts.

This plugin is on [on Github](https://github.com/BraadMartin/better-rest-api-featured-images "Better REST API Featured Images") and pull requests are always welcome. :)

== Installation ==

= Manual Installation =

1. Upload the entire `/better-rest-api-featured-images` directory to the `/wp-content/plugins/` directory.
1. Activate 'Better REST API Featured Images' through the 'Plugins' menu in WordPress.

= Better Installation =

1. Go to Plugins > Add New in your WordPress admin and search for 'Better REST API Featured Images'.
1. Click Install.

== Frequently Asked Questions ==

= How does it work? =

The WP REST API includes a filter on the response data it returns, and this plugin uses that filter to add a new field `better_featured_image` with the extra data for the featured image.

= When does the plugin load? =

The plugin loads on `init` at priority 12, in order to come after any custom post types have been registered.

= Why doesn't the plugin replace the default `featured_image` field? =

The `featured_image` field is a core field, and other applications might expect it to always be an integer value. To avoid any issues, this plugin includes the extra data under the `better_featured_image` field name.

== Changelog ==

= 1.0.1 =
* Switch to returning null instead of 0 when no featured image is present

= 1.0.0 =
* First Release

== Upgrade Notice ==

= 1.0.1 =
* Switch to returning null instead of 0 when no featured image is present

= 1.0.0 =
* First Release
