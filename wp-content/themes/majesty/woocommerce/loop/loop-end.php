<?php
/**
 * Product Loop End
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
?>
</ul>
<?php
global $majesty_options;
$layout = $majesty_options['shortcode_products_query'];
if( ! empty( $layout ) && $majesty_options['vc_woo_filter'] != 'true' ) {
	if( $layout == 'list' || $layout == 'list2' ) {
		
	} elseif( $layout == 'masonry' || $layout == 'masonryfullwidth' || $layout == '3col' || $layout == '4col' ) {
		echo '</div>';
	} elseif( $layout == 'grid' || $layout == 'grid4col' || $layout == 'gridfullwidth' ) {
		echo '</div></div>';
	}
	$majesty_options['shortcode_products_query'] = '';
	$majesty_options['shortcode_masonrry_loop'] = '';
	$majesty_options['vc_woo_filter'] = '';
}
if( $majesty_options['woo_display_cat_loop'] == true ) {
	echo '</div>';
	$majesty_options['woo_display_cat_loop'] = false;
	//echo $majesty_options['default_woocommerce_shop_page_display'];
	//update_option( 'woocommerce_shop_page_display', $majesty_options['default_woocommerce_shop_page_display'] );
	//echo get_option( 'woocommerce_shop_page_display');
}
?>