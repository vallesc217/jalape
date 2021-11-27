<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $majesty_options;
$shortocde_layout = $majesty_options['shortcode_products_query'];
$shop_layout = $majesty_options['shop_type'];
if( ( $shortocde_layout == 'list2' || $shortocde_layout == '3col' || $shortocde_layout == '4col') || ( ( is_shop() || is_product_category() || is_product_tag() ) && ( $shop_layout == 'list2' || $shop_layout == 'list2sidebar' || $shop_layout == '4col' || $shop_layout == '3col' || $shop_layout == '3colwithsidebar' ) ) ) {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
			sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() )
			),
		$product, $args );
	} else {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
			sprintf( '<a href="%s" data-quantity="%s" class="%s" %s><i class="fa fa-shopping-cart"></i></a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button btn btn-gold' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() )
			),
		$product, $args );
		
	}