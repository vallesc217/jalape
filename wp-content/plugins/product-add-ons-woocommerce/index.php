<?php
/**
 * Plugin Name: Product Add-Ons for WooCommerce
 * Plugin URI: https://www.bizswoop.com/wp/productaddons
 * Description: Add Customized Product Add-Ons Support for WooCommerce
 * Version: 2.1.34
 * Text Domain: product-add-ons-woocommerce
 * Domain Path: /lang
 * WC requires at least: 2.4.0
 * WC tested up to: 5.5.2
 * Author: BizSwoop a CPF Concepts, LLC Brand
 * Author URI: http://www.bizswoop.com
 */

namespace ZAddons;

defined( 'ABSPATH' ) || exit;

$plugin_data = get_file_data( __FILE__, array( 'version' => 'Version' ) );

const ACTIVE = true;
const PLUGIN_ROOT = __DIR__;
const PLUGIN_ROOT_FILE = __FILE__;
const REST_NAMESPACE = 'wc-zaddons';
define( 'ZA_VERSION', $plugin_data['version'] );

spl_autoload_register(function ($name) {
	$name = explode('\\', $name);
	$name[0] = 'includes';
	$path = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $name) . '.php';

	if (file_exists($path)) {
		require_once $path;
	}
}, false);

new Setup();
