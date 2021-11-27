<?php
/*------------------------------------------------------------------------------
Plugin Name: Majesty Import Demo Content
Plugin URI: http://eye-themes.com/
Description: Allow you to import akhbaar wordpress theme demo content to your wordpress site.
Version: 1.0
Author: Mostafa Abdallah
Author URI: http://themeforest.net/user/eye-themes
License: GNU General Public License version 3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
text domain: eye-akhbaar-demo-content
------------------------------------------------------------------------------*/
/*
ob_start();
			require( $located );
			$content = ob_get_clean();
			return $content;
*/
define('Majesty_DEMO_CONTENT_VERSION', '1.0.0');
define('Majesty_DEMO_CONTENT_URI', plugins_url( '', __FILE__) );
if ( is_admin() ) {
	require_once('admin/options.php');
	require_once('classes/import-demo-content.php');
}



$filename = Majesty_DEMO_CONTENT_URI . '/uploads/s-launch-7.jpg';
//echo $filename;
# http://localhost/majestyxml/wp-content/plugins/majesty-import-demo/uploads/s-launch-7.jpg

function insert_image( $image_name ) {
	
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC . '/class-http.php' );
	}
	
	$http = new WP_Http();
	$url = Majesty_DEMO_CONTENT_URI . '/uploads/' . $image_name;
	$response = $http->request( $url );
	if( $response['response']['code'] != 200 ) {
		return false;
	}
	$upload = wp_upload_bits( basename($url), null, $response['body'] );
	if( !empty( $upload['error'] ) ) {
		return false;
	}
	$file_path = $upload['file'];
	$file_name = basename( $file_path );
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir = wp_upload_dir();
	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	// Create the attachment
	$attach_id = wp_insert_attachment( $post_info, $file_path );
	if( ! function_exists( 'wp_generate_attachment_metadata' ) ) {	
		// Include image.php
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	return $attach_id;
	
}
//add_action('init', 'add_image');
function add_image() {
	$id = insert_image('s-launch-7.jpg');
	echo $id;
}
function crb_insert_attachment_from_url($url, $parent_post_id = null) {
	if( !class_exists( 'WP_Http' ) )
		include_once( ABSPATH . WPINC . '/class-http.php' );
	$http = new WP_Http();
	$response = $http->request( $url );
	if( $response['response']['code'] != 200 ) {
		return false;
	}
	$upload = wp_upload_bits( basename($url), null, $response['body'] );
	if( !empty( $upload['error'] ) ) {
		return false;
	}
	$file_path = $upload['file'];
	$file_name = basename( $file_path );
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir = wp_upload_dir();
	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	// Create the attachment
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
	// Include image.php
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	return $attach_id;
}