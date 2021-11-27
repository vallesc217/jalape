<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists('Eye_Ahbaar_Demo_Content_Option') ) {

	class Eye_Ahbaar_Demo_Content_Option {
		
		const page_slug		= 'majesty-demo-content'; // the settings page slug
		const option_name	= 'eye_majesty_demo_content';
		
		function __construct() {
		
			add_action( 'plugins_loaded', array( $this, 'init'));
		}
		
		function init() {
			
			add_action( 'admin_menu', array( $this, 'eye_add_demo_content_options_page' ));
			//add_action( 'admin_init', array( $this, 'eye_demo_content_register_settings') );
		}

		// Add Plugin option page
		function eye_add_demo_content_options_page() {
			$majesty_demo_content_page = add_submenu_page(
											'themes.php',
											esc_html__('Import Majesty Demo Content', 'eye-pro-author-review'),
											esc_html__('Import Majesty Demo', 'eye-pro-author-review'),
											'manage_options',
											self::page_slug,
											array( $this, 'eye_majesty_demo_content_settings_page_fn')
									);
		}
		
		// Out HTML for Plugin option page
		function eye_majesty_demo_content_settings_page_fn() {
			$options = get_option('eye_majesty_demo_content', array());
			$imported_demo = false;
			if( get_option( 'majesty_importedcontent') == 'true'  ) { // true
				$imported_demo = true;
			}
		
	?>
			<div class="wrap">
				<div class="icon32" id="icon-options-general"></div>
				
				<h2><?php esc_html_e('Majesty Import Demo Content', 'eye-pro-author-review'); ?></h2>
				<form method="post" action="options.php">
					
					<?php settings_fields( self::option_name );  ?>
										
					<?php do_settings_sections( self::page_slug ); ?>
					<?php echo '<div class="majesty-democontent-message"></div>'; ?>
					<?php echo '<div class="wrap-timer"><div class="demo-timer"></div></div>'; ?>
					<?php echo '<div class="demo-buttons">'; ?>
					<?php echo '<button class="add_update_demo button button-primary" type="button">Import Demo</button>'; ?>
				</form>
			</div>
		
	<?php
		}
	}
	
	new Eye_Ahbaar_Demo_Content_Option();
}
?>