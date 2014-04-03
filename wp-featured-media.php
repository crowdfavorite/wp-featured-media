<?php

/*
Plugin Name: Featured Media
Description: Simple library to add featured support for different media types
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
Version: 1.0
*/

load_plugin_textdomain('cffm');

if (class_exists('cf_meta')) {
function cffm_meta_config($config) {
	$config[] = array(
		'title' => __('Featured Video', 'cffm'),
		'description' => __('Paste the source URL of the video you would like to be featured', 'cffm'),
		'type' => apply_filters('cffm_post_types', array('post')),
		'id' => 'featured-video',
		'add_to_srotables' => true,
		'context' => 'side',
		'items' => array(
			array(
				'name' => '_cf_featured_video_url',
				'label' => '',
				'type' => 'text',
				'label_position' => 'before'
			),
		),
	);

	return $config;
}
add_action('cf_meta_config', 'cffm_meta_config', 11);
}
else {
	// @TODO build out meta box and save process manually
}

function cffm_featured_media($post_id) {
	global $content_width;
	$video_url = trim(get_post_meta($post_id, '_cf_featured_video_url', true));


	if (!empty($video_url) && is_single()) {
		$vid_width = empty($content_width) ? '990' : $content_width;
		$vid_width = apply_filters('cffm_video_width', $content_width, $post_id);
		$vid_height = apply_filters('cffm_video_height', null, $post_id);

		$attrs = array(
			'src' => esc_url($video_url),
			'width' => esc_attr($vid_width),
			'height' => esc_attr($vid_height),
			'frameborder' => '0',
			'seamless' => 'seamless',
		);
		$attr_string = '';
		foreach ($attrs as $key => $value) {
			if (!is_null($value)) {
				$attr_string .= $key.'="'.$value.'" ';
			}
		}
		//@TODO look into oembed support, this was built for a player which does not suppose oembed
		return apply_filters('cffm_video_markup', '<iframe '.$attr_string.' allowfullscreen>'.__('Your browser does not support iframes.', 'cffm').'</iframe>', $post_id, $attrs);
	}
	else if (has_post_thumbnail($post_id)) {
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$thumbnail_url = wp_get_attachment_url($post_thumbnail_id);
		$img_size = apply_filters('cffm_image_size', 'large', $post_id, $post_thumnail_id);

		$img = wp_get_attachment_image($post_thumbnail_id, $img_size);

		return apply_filters('cffm_image_markup', '
		<figure class="single-featured-image">
			'.$img.'
		</figure>', $post_id, $img, $post_thumbnail_id, $thumbnail_url);

	}

	return false;
}
