<?php

namespace ZAddons\Integrations;

defined( 'ABSPATH' ) || exit;

class QuickView {

	public function __construct() {
		if ( defined( 'YITH_WCQV' ) ) {
			add_filter( 'za_force_enqueue_product_scripts', array( $this, 'force_enqueue_product_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	public function force_enqueue_product_scripts(): bool {
		return true;
	}

	public function enqueue_scripts() {
		wp_add_inline_script( 'za_product.js', 'jQuery(document).on("qv_loader_stop", zaProductInit)' );
	}
}
