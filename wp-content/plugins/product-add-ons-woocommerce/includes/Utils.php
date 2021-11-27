<?php

namespace ZAddons;

defined( 'ABSPATH' ) || exit;

class Utils {

	public static function array_mapk( $callback, $array ) {
		$result = [];

		foreach ( $array as $key => $val ) {
			$result[ $key ] = $callback( $key, $val );
		}

		return $result;
	}
}
