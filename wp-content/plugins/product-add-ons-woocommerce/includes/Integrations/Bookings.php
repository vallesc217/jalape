<?php

namespace ZAddons\Integrations;

defined( 'ABSPATH' ) || exit;

class Bookings {

	public function __construct() {
		if ( class_exists( '\WC_Bookings', false ) ) {
			add_action( 'woocommerce_before_booking_form', array( $this, 'add_compatibility_script' ) );
		}
	}

	public function add_compatibility_script() {
		?>
		<script>
			jQuery(document).ready(function () {
				jQuery(document).ajaxSuccess(function (event, request, settings) {
					var data = settings.data ? settings.data.split('&').reduce(function(map, item) {
						item = item.split('=');
						map[item[0]] = item[1];
						return map;
					}, {}) : {};
					if ('wc_bookings_calculate_costs' === data.action) {
						jQuery('#zaddon_base_price').val(parseFloat(jQuery('.wc-bookings-booking-cost .amount bdi').clone().children().remove().end().text())).trigger('change');
					}
				});
			});
		</script>
		<?php
	}
}
