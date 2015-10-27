<?php
/**
 * Better REST API Featured Images
 *
 * @package             Better_REST_API_Featured_Images
 * @author              Braad Martin <wordpress@braadmartin.com>
 * @license             GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:         Better REST API Featured Images
 * Plugin URI:          https://wordpress.org/plugins/better-rest-api-featured-images/
 * Description:         Enhances the featured image data returned on the post object by the REST API to include urls for all available sizes and other useful image data.
 * Version:             1.0.1
 * Author:              Braad Martin
 * Author URI:          http://braadmartin.com
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         better-rest-api-featured-images
 * Domain Path:         /languages
 */

add_action( 'init', 'better_rest_api_featured_images_init', 12 );
/**
 * Register our enhanced better_featured_image field to all public post types
 * that support post thumbnails.
 *
 * @since  1.0.0
 */
function better_rest_api_featured_images_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
		$supports_thumbnail = post_type_supports( $post_type_name, 'thumbnail' );

		// Only proceed if the post type is set to be accessible over the REST API
		// and supports featured images.
		if ( $show_in_rest && $supports_thumbnail ) {

			register_api_field( $post_type_name,
				'better_featured_image',
				array(
					'get_callback' => 'better_rest_api_featured_images_get_field',
					'schema'       => null,
				)
			);
		}
	}
}

/**
 * Return the better_featured_image field.
 *
 * @since   1.0.0
 *
 * @return  object|0
 */
function better_rest_api_featured_images_get_field( $object, $field_name, $request ) {

	// Only proceed if the post has a featured image.
	if ( $object['featured_image'] ) {
		$image_id = (int)$object['featured_image'];
	} else {
		return null;
	}

	$image = get_post( $image_id );

	if ( ! $image ) {
		return null;
	}

	// This is taken from WP_REST_Attachments_Controller::prepare_item_for_response().
	$featured_image['id']            = $image_id;
	$featured_image['alt_text']      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$featured_image['caption']       = $image->post_excerpt;
	$featured_image['description']   = $image->post_content;
	$featured_image['media_type']    = wp_attachment_is_image( $image_id ) ? 'image' : 'file';
	$featured_image['media_details'] = wp_get_attachment_metadata( $image_id );
	$featured_image['post']          = ! empty( $image->post_parent ) ? (int) $image->post_parent : null;
	$featured_image['source_url']    = wp_get_attachment_url( $image_id );

	if ( empty( $featured_image['media_details'] ) ) {
		$featured_image['media_details'] = new stdClass;
	} elseif ( ! empty( $featured_image['media_details']['sizes'] ) ) {
		$img_url_basename = wp_basename( $featured_image['source_url'] );
		foreach ( $featured_image['media_details']['sizes'] as $size => &$size_data ) {
			$image_src = wp_get_attachment_image_src( $image_id, $size );
			if ( ! $image_src ) {
				continue;
			}
			$size_data['source_url'] = $image_src[0];
		}
	} else {
		$featured_image['media_details']['sizes'] = new stdClass;
	}

	return $featured_image;
}
