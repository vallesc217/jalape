<?php
/**
 * Media Trash Button
 *
 * @package    MediaTrashButton
 * @subpackage MediaTrashButton
/*
	Copyright (c) 2020- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$mediatrashbutton = new MediaTrashButton();

/** ==================================================
 * Main Functions
 */
class MediaTrashButton {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'define_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_style' ) );

		add_filter( 'query_vars', array( $this, 'add_query_vars_filter' ) );

	}

	/** ==================================================
	 * Add Script
	 *
	 * @since 1.00
	 */
	public function load_custom_wp_admin_style() {

		global $pagenow;
		if ( 'upload.php' == $pagenow ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'mediatrashbutton-js', plugin_dir_url( __DIR__ ) . 'js/jquery.mediatrashbutton.js', array( 'jquery' ), '1.0.0', false );

			if ( 'trash' == get_query_var( 'attachment-filter' ) ) {
				$button = '<a href="' . admin_url( 'upload.php' ) . '" class="page-title-action">' . __( 'All media items' ) . '</a>';
			} else {
				$button = '<a href="' . admin_url( 'upload.php?mode=list&attachment-filter=trash' ) . '" class="page-title-action">' . __( 'View Trash' ) . '</a>';
			}

			wp_localize_script(
				'mediatrashbutton-js',
				'media_trash',
				array(
					'button' => $button,
				)
			);
		}

	}

	/** ==================================================
	 * Get query
	 *
	 * @param array $vars  vars.
	 * @since 1.01
	 */
	public function add_query_vars_filter( $vars ) {
		$vars[] = 'attachment-filter';
		return $vars;
	}

	/** ==================================================
	 * Define notice
	 *
	 * @since 1.00
	 */
	public function define_notice() {

		global $pagenow;
		if ( 'upload.php' == $pagenow && ! MEDIA_TRASH ) {
			?>
			<div class="notice notice-warning is-dismissible"><ul><li><strong>Media Trash Button</strong>
			<?php
			$wp_config_link = __( 'https://wordpress.org/support/article/editing-wp-config-php/', 'media-trash-button' );
			$wp_config_link_html = '<a style="text-decoration: none;" href="' . $wp_config_link . '" target="_blank" rel="noopener noreferrer">wp-config.php</a>';
			$wp_define_html = '<code>define( &#39;MEDIA_TRASH&#39;, true )&#059;</code>';
			/* translators: %1$s wp-config.php link */
			echo wp_kses_post( sprintf( __( ' : In %1$s, Add the following one line. %2$s', 'media-trash-button' ), $wp_config_link_html, $wp_define_html ) );
			?>
			</div>
			<?php
		}

	}

}


