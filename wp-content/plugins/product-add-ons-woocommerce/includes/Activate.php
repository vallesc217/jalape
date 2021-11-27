<?php

namespace ZAddons;

defined( 'ABSPATH' ) || exit;

class Activate {

	const DB_UPDATED_META = 'zaddon_db_updated';

	public function __construct() {
		register_activation_hook( PLUGIN_ROOT_FILE, function () {
			DB::db_activate();
			do_action( 'zaddon_on_activate' );
		} );

		add_action( 'upgrader_process_complete', function ( $upgrader_obj, $options ) {
			$curr_plugin = plugin_basename( PLUGIN_ROOT_FILE );
			$plugins     = isset( $options['plugins'] ) ? $options['plugins'] : array();

			if ( $plugins ) {
				foreach ( $options['plugins'] as $plugin ) {
					if ( $plugin === $curr_plugin ) {
						$this->set_db_update_status( false );
					}
				}
			}
		}, 10, 2 );

		add_action( 'plugins_loaded', [ $this, 'update_db' ] );

		register_deactivation_hook( PLUGIN_ROOT_FILE, function () {
			if ( get_customize_addon_option( 'zac_delete_data' ) ) {
				do_action( 'zaddon_on_delete_data' );
				DB::drop_tables();
			}
		} );
	}

	public function update_db() {
		if ( ! get_option( self::DB_UPDATED_META, false ) ) {
			DB::check_new_tables();
			$this->set_db_update_status( true );
		}
	}

	private function set_db_update_status( $status ) {
		update_option( self::DB_UPDATED_META, $status, false );
	}
}
