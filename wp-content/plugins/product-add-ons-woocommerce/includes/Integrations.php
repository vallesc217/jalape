<?php

namespace ZAddons;

defined( 'ABSPATH' ) || exit;

class Integrations {

	public function __construct() {
		new Integrations\Bookings();
		new Integrations\QuickView();
	}
}
