<?php
add_action( 'tgmpa_register', 'sama_majesty_register_required_plugins' );

function sama_majesty_register_required_plugins() {

    $plugins = array(
		
		array(
            'name'      => 'CMB2',
            'slug'      => 'cmb2',
            'required'  => true,
        ),
		array(
            'name'      => 'Redux Framework',
            'slug'      => 'redux-framework',
            'required'  => true,
        ),
		array(
            'name'      => 'Team member',
            'slug'      => 'our-team-by-woothemes',
            'required'  => false,
        ),
		array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
		array(
            'name'      => 'WooSidebars',
            'slug'      => 'woosidebars',
            'required'  => false,
        ),
		array(
            'name'      => 'WooCommerce - excelling eCommerce',
            'slug'      => 'woocommerce',
            'required'  => false,
        ),
		array(
            'name'      => 'Restaurant Reservations',
            'slug'      => 'restaurant-reservations',
            'required'  => false,
        ),
		array(
            'name'      => 'Events Manager',
            'slug'      => 'events-manager',
            'required'  => false,
        ),
		array(
            'name'      => 'Max Mega Menu',
            'slug'      => 'megamenu',
            'required'  => false,
        ),
		array(
            'name'               => 'Majesty Shortcodes',
            'slug'               => 'majesty-shortcodes',
            'source'             => get_stylesheet_directory() . '/includes/plugins/majesty-shortcodes.zip',
            'required'           => false,
            'version'            => '1.0',
            'force_activation'   => false,
            'force_deactivation' => false,
            'external_url'       => '',
        ),
		array(
            'name'               => 'WPBakery Page Builder for WordPress',
            'slug'               => 'js_composer',
            'source'             => get_stylesheet_directory() . '/includes/plugins/js_composer.zip',
            'required'           => true,
			'version'           => '6.0.4',
            'force_activation'   => false,
            'force_deactivation' => false,
            'external_url'       => get_stylesheet_directory() . '/includes/plugins/js_composer.zip',
        ),
		
		array(
            'name'               => 'Envato WordPress Toolkit',
            'slug'               => 'envato-wordpress-toolkit-master',
            'source'             => get_stylesheet_directory() . '/includes/plugins/envato-wordpress-toolkit-master.zip',
            'required'           => false,
            'force_activation'   => false,
            'force_deactivation' => false,
            'external_url'       => '',
        ),
		array(
            'name'               => 'WP FullCalendar',
            'slug'               => 'wp-fullcalendar',
            'required'           => false,

        ),
    );
    tgmpa( $plugins );
}