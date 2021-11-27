<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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
?>
<?php

global $majesty_options;
$layout = $majesty_options['shortcode_products_query'];
if( ! empty( $layout ) && $majesty_options['vc_woo_filter'] != 'true' ) {
	if( $layout == 'list' || $layout == 'list2' ) {
		
	} elseif( $layout == '3col' ) {
		echo '<div class="woocommerce-columns woocommerce-3columns">';
	} elseif( $layout == '4col' ) {
		echo '<div class="woocommerce-columns woocommerce-4columns">';
	} elseif( $layout == 'masonry' ) {
		echo '<div class="text-center masonry_menu masonry_columm menu-items masonry-items">';
	} elseif( $layout == 'masonryfullwidth' ) {
		echo '<div class="text-center masonry_menu masonry_columm_full menu-items masonry-items">';
	} elseif( $layout == 'grid' || $layout == 'grid4col' || $layout == 'gridfullwidth' ) {
		if( $layout == 'grid4col' ) {
			echo '<div class="menu_grid our-menu text-center shop-grid-4col"><div class="menu-type">';
		} else {
			echo '<div class="menu_grid our-menu text-center"><div class="menu-type">';
		}
	}
}
if( $majesty_options['woo_display_cat_loop'] == true ) {
	echo '<div class="theme-woo-categories woocommerce-columns woocommerce-'. absint( $majesty_options['woo_cat_per_row'] ) .'columns">';
}
?>
<ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
