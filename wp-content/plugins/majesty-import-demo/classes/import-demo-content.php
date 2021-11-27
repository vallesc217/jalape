<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists('Majesty_Import_Demo_Content') ) {

	class Majesty_Import_Demo_Content {
		
		const page_slug			= 'majesty-demo-content'; // the settings page slug
		const OPTION_NAME		= 'theme-majesty-import-demo';
		const PROCESSED_NAME	= 'theme-majesty-import-demo-processed';
		const MEDIA_TEXT		= 'Add Media ';
		
		
		public $site_url;
		public $default_demo_options;
		public $old_new_id;
		public $processed;
		
		function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init'));			
		}
		
		function init() {
			
			$this->site_url = esc_url(home_url('/'));
			$this->old_new_id 	= $this->old_new_id();
			$this->processed 	=  $this->get_processed();
			$this->default_demo_options = $this->default_demo_options();

			add_action( 'wp_ajax_majesty_add_update_demo_content', 	array( $this, 'ajax_add_update_demo_content' ));
			add_action( 'wp_ajax_majesty_set_home_page', 	array( $this, 'ajax_update_home_page' ));
			add_action( 'wp_ajax_majesty_update_contact_us_page', 	array( $this, 'update_contact_us_page' ));	
			add_action( 'admin_enqueue_scripts', 	array( $this, 'admin_enqueue_scripts_styles' ));
			
		}
		
		function admin_enqueue_scripts_styles () {
			global $pagenow;
			if ( $pagenow ==  'themes.php' ) {
				wp_enqueue_style('majesty-demo-content-css', 	Majesty_DEMO_CONTENT_URI . '/assets/css/demo.css', '', '4.0.3');
				wp_enqueue_script('majesty-demo-content-timer', Majesty_DEMO_CONTENT_URI .'/assets/js/timer.js', '', '', true );
				wp_enqueue_script('majesty-demo-content-plugin', Majesty_DEMO_CONTENT_URI .'/assets/js/demo.js', '', '', true );
				wp_localize_script('majesty-demo-content-plugin', 'majesty_demo_content', 
					array( 
						'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
						'demononce'		=> wp_create_nonce('majesty_plugin_demo_content')
					) 
				);
			}
		}

		function ajax_add_update_demo_content() {
			
			$nonce = $_POST['demosecurity'];
			if ( ! wp_verify_nonce( $nonce, 'majesty_plugin_demo_content' ) ) {
				echo json_encode(array(
					'success'	=> 'false',
					'error' 	=> 'Secuirty code error',
					'message'	=> 'Secuirty code error'
				));
				wp_die();
			}
			
			if( ! isset( $_POST['current_progress'] ) && ! empty( $_POST['current_progress'] ) ) {
				echo json_encode(array(
					'error' 	=> 'There is ajax function can\'t where to start',
					'current'	=> 'cat'
				));
				wp_die();
			}
			$next_progress = $title = '';
			$current_progress = $_POST['current_progress'];
			if( $current_progress == 'cat' )  {
				$ajax_content = $this->create_post_categories();
				$next_progress = 'tag';
				$title = 'Tags';
			} else if( $current_progress == 'tag' )  {
				$ajax_content = $this->create_post_tags();
				$next_progress = 'media';
				
				$images = $this->default_demo_options['media'];
				$image_name  	= $images[0]['title'];
				$title = self::MEDIA_TEXT . $image_name;
			} elseif( $current_progress == 'media' )  {
				$ajax_content = $this->add_media();
				$image_num = $this->processed['media_num'];
				$images = $this->default_demo_options['media'];
				if( count( $images ) > $image_num ) {
					$next_progress = 'media';
					$image_name  	= $images[$image_num]['title'];
					$title = self::MEDIA_TEXT . $image_name;
				} else {
					$next_progress = 'posts';
					$posts = $this->default_demo_options['posts'];
					$post_name  	= $posts[0]['title'];
					$title = 'Add Post - ' . $post_name;
					
					$this->processed['add_media'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
				
				
			} elseif( $current_progress == 'posts' ) {
				$ajax_content = $this->add_posts();
				$post_num = $this->processed['posts_num'];
				$posts = $this->default_demo_options['posts'];
				if( count( $posts ) > $post_num ) {
					$next_progress = 'posts';
					$post_name  	= $posts[$post_num]['title'];
					$title = 'Add Post - ' . $post_name;
				} else {
					$next_progress = 'teamcats';
					$title = 'Add team member categories';
					
					$this->processed['add_posts'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			} elseif ( $current_progress == 'teamcats' ) {
					$ajax_content = $this->create_team_categories();
					$next_progress = 'teamposts';
					// For team member posts
					$posts = $this->default_demo_options['team_members'];
					$post_name  	= $posts[0]['title'];					
					$title = 'Add Team member post - '. $post_name;
					
			} elseif( $current_progress == 'teamposts' ) {
				$ajax_content = $this->add_team_member_post();
				$post_num = $this->processed['team_member_num'];
				$posts = $this->default_demo_options['team_members'];
				if( count( $posts ) > $post_num ) {
					$next_progress = 'teamposts';
					$post_name  	= $posts[$post_num]['title'];
					$title = 'Add Team member post - ' . $post_name;
				} else {					
					$next_progress = 'shopcategory';
					$title = 'Add shop Categories';
					
					$this->processed['add_team_member'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			} elseif( $current_progress == 'shopcategory' )  {
				$ajax_content = $this->create_shop_categories();
				
				$next_progress = 'shoptags';
				$title = 'Add shop tags';
			} elseif( $current_progress == 'shoptags' )  {
				$ajax_content = $this->create_shop_tags();
				
				$next_progress = 'productposts';
				
				$posts = $this->default_demo_options['products'];
				$post_name  	= $posts[0]['title'];					
				$title = 'Add product post - '. $post_name;
			} elseif( $current_progress == 'productposts' ) {
				$ajax_content = $this->add_product_post();
				$post_num = $this->processed['product_posts_num'];
				$posts = $this->default_demo_options['products'];
				if( count( $posts ) > $post_num ) {
					$next_progress = 'productposts';
					$post_name  	= $posts[$post_num]['title'];
					$title = 'Add product post - ' . $post_name;
				} else {
					$next_progress = 'contactforms';
					$title = 'Add Contact Forms';
					
					$this->processed['add_product_posts'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			} elseif( $current_progress == 'contactforms' ) {
				$ajax_content = $this->add_contact_forms();
				
				$this->processed['add_contactforms'] = true;
				wp_cache_delete( self::PROCESSED_NAME );
				update_option( self::PROCESSED_NAME, $this->processed );
				
				$next_progress  = 'pages';
				$posts 			= $this->default_demo_options['pages'];
				$post_name  	= $posts[0]['title'];					
				$title 			= 'Add Page - '. $post_name ;
			
			} elseif( $current_progress == 'pages' ) {
				$ajax_content = $this->add_pages();
				$post_num = $this->processed['pages_num'];
				$posts = $this->default_demo_options['pages'];
				if( count( $posts ) > $post_num ) {
					$next_progress  = 'pages';
					$post_name  	= $posts[$post_num]['title'];
					$title = 'Add Page - ' . $post_name;
				} else {
					$next_progress = 'scrollmenu';
					$title = 'Add Scroll Menu';
					
					$this->processed['add_pages'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			} elseif( $current_progress == 'scrollmenu' ) {				
				if( ! $this->processed['add_scroll_menu'] ) {
					$ajax_content = $this->create_scroll_menu();
				} else {
					$ajax_content = array();
					$ajax_content['success'] = 'true';
				}
				$next_progress = 'megamenu';
				$title = 'Add Mega Menu';
				
			} elseif( $current_progress == 'megamenu' ) {
				if( ! $this->processed['add_mega_menu'] ) {
					$ajax_content = $this->create_mega_menu();;
				} else {
					$ajax_content = array();
				}
				
				$next_progress = 'footerwidget';
				$title = 'Add Footer Widget';
				
			} elseif( $current_progress == 'footerwidget' ) {
				
				if( ! $this->processed['add_footer_widget'] ) {
					$ajax_content = $this->add_footer_widget();
					$this->processed['add_footer_widget'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
					
					// Update pages
					update_option( 'page_on_front', $this->old_new_id['pages'][1527] ); // 
					update_option( 'show_on_front', 'page' );
					update_option( 'page_for_posts', $this->old_new_id['pages'][1066] );	// 1066
					
					// Woocommerce
					update_option( 'woocommerce_shop_page_id', $this->old_new_id['pages'][1304] );
					update_option( 'wp_page_for_privacy_policy', $this->old_new_id['pages'][2161] );
					update_option( 'woocommerce_cart_page_id', $this->old_new_id['pages'][1305] );
					update_option( 'woocommerce_checkout_page_id', $this->old_new_id['pages'][1306] );
					update_option( 'woocommerce_myaccount_page_id', $this->old_new_id['pages'][1307] );
					
					// Events
					update_option( 'dbem_events_page', $this->old_new_id['pages'][2974] ); // Events page
					update_option( 'dbem_locations_page', $this->old_new_id['pages'][2975] );
					update_option( 'dbem_my_bookings_page', $this->old_new_id['pages'][2978] );
					
					$booking = get_option( 'rtb-settings' , array() );
					$booking['booking-page'] = $this->old_new_id['pages'][2954];
					update_option( 'rtb-settings', $booking );	
					
				} else {
					$ajax_content = array();
					$ajax_content['success'] = 'true';
				}
				
				$next_progress = 'updateoptions';
				$title = 'Add Theme option';
				
			} elseif( $current_progress == 'updateoptions' ) {
				if( ! $this->processed['add_theme_options'] ) {
					$ajax_content = $this->add_theme_options();
					$this->processed['add_theme_options'] = true;
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::PROCESSED_NAME, $this->processed );
				} else {
					$ajax_content = array();
					$ajax_content['success'] = 'true';
				}
				$next_progress = 'finished';
				$title = 'Finished Import';
				
			} else {
				// finished
				$next_progress = 'finished';
				$title = 'Finished Import';
				
				$ajax_content = array();
				$ajax_content['success'] = 'true';
			}
			
			if( empty($ajax_content['error']) ) {
				$ajax_content['error'] = 'false';
			} else {
				$output = '';
				foreach ( $ajax_content['error'] as $error ) {
					$output .= '<li>'. $error .'</li>';
				}
				$ajax_content['success'] = 'false';
				$ajax_content['error'] = '<ul>'. $output . '</ul>';
			}
			echo json_encode(array(
				'title'		=> $title,						// for next progress
				'success'	=> $ajax_content['success'],
				'current'	=> $next_progress,
				'error'		=> $ajax_content['error'],
				)
			);

			wp_die();
		}
		
		
		
		function create_post_categories() {
			
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_post_category'] ) {
				$categories = $this->default_demo_options['post_cat'];
				foreach( $categories as $cat ) {
					
					$wpdocs_cat = array( 'cat_name' => $cat['name'] );
					// Create the category
					$cat_id = wp_insert_category($wpdocs_cat);
					
					if( is_wp_error( $cat_id ) ) {	
						$success 	= 'false';
						$error[] = 'Failed: to add Category '.$cat['name'];
					} else {
						$this->old_new_id['post_cat'][$cat['old_id']] =  $cat_id;
					}
				}
				if( $success == 'true' ) {
					$this->processed['add_post_category'] = true;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Categories'
			);
			
		}
		
		
		function create_post_tags() {
			
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_post_tag'] ) {
				$tags = $this->default_demo_options['post_tag'];
				foreach( $tags as $tag ) {
					$tag_id = wp_insert_term($tag['name'], 'post_tag');
					
					if( is_wp_error( $tag_id ) ) {	
						$success 	= 'false';
						$error[] = 'Failed: to add Category '.$tag['name'];
					} else {
						$this->old_new_id['post_tag'][$tag['old_id']] =  $tag_id['term_id'];
					}
				}
				if( $success == 'true' ) {
					$this->processed['add_post_tag'] = true;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Categories'
			);
			
		}
		
		function create_team_categories() {
			
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_team_category'] ) {
				$categories = $this->default_demo_options['team_cats'];
				foreach( $categories as $cat ) {
					$cat_id = wp_insert_term( $cat['name'], 'team-member-category' );				
					if( is_wp_error( $cat_id ) ) {	
						$success 	= 'false';
						$error[] = 'Failed: to add team category '.$cat['name'];
					} else {
						$this->old_new_id['team_cats'][$cat['old_id']] =  $cat_id['term_id'];
					}
				}
				if( $success == 'true' ) {
					$this->processed['add_team_category'] = true;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Categories'
			);
			
		}
		
		function create_shop_categories() {
			
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_product_category'] ) {
				$categories = $this->default_demo_options['product_cats'];
				foreach( $categories as $cat ) {
					
					$cat_id = wp_insert_term( $cat['name'], 'product_cat' );
					
					if( is_wp_error( $cat_id ) ) {	
						$success 	= 'false';
						$error[] = 'Failed: to add product category '.$cat['name'];
					} else {
						$this->old_new_id['product_cats'][$cat['old_id']] =  $cat_id['term_id'];
					}
				}
				if( $success == 'true' ) {
					$this->processed['add_product_category'] = true;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Product Categories'
			);
			
		}
		
		function create_shop_tags() {
			
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_product_tag'] ) {
				$categories = $this->default_demo_options['product_tags'];
				foreach( $categories as $cat ) {
					
					$cat_id = wp_insert_term( $cat['name'], 'product_tag' );
					
					if( is_wp_error( $cat_id ) ) {	
						$success 	= 'false';
						$error[] = 'Failed: to add product tag '.$cat['name'];
					} else {						
						$this->old_new_id['product_tags'][$cat['old_id']] =  $cat_id['term_id'];
					}
				}
				if( $success == 'true' ) {
					$this->processed['add_product_tag'] = true;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Product Tags'
			);
			
		}
			
		function add_media() {
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_media'] ) {
				$image_num = $this->processed['media_num'];
				$images = $this->default_demo_options['media'];
				$image  = $images[$image_num];
				$image_id = $this->insert_image($image['title']);
				if( $image_id ) {					
					$this->old_new_id['media'][$image['old_id']] =  $image_id;
					
					$this->processed['media_num'] = $image_num + 1;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
					
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Media'
			);
		}
			
		function insert_image( $image_name ) {
		
			if( !class_exists( 'WP_Http' ) ) {
				include_once( ABSPATH . WPINC . '/class-http.php' );
			}
			
			$http = new WP_Http();
			$url = Majesty_DEMO_CONTENT_URI . '/uploads/' . $image_name;
			$response = $http->request( $url );
			if( $response['response']['code'] != 200 ) {
				return false;
			}
			$upload = wp_upload_bits( basename($url), null, $response['body'] );
			if( !empty( $upload['error'] ) ) {
				return false;
			}
			$file_path = $upload['file'];
			$file_name = basename( $file_path );
			$file_type = wp_check_filetype( $file_name, null );
			$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
			$wp_upload_dir = wp_upload_dir();
			$post_info = array(
				'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
				'post_mime_type' => $file_type['type'],
				'post_title'     => $attachment_title,
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			// Create the attachment
			$attach_id = wp_insert_attachment( $post_info, $file_path );
			if( ! function_exists( 'wp_generate_attachment_metadata' ) ) {	
				// Include image.php
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}
			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			return $attach_id;
				
		}
		
		function add_posts() {
			$error 		= array();
			$success 	= 'true';
			if( ! $this->processed['add_posts'] ) {
				$post_num = $this->processed['posts_num'];
				$posts = $this->default_demo_options['posts'];
				$post  = $posts[$post_num];
				
				// Attachment URL	152 153 154 155
				$attach_1  = $this->old_new_id['media'][152];
				$attach_2  = $this->old_new_id['media'][153];
				$attach_3  = $this->old_new_id['media'][154];
				$attach_4  = $this->old_new_id['media'][155];
				$att_url_1 = wp_get_attachment_url($attach_1['new_id']);
				$att_url_2 = wp_get_attachment_url($attach_2['new_id']);
				$att_url_3 = wp_get_attachment_url($attach_3['new_id']);
				$att_url_4 = wp_get_attachment_url($attach_4['new_id']);
				
				$post_content = '<p><a title="Video Game Inspired Mural [Pics, Videos]" href="http://themeforest.net/user/samathemes/portfolio" target="_blank" rel="noopener">8BITs Office - Video Game Inspired Mural</a><br />Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. unt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<h2>WHAT WE WILL DO ? IT IS FOR YOU</h2>
				<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.</p>
				<p>[sama_blockquote float="left" style="style2"]Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.[/sama_blockquote]</p>
				<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Aenean commodo ligula eget dolor aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec vitae sapien ut libero venenatis faucibus Nulla consequat massa quis enim. Donec vitae sapien ut libero venenatis faucibus.</p>
				<p>[sama_row type="fixed-width"]<br class="removebr" />[sama_column cssclass="col-md-3"]<br class="removebr" /><img class="img-responsive" src="'. esc_url( $att_url_1 ) .'" alt="desert1" /><br class="removebr" />[/sama_column]<br class="removebr" />[sama_column cssclass="col-md-3"]<br class="removebr" /><img class="img-responsive" src="'. esc_url( $att_url_2 ) .'" alt="desert2" /><br class="removebr" />[/sama_column]<br class="removebr" />[sama_column cssclass="col-md-3"]<br class="removebr" /><img class="img-responsive" src="'. esc_url( $att_url_3 ) .'" alt="desert3" /><br class="removebr" />[/sama_column]<br class="removebr" />[sama_column cssclass="col-md-3"]<br class="removebr" /><img class="img-responsive" src="'. esc_url( $att_url_4 ) .'" alt="desert4" /><br class="removebr" />[/sama_column]<br class="removebr" />[/sama_row]</p>
				<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Aenean commodo ligula eget dolor aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec vitae sapien ut libero venenatis faucibus Nulla consequat massa quis enim. Donec vitae sapien ut libero venenatis faucibus</p>';
				
				if( $post['old_id'] == 1400 ) {
					
					$video_mp4_url  = $this->old_new_id['media'][80];
					$video_webm_url = $this->old_new_id['media'][81];
					$poster_url  	= $this->old_new_id['media'][87];
					if( $video_mp4_url['new_id'] != null ) {
						$video_mp4_url = wp_get_attachment_url($video_mp4_url);
					}
					if( $video_webm_url['new_id'] != null ) {
						$video_webm_url = wp_get_attachment_url($video_webm_url);
					}
					if( $poster_url['new_id'] != null ) {
						$poster_url = wp_get_attachment_url($poster_url);
					}					
					$post['meta'] = array( '_sama_post_layout'	=> 'fullwidth', '_sama_video_mp4' => esc_url($video_mp4_url), '_sama_video_webm' => esc_url($video_webm_url), '_sama_video_poster' => esc_url($poster_url) );
				}
				$cats = array();
				if( ! empty( $post['cat'] ) ) {
					foreach ( $post['cat'] as $cat_id ) {
						$cats[] = $this->old_new_id['post_cat'][$cat_id];
					}
				}
				$tags = array();
				if( ! empty( $post['tag'] ) ) {
					foreach ( $post['tag'] as $tag_id ) {
						$tags[] = $this->old_new_id['post_tag'][$tag_id];
					}
				}
				$post_id = wp_insert_post( array(	
									'post_title'		=> $post['title'],
									'post_type'			=> 'post',
									'post_content'		=> $post_content,
									'post_status'   	=> 'publish',
									'post_category' 	=> $cats,
									'tags_input' 		=> $tags,
									'meta_input'		=> maybe_unserialize($post['meta']),
				));
				
				
				if( $post_id ) {
					$this->old_new_id['posts'][$post['old_id']] =  $post_id;
					
					if( ! empty( $post['post_format'] ) ) {
						set_post_format( $post_id , $post['post_format'] );
						if( $post['post_format'] == 'gallery' ) {
							$attach_1  = $this->old_new_id['media'][70];
							$attach_1  = $this->old_new_id['media'][73];
							$my_post = array(
								  'ID'				=> $attach_1,
								  'post_parent'		=> $post_id,
							);
							wp_update_post( $my_post );
							$my_post = array(
								  'ID'				=> $attach_2,
								  'post_parent'		=> $post_id,
							);
							wp_update_post( $my_post );
						}
					}
					
					$thumb_id  = $this->old_new_id['media'][$post['thumb_id']];
					set_post_thumbnail( $post_id, $thumb_id );
					
					$this->processed['posts_num'] = $post_num + 1;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
					
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Media'
			);
		}
		
		function add_team_member_post() {
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_team_member'] ) {
				$post_num = $this->processed['team_member_num'];
				$posts = $this->default_demo_options['team_members'];
				$post  = $posts[$post_num];
				
				$post_content = 'We aim to home-produce as much as possible – for the best quality, and to reduce “food miles”. Our delicious cakes, traditional Devon scones, breads, soups, sauces and accompaniments are produced in our own kitchens – and we use herbs from our gardens when in season. Even our bottled water is produced in-house, using a sophisticated seven-stage filtration and purification system.

				We offer vegetarian options of course, and it is our pleasure to cater for any special dietary requirements – just let us know, and we will tailor our dishes accordingly.';
				$team_cats  = $this->old_new_id['team_cats'][8];

				$post_id = wp_insert_post( array(	
									'post_title'		=> $post['title'],
									'post_type'			=> 'team-member',
									'post_content'		=> $post_content,
									'post_status'   	=> 'publish',
									'tax_input' 		=> array( 'team-member-category' => array($team_cats)),
									'meta_input'		=> maybe_unserialize($post['meta']),
				));
				
				
				if( $post_id ) {
					$this->old_new_id['team_members'][$post['old_id']] = $post_id;
					$thumb_id  = $this->old_new_id['media'][$post['thumb_id']];
					set_post_thumbnail( $post_id, $thumb_id );

					$this->processed['team_member_num'] = $post_num + 1;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
					
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Media'
			);
		}
		
		function add_product_post() {
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_product_posts'] ) {
				$post_num = $this->processed['product_posts_num'];
				$posts = $this->default_demo_options['products'];
				$post  = $posts[$post_num];
				
				$post_content = '[sama_column cssclass="col-md-6"]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.<br class="removebr" />[/sama_column][sama_column cssclass="col-md-6"]
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.<br class="removebr" />[/sama_column]';
				
				$cats = array();
				if( ! empty( $post['cat'] ) ) {
					foreach ( $post['cat'] as $cat_id ) {
						$cats[] = $this->old_new_id['product_cats'][$cat_id];
					}
				}
				$tags = array();
				if( ! empty( $post['tag'] ) ) {
					foreach ( $post['tag'] as $tag_id ) {
						$tags[] = $this->old_new_id['product_tags'][$tag_id];
					}
				}
				
				$gallery = array();
				$gallery[]  = $this->old_new_id['media'][168];
				$gallery[]  = $this->old_new_id['media'][169];
				$gallery[]  = $this->old_new_id['media'][170];
				
				$meta = array( 
					'_sama_post_layout'		=> 'rightsidebar',
					'_visibility'			=> 'visible',
					'_stock_status' 		=> 'instock',
					'total_sales' 			=> '0',
					'_downloadable' 		=> '',
					'_virtual' 				=> '',
					'_price'				=> 79.99,
					'_regular_price' 		=> 79.99,
					'_sale_price' 			=> '',
					'_purchase_note' 		=> '',
					'_featured' 			=> 0,
					'_weight' 				=> '',
					'_length' 				=> '',
					'_height' 				=> '',
					'_width' 				=> '',
					'_sku' 					=> $post['sku'],
					'_manage_stock' 		=> 'no',
					'_backorders' 			=> 'no',
					'_stock' 				=> '',
					'_product_image_gallery' => implode(',', $gallery),
				);
				if( in_array( $post['old_id'], array(3196, 3187, 3179, 3177) ) ) {
					$meta['_featured'] = 'yes';
				}
				if( $post['on_sale'] ) {
					$meta['_sale_price'] = 69.99;
				}				
				$post_excerpt = 'Ei est doctus persius. Cum cu putant iuvaret voluptatibus, eu dolore primis vix, singulis accusamus te quo. Homero saperet iudicabit ut eum, et eam everti abhorreant, eos essent dolores scriptorem ut. Tibique epicuri no vis, quo epicuri appareat id.';
				$post_id = wp_insert_post( array(	
									'post_title'		=> $post['title'],
									'post_type'			=> 'product',
									'post_content'		=> $post_content,
									'post_excerpt'		=> $post_excerpt,
									'post_status'   	=> 'publish',
									'tax_input' 		=> array( 'product_cat' => $cats, 'product_tag' => $tags ),
									'meta_input'		=> $meta,
				));
				
				
				if( $post_id ) {
					
					wp_set_object_terms($post_id, 'simple', 'product_type');										
					$this->old_new_id['products'][$post['old_id']] = $post_id;
					
					$thumb_id  = $this->old_new_id['media'][$post['thumb_id']];
					set_post_thumbnail( $post_id, $thumb_id );					
					$this->processed['product_posts_num'] = $post_num + 1;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Media'
			);
			
			
		}
		
		function add_contact_forms() {
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_contactforms'] ) {
				
				$contacts = $this->default_demo_options['contactforms'];
				
				foreach( $contacts as $post ) {
					$post_id = wp_insert_post( array(	
									'post_title'		=> $post['title'],
									'post_type'			=> 'wpcf7_contact_form',
									'post_content'		=> $post['content'],
									'post_status'   	=> 'publish',
									'meta_input'		=> array( '_form' => $post['content'] )
									
					));
					
					if( $post_id ) {						
						$this->old_new_id['contactforms'][$post['old_id']] = $post_id;
					}
					
				}
				wp_cache_delete( self::OPTION_NAME );
				update_option( self::OPTION_NAME, $this->old_new_id );
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add Contact Forms'
			);
		}
		
		function add_pages() {
			$error 		= array();
			$success 	= 'true';
			
			if( ! $this->processed['add_pages'] ) {
				$post_num = $this->processed['pages_num'];
				$posts = $this->default_demo_options['pages'];
				$post  = $posts[$post_num];
				if( isset( $post['meta']['_sama_bgslider_settings'] ) ) {
					$images = $post['meta']['_sama_bgslider_settings'][0]['images'];
					
					$new_images = array();
					foreach ( $images as $key ) {
						$image_id 	= $this->old_new_id['media'][$key];
						$image_url  = wp_get_attachment_url($image_id);
						$new_images[$image_id] = esc_url($image_url);
					}
					$post['meta']['_sama_bgslider_settings'][0]['images'] = $new_images;
				}
				
				if( isset( $post['meta']['_sama_page_bg_id'] ) ) {
					$id = $post['meta']['_sama_page_bg_id'];
					$image_id 	= $this->old_new_id['media'][$id];
					$image_url  = wp_get_attachment_url($image_id);
					
					$post['meta']['_sama_page_bg_id'] = $image_id;
					$post['meta']['_sama_page_bg']    = esc_url( $image_url );
					
				}
				if( empty( $post['tpl'] ) ) {
					
					if( isset( $post['content'] ) ) {
						$post_content = $post['content'];
					} else {
						$post_content = '';
					}
					
				} else {
					$post_content = $this->get_content_page_template($post['tpl']);
				}
				if( isset( $post['meta']['_sama_parallaxbg_settings'] ) ) {
					$image_id = $post['meta']['_sama_parallaxbg_settings'][0]['image_id'];
					$image_id 	= $this->old_new_id['media'][$image_id];
					$image_url  = wp_get_attachment_url($image_id);
		
					$post['meta']['_sama_parallaxbg_settings'][0]['image_id'] = $image_id;
					$post['meta']['_sama_parallaxbg_settings'][0]['image'] = $image_url;
				}
				
				$post_id = wp_insert_post( array(	
									'post_title'		=> $post['title'],
									'post_type'			=> 'page',
									'post_content'		=> $post_content,
									'post_status'   	=> 'publish',
									'meta_input'		=> $post['meta'],
				));
				
				
				if( $post_id ) {
					if( $post['old_id'] == 2975 || $post['old_id'] == 2978 ) {
						$add_post_parent = array(
						  'ID'           => $post_id,
						  'post_parent'   => $this->old_new_id['pages'][2974],
						);
						wp_update_post( $add_post_parent );
					}
					$page_child = array( 2077, 2784, 2824, 2048, 2833, 2838, 2835, 2124, 2855, 2136, 2058, 2088, 2119, 2107, 2103, 2116, 2071, 2912, 2773, 2146);
					if( in_array( $post['old_id'] , $page_child ) ) {
						$add_post_parent = array(
							  'ID'           => $post_id,
							  'post_parent'   => $this->old_new_id['pages'][2026],
						);
						wp_update_post( $add_post_parent );
					}
					$this->old_new_id['pages'][$post['old_id']] = $post_id;
					$this->processed['pages_num'] = $post_num + 1;
					wp_cache_delete( self::OPTION_NAME );
					wp_cache_delete( self::PROCESSED_NAME );
					update_option( self::OPTION_NAME, $this->old_new_id );
					update_option( self::PROCESSED_NAME, $this->processed );
					
				}
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Page'
			);
		}
		function get_processed() {
			$args = array(
				'add_post_category'		=> false,
				'add_post_tag'			=> false,
				'add_media'				=> false,
				'media_num'				=> 0,
				'add_posts'				=> false,
				'posts_num'				=> 0,
				'add_team_category'		=> false,
				'add_team_member'		=> false,
				'team_member_num'		=> 0,
				'add_product_category'	=> false,
				'add_product_tag'		=> false,
				'add_product_posts'		=> false,
				'product_posts_num'		=> 0,
				'add_contactforms'		=> false,
				'add_pages'				=> false,
				'pages_num'				=> 0,
				'add_scroll_menu'		=> false,
				'add_mega_menu'			=> false,
				'add_footer_widget'		=> false,
				'add_theme_options'		=> false
			);
			
			$processed = get_option( self::PROCESSED_NAME, array());
			$majesty_processed = wp_parse_args( $processed, $args );
			
			return $majesty_processed;
		}
		
		
		
		function default_demo_options() {
			//				
			$date1 = new DateTime("+1 months");
			$date =  $date1->format("Y/m/d");
			$logo_url = $comming_default_id = $comming_default_img = $mb4 = $webm = $poster_id = $poster_url = '';
			$movementbg_id = $movementbg_img = $swiper_slide_1_id = $swiper_slide_1_url = '';
			$swiper_slide_2_id = $swiper_slide_2_url = $skipper_slide_1_id = $skipper_slide_1_url = $skipper_slide_2_id = $skipper_slide_2_url = '';
			$skipper_slide_3_id = $skipper_slide_3_url = $interactivebg_id = $interactivebg_img = '';

			if( isset( $this->old_new_id['media'][218] ) ) {
				$logo_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][218]));
			}
			if( isset( $this->old_new_id['media'][81] ) ) {
				$mb4  = esc_url(wp_get_attachment_url($this->old_new_id['media'][80]));
				$webm  = esc_url(wp_get_attachment_url($this->old_new_id['media'][81]));
				$poster_id = $this->old_new_id['media'][71];
				$poster_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][71]));
				
				//american
				$swiper_slide_1_id 	= $this->old_new_id['media'][42];
				$swiper_slide_1_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][42]));
				
				$swiper_slide_2_id 	= $this->old_new_id['media'][37];
				$swiper_slide_2_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][37]));
				
				// movement image
				$movementbg_id  = $this->old_new_id['media'][90];
				$movementbg_img = esc_url(wp_get_attachment_url($this->old_new_id['media'][90]));
				
				// on page fade 
				$skipper_slide_1_id  = $this->old_new_id['media'][23];
				$skipper_slide_1_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][23]));
				$skipper_slide_2_id  = $this->old_new_id['media'][34];
				$skipper_slide_2_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][34]));
				$skipper_slide_3_id  = $swiper_slide_2_id;
				$skipper_slide_3_url = $swiper_slide_2_url;
				
				// interactive bg
				$interactivebg_id  = $this->old_new_id['media'][19];
				$interactivebg_img = esc_url(wp_get_attachment_url($this->old_new_id['media'][19]));
				
			}
			
			//_swiper_slide 
			
			$args = array(
				'post_cat'		=> array(
									   array(
											'name'		=> 'News',
											'old_id'	=> 116,
											'new_id'	=> '',
									   ),
									    array(
											'name'		=> 'Our Events',
											'old_id'	=> 120,
											'new_id'	=> '',
									   ),
				),
				'post_tag'		=> array(
									   array(
											'name'		=> 'Burger',
											'old_id'	=> 122,
											'new_id'	=> null,
									   ),
									   array(
											'name'		=> 'Chef',
											'old_id'	=> 123,
											'new_id'	=> null,
									   ),
									   array(
											'name'		=> 'French Bread',
											'old_id'	=> 128,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'Pizza',
											'old_id'	=> 121,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'Red',
											'old_id'	=> 127,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'Sea Food',
											'old_id'	=> 124,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'Shawarma',
											'old_id'	=> 126,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'Steak',
											'old_id'	=> 125,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'asian',
											'old_id'	=> 152,
											'new_id'	=> null
									   ),
									   array(
											'name'		=> 'american food',
											'old_id'	=> 153,
											'new_id'	=> null
									   ),
				),
				'team_cats'		=> array(
									   array(
											'name'		=> 'CHEF',
											'old_id'	=> 8,
											'new_id'	=> '',
									   )
				),
				'product_cats'		=> array(
										   array(
												'name'		=> 'Breakfast',
												'old_id'	=> 104,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'Dinner',
												'old_id'	=> 107,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'Drinks',
												'old_id'	=> 108,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> '	Lunch',
												'old_id'	=> 105,
												'new_id'	=> '',
										   )
				),
				'product_tags'		=> array(
										   array(
												'name'		=> 'break',
												'old_id'	=> 111,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'dinner',
												'old_id'	=> 112,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'Dishes',
												'old_id'	=> 106,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'drinks',
												'old_id'	=> 109,
												'new_id'	=> '',
										   ),
										   array(
												'name'		=> 'lunch',
												'old_id'	=> 110,
												'new_id'	=> '',
										   )
				),
				'media'		=> array(
								    array("title" =>"art-1.jpg", "old_id" =>1, "new_id" => '' ),
									array("title" =>"art-2.jpg", "old_id" =>2, "new_id" => '' ),
									array("title" =>"bg-about.jpg", "old_id" =>3, "new_id" => '' ),
									array("title" =>"bg-blog.jpg", "old_id" =>4, "new_id" => '' ),
									array("title" =>"bg-carts.jpg", "old_id" =>5, "new_id" => '' ),
									array("title" =>"bg-clients.jpg", "old_id" =>6, "new_id" => '' ),
									array("title" =>"bg-coming.jpg", "old_id" =>7, "new_id" => '' ),
									array("title" =>"bg-contact.jpg", "old_id" =>8, "new_id" => '' ),
									array("title" =>"bg-in-vc-1.jpg", "old_id" =>9, "new_id" => '' ),
									array("title" =>"bg-in-vc-10.jpg", "old_id" =>10, "new_id" => '' ),
									array("title" =>"bg-in-vc-2.jpg", "old_id" =>11, "new_id" => '' ),
									array("title" =>"bg-in-vc-3.jpg", "old_id" =>12, "new_id" => '' ),
									array("title" =>"bg-in-vc-4.jpg", "old_id" =>13, "new_id" => '' ),
									array("title" =>"bg-in-vc-5.jpg", "old_id" =>14, "new_id" => '' ),
									array("title" =>"bg-in-vc-6.jpg", "old_id" =>15, "new_id" => '' ),
									array("title" =>"bg-in-vc-7.jpg", "old_id" =>16, "new_id" => '' ),
									array("title" =>"bg-in-vc-8.jpg", "old_id" =>17, "new_id" => '' ),
									array("title" =>"bg-in-vc-9.jpg", "old_id" =>18, "new_id" => '' ),
									array("title" =>"bg-interactive.jpg", "old_id" =>19, "new_id" => '' ),
									array("title" =>"bg-menu-shop.jpg", "old_id" =>20, "new_id" => '' ),
									array("title" =>"bg-menu.jpg", "old_id" =>21, "new_id" => '' ),
									array("title" =>"bg-services.jpg", "old_id" =>22, "new_id" => '' ),
									array("title" =>"bg-slider-1.jpg", "old_id" =>23, "new_id" => '' ),
									array("title" =>"bg-slider-10.jpg", "old_id" =>24, "new_id" => '' ),
									array("title" =>"bg-slider-11.jpg", "old_id" =>25, "new_id" => '' ),
									array("title" =>"bg-slider-12.jpg", "old_id" =>26, "new_id" => '' ),
									array("title" =>"bg-slider-13.jpg", "old_id" =>27, "new_id" => '' ),
									array("title" =>"bg-slider-14.jpg", "old_id" =>28, "new_id" => '' ),
									array("title" =>"bg-slider-15.jpg", "old_id" =>29, "new_id" => '' ),
									array("title" =>"bg-slider-16.jpg", "old_id" =>30, "new_id" => '' ),
									array("title" =>"bg-slider-17.jpg", "old_id" =>31, "new_id" => '' ),
									array("title" =>"bg-slider-18.jpg", "old_id" =>32, "new_id" => '' ),
									array("title" =>"bg-slider-19.jpg", "old_id" =>33, "new_id" => '' ),
									array("title" =>"bg-slider-2.jpg", "old_id" =>34, "new_id" => '' ),
									array("title" =>"bg-slider-20.jpg", "old_id" =>35, "new_id" => '' ),
									array("title" =>"bg-slider-21.jpg", "old_id" =>36, "new_id" => '' ),
									array("title" =>"bg-slider-3.jpg", "old_id" =>37, "new_id" => '' ),
									array("title" =>"bg-slider-4.jpg", "old_id" =>38, "new_id" => '' ),
									array("title" =>"bg-slider-5.jpg", "old_id" =>39, "new_id" => '' ),
									array("title" =>"bg-slider-6.jpg", "old_id" =>40, "new_id" => '' ),
									array("title" =>"bg-slider-7.jpg", "old_id" =>41, "new_id" => '' ),
									array("title" =>"bg-slider-8.jpg", "old_id" =>42, "new_id" => '' ),
									array("title" =>"bg-slider-9.jpg", "old_id" =>43, "new_id" => '' ),
									array("title" =>"bg-vc-1.jpg", "old_id" =>44, "new_id" => '' ),
									array("title" =>"bg-vc-10.jpg", "old_id" =>45, "new_id" => '' ),
									array("title" =>"bg-vc-11.jpg", "old_id" =>46, "new_id" => '' ),
									array("title" =>"bg-vc-12.jpg", "old_id" =>47, "new_id" => '' ),
									array("title" =>"bg-vc-13.jpg", "old_id" =>48, "new_id" => '' ),
									array("title" =>"bg-vc-14.jpg", "old_id" =>49, "new_id" => '' ),
									array("title" =>"bg-vc-15.jpg", "old_id" =>50, "new_id" => '' ),
									array("title" =>"bg-vc-16.jpg", "old_id" =>51, "new_id" => '' ),
									array("title" =>"bg-vc-17.jpg", "old_id" =>52, "new_id" => '' ),
									array("title" =>"bg-vc-18.jpg", "old_id" =>53, "new_id" => '' ),
									array("title" =>"bg-vc-19.jpg", "old_id" =>54, "new_id" => '' ),
									array("title" =>"bg-vc-2.jpg", "old_id" =>55, "new_id" => '' ),
									array("title" =>"bg-vc-20.jpg", "old_id" =>56, "new_id" => '' ),
									array("title" =>"bg-vc-21.jpg", "old_id" =>57, "new_id" => '' ),
									array("title" =>"bg-vc-22.jpg", "old_id" =>58, "new_id" => '' ),
									array("title" =>"bg-vc-23.jpg", "old_id" =>59, "new_id" => '' ),
									array("title" =>"bg-vc-3.jpg", "old_id" =>60, "new_id" => '' ),
									array("title" =>"bg-vc-4.jpg", "old_id" =>61, "new_id" => '' ),
									array("title" =>"bg-vc-5.jpg", "old_id" =>62, "new_id" => '' ),
									array("title" =>"bg-vc-6.jpg", "old_id" =>63, "new_id" => '' ),
									array("title" =>"bg-vc-7.jpg", "old_id" =>64, "new_id" => '' ),
									array("title" =>"bg-vc-8.jpg", "old_id" =>65, "new_id" => '' ),
									array("title" =>"bg-vc-9.jpg", "old_id" =>66, "new_id" => '' ),
									array("title" =>"bg-wood-2.jpg", "old_id" =>67, "new_id" => '' ),
									array("title" =>"bg-wood-3.jpg", "old_id" =>68, "new_id" => '' ),
									array("title" =>"bg-wood.jpg", "old_id" =>69, "new_id" => '' ),
									array("title" =>"blog-list.jpg", "old_id" =>70, "new_id" => '' ),
									array("title" =>"blog-list2.jpg", "old_id" =>71, "new_id" => '' ),
									array("title" =>"blog-list3.jpg", "old_id" =>72, "new_id" => '' ),
									array("title" =>"blog-list4.jpg", "old_id" =>73, "new_id" => '' ),
									array("title" =>"blog-list5.jpg", "old_id" =>74, "new_id" => '' ),
									array("title" =>"center-intro-2.jpg", "old_id" =>75, "new_id" => '' ),
									array("title" =>"center-intro.jpg", "old_id" =>76, "new_id" => '' ),
									array("title" =>"certificate.png", "old_id" =>77, "new_id" => '' ),
									array("title" =>"chef-1.jpg", "old_id" =>78, "new_id" => '' ),
									array("title" =>"chef.png", "old_id" =>79, "new_id" => '' ),
									array("title" =>"left-bg.png", "old_id" =>82, "new_id" => '' ),
									array("title" =>"Left-Image-1.jpg", "old_id" =>83, "new_id" => '' ),
									array("title" =>"Left-Image-2.jpg", "old_id" =>84, "new_id" => '' ),
									array("title" =>"left-intro-1.jpg", "old_id" =>85, "new_id" => '' ),
									array("title" =>"left-intro-2.jpg", "old_id" =>86, "new_id" => '' ),
									array("title" =>"map.png", "old_id" =>88, "new_id" => '' ),
									array("title" =>"move-bg.jpg", "old_id" =>90, "new_id" => '' ),
									array("title" =>"pattern.jpg", "old_id" =>91, "new_id" => '' ),
									array("title" =>"png-app.png", "old_id" =>92, "new_id" => '' ),
									array("title" =>"png-breakfast-dark.png", "old_id" =>93, "new_id" => '' ),
									array("title" =>"png-breakfast.png", "old_id" =>94, "new_id" => '' ),
									array("title" =>"png-desert-dark.png", "old_id" =>95, "new_id" => '' ),
									array("title" =>"png-dessert.png", "old_id" =>96, "new_id" => '' ),
									array("title" =>"png-dinner-dark.png", "old_id" =>97, "new_id" => '' ),
									array("title" =>"png-dinner.png", "old_id" =>98, "new_id" => '' ),
									array("title" =>"png-lunch-dark.png", "old_id" =>99, "new_id" => '' ),
									array("title" =>"png-lunch.png", "old_id" =>100, "new_id" => '' ),
									
									array("title" =>"rest-1.jpg", "old_id" =>105, "new_id" => '' ),
									array("title" =>"rest-2.jpg", "old_id" =>106, "new_id" => '' ),
									array("title" =>"rest-3.jpg", "old_id" =>107, "new_id" => '' ),
									array("title" =>"rest-4.jpg", "old_id" =>108, "new_id" => '' ),
									array("title" =>"rest-5.jpg", "old_id" =>109, "new_id" => '' ),
									array("title" =>"rest-6.jpg", "old_id" =>110, "new_id" => '' ),
									array("title" =>"rest-7.jpg", "old_id" =>111, "new_id" => '' ),
									array("title" =>"rest-8.jpg", "old_id" =>112, "new_id" => '' ),
									array("title" =>"rest-9.jpg", "old_id" =>113, "new_id" => '' ),
									array("title" =>"right-bg-1.png", "old_id" =>114, "new_id" => '' ),
									array("title" =>"right-bg-2.png", "old_id" =>115, "new_id" => '' ),
									array("title" =>"right-image-1.jpg", "old_id" =>116, "new_id" => '' ),
									array("title" =>"right-image-2.jpg", "old_id" =>117, "new_id" => '' ),
									array("title" =>"right-intro-1.jpg", "old_id" =>118, "new_id" => '' ),
									array("title" =>"right-intro-2.jpg", "old_id" =>119, "new_id" => '' ),
									array("title" =>"s-breakfast-1.jpg", "old_id" =>120, "new_id" => '' ),
									array("title" =>"s-breakfast-2.jpg", "old_id" =>121, "new_id" => '' ),
									array("title" =>"s-breakfast-3.jpg", "old_id" =>122, "new_id" => '' ),
									array("title" =>"s-breakfast-4.jpg", "old_id" =>123, "new_id" => '' ),
									array("title" =>"s-breakfast-5.jpg", "old_id" =>124, "new_id" => '' ),
									array("title" =>"s-breakfast-6.jpg", "old_id" =>125, "new_id" => '' ),
									array("title" =>"s-breakfast-7.jpg", "old_id" =>126, "new_id" => '' ),
									array("title" =>"s-breakfast-8.jpg", "old_id" =>127, "new_id" => '' ),
									array("title" =>"s-desert-1.jpg", "old_id" =>128, "new_id" => '' ),
									array("title" =>"s-desert-2.jpg", "old_id" =>129, "new_id" => '' ),
									array("title" =>"s-desert-3.jpg", "old_id" =>130, "new_id" => '' ),
									array("title" =>"s-desert-4.jpg", "old_id" =>131, "new_id" => '' ),
									array("title" =>"s-desert-5.jpg", "old_id" =>132, "new_id" => '' ),
									array("title" =>"s-desert-6.jpg", "old_id" =>133, "new_id" => '' ),
									array("title" =>"s-desert-7.jpg", "old_id" =>134, "new_id" => '' ),
									array("title" =>"s-desert-8.jpg", "old_id" =>135, "new_id" => '' ),
									array("title" =>"s-dinner-1.jpg", "old_id" =>136, "new_id" => '' ),
									array("title" =>"s-dinner-2.jpg", "old_id" =>137, "new_id" => '' ),
									array("title" =>"s-dinner-3.jpg", "old_id" =>138, "new_id" => '' ),
									array("title" =>"s-dinner-4.jpg", "old_id" =>139, "new_id" => '' ),
									array("title" =>"s-dinner-5.jpg", "old_id" =>140, "new_id" => '' ),
									array("title" =>"s-dinner-6.jpg", "old_id" =>141, "new_id" => '' ),
									array("title" =>"s-dinner-7.jpg", "old_id" =>142, "new_id" => '' ),
									array("title" =>"s-dinner-8.jpg", "old_id" =>143, "new_id" => '' ),
									array("title" =>"s-launch-1.jpg", "old_id" =>144, "new_id" => '' ),
									array("title" =>"s-launch-2.jpg", "old_id" =>145, "new_id" => '' ),
									array("title" =>"s-launch-3.jpg", "old_id" =>146, "new_id" => '' ),
									array("title" =>"s-launch-4.jpg", "old_id" =>147, "new_id" => '' ),
									array("title" =>"s-launch-5.jpg", "old_id" =>148, "new_id" => '' ),
									array("title" =>"s-launch-6.jpg", "old_id" =>149, "new_id" => '' ),
									array("title" =>"s-launch-7.jpg", "old_id" =>150, "new_id" => '' ),
									array("title" =>"s-launch-8.jpg", "old_id" =>151, "new_id" => '' ),
									array("title" =>"shop-1.jpg", "old_id" =>152, "new_id" => '' ),
									array("title" =>"shop-10.jpg", "old_id" =>153, "new_id" => '' ),
									array("title" =>"shop-11.jpg", "old_id" =>154, "new_id" => '' ),
									array("title" =>"shop-12.jpg", "old_id" =>155, "new_id" => '' ),
									array("title" =>"shop-13.jpg", "old_id" =>156, "new_id" => '' ),
									array("title" =>"shop-14.jpg", "old_id" =>157, "new_id" => '' ),
									array("title" =>"shop-15.jpg", "old_id" =>158, "new_id" => '' ),
									array("title" =>"shop-16.jpg", "old_id" =>159, "new_id" => '' ),
									array("title" =>"shop-2.jpg", "old_id" =>160, "new_id" => '' ),
									array("title" =>"shop-3.jpg", "old_id" =>161, "new_id" => '' ),
									array("title" =>"shop-4.jpg", "old_id" =>162, "new_id" => '' ),
									array("title" =>"shop-5.jpg", "old_id" =>163, "new_id" => '' ),
									array("title" =>"shop-6.jpg", "old_id" =>164, "new_id" => '' ),
									array("title" =>"shop-7.jpg", "old_id" =>165, "new_id" => '' ),
									array("title" =>"shop-8.jpg", "old_id" =>166, "new_id" => '' ),
									array("title" =>"shop-9.jpg", "old_id" =>167, "new_id" => '' ),
									array("title" =>"shop-detail-1.jpg", "old_id" =>168, "new_id" => '' ),
									array("title" =>"shop-detail-2.jpg", "old_id" =>169, "new_id" => '' ),
									array("title" =>"shop-detail-3.jpg", "old_id" =>170, "new_id" => '' ),
									array("title" =>"slide1.jpg", "old_id" =>171, "new_id" => '' ),
									array("title" =>"team-1.jpg", "old_id" =>172, "new_id" => '' ),
									array("title" =>"team-2.jpg", "old_id" =>173, "new_id" => '' ),
									array("title" =>"team-3.jpg", "old_id" =>174, "new_id" => '' ),
									array("title" =>"team-4.jpg", "old_id" =>175, "new_id" => '' ),
									array("title" =>"team-5.jpg", "old_id" =>176, "new_id" => '' ),
									array("title" =>"team-6.jpg", "old_id" =>177, "new_id" => '' ),
									array("title" =>"team-7.jpg", "old_id" =>178, "new_id" => '' ),
									array("title" =>"team-8.jpg", "old_id" =>179, "new_id" => '' ),
									array("title" =>"team.jpg", "old_id" =>180, "new_id" => '' ),
									array("title" =>"vc-block-10.jpg", "old_id" =>181, "new_id" => '' ),
									array("title" =>"vc-block-4.jpg", "old_id" =>182, "new_id" => '' ),
									array("title" =>"vc-block-8.jpg", "old_id" =>183, "new_id" => '' ),
									array("title" =>"vc-block-9.jpg", "old_id" =>184, "new_id" => '' ),
									array("title" =>"y-alignment-1200x4002.jpg", "old_id" =>186, "new_id" => '' ),
									array("title" =>"y-alignment-150x150.jpg", "old_id" =>187, "new_id" => '' ),
									array("title" =>"y-alignment-300x200.jpg", "old_id" =>188, "new_id" => '' ),
									array("title" =>"y-alignment-580x300.jpg", "old_id" =>189, "new_id" => '' ),
									array("title" =>"client-1.jpg", "old_id" =>190, "new_id" => '' ),
									array("title" =>"client-10.png", "old_id" =>191, "new_id" => '' ),
									array("title" =>"client-11.png", "old_id" =>192, "new_id" => '' ),
									array("title" =>"client-12.png", "old_id" =>193, "new_id" => '' ),
									array("title" =>"client-13.png", "old_id" =>194, "new_id" => '' ),
									array("title" =>"client-14.png", "old_id" =>195, "new_id" => '' ),
									array("title" =>"client-15.png", "old_id" =>196, "new_id" => '' ),
									array("title" =>"client-2.jpg", "old_id" =>197, "new_id" => '' ),
									array("title" =>"client-3.jpg", "old_id" =>198, "new_id" => '' ),
									array("title" =>"client-4.jpg", "old_id" =>199, "new_id" => '' ),
									array("title" =>"client-7.png", "old_id" =>200, "new_id" => '' ),
									array("title" =>"client-8.png", "old_id" =>201, "new_id" => '' ),
									array("title" =>"client-9.png", "old_id" =>202, "new_id" => '' ),
									array("title" =>"menu-1.jpg", "old_id" =>203, "new_id" => '' ),
									array("title" =>"menu-2.jpg", "old_id" =>204, "new_id" => '' ),
									array("title" =>"menu-3.jpg", "old_id" =>205, "new_id" => '' ),
									array("title" =>"menu-4.jpg", "old_id" =>206, "new_id" => '' ),
									array("title" =>"menu-5.jpg", "old_id" =>207, "new_id" => '' ),
									array("title" =>"menu-6.jpg", "old_id" =>208, "new_id" => '' ),
									array("title" =>"menu-7.jpg", "old_id" =>209, "new_id" => '' ),
									array("title" =>"menu-8.jpg", "old_id" =>210, "new_id" => '' ),
									
									array("title" =>"footer_logo.png", "old_id" =>214, "new_id" => '' ),
									array("title" =>"logo-dark-small.png", "old_id" =>215, "new_id" => '' ),
									array("title" =>"logo-dark.png", "old_id" =>216, "new_id" => '' ),
									array("title" =>"logo-white-bg.png", "old_id" =>217, "new_id" => '' ),
									array("title" =>"logo.png", "old_id" =>218, "new_id" => '' ),
									array("title" =>"logo_intro.png", "old_id" =>219, "new_id" => '' ),
									array("title" =>"vertical-logo.png", "old_id" =>220, "new_id" => '' ),
									
									array("title" =>"explore.mp4", "old_id" =>80, "new_id" => '' ),
									array("title" =>"explore.webm", "old_id" =>81, "new_id" => '' ),	
									array("title" =>"poster.jpg", "old_id" =>87, "new_id" => '' ),	
									/*
									array("title" =>"pop-1.png", "old_id" =>101, "new_id" => '' ),
									array("title" =>"pop-2.png", "old_id" =>102, "new_id" => '' ),
									array("title" =>"pop-3.png", "old_id" =>103, "new_id" => '' ),
									array("title" =>"pop-4.png", "old_id" =>104, "new_id" => '' ),
									array("title" =>"apple-touch-icon-144x144.png", "old_id" =>211, "new_id" => '' ),
									array("title" =>"apple-touch-icon-57x57.png", "old_id" =>212, "new_id" => '' ),
									array("title" =>"apple-touch-icon-72x72.png", "old_id" =>213, "new_id" => '' ),
									*/

									
				),
				'posts'	=> array(
								array(
									'title'				=> 'Now new branch in maadi is open',
									'old_id'			=> 1405,
									'new_id'			=> null,
									'post_format'		=> '',
									'tag'				=> array(124,126,125),
									'cat'				=> array(116,120),
									'thumb_id'			=> 74,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'Buy two pizza and get one free',
									'old_id'			=> 3656,
									'new_id'			=> null,
									'post_format'		=> 'video',
									'tag'				=> array(124,126,125),
									'cat'				=> array(120),
									'thumb_id'			=> 71,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_sama_oembed' => 'https://youtu.be/HAeWL6I25rc' )
								),
								array(
									'title'				=> 'Now new branch in maadi is open',
									'old_id'			=> 3645,
									'new_id'			=> null,
									'post_format'		=> '',
									'tag'				=> array(128,121,127),
									'cat'				=> array(116),
									'thumb_id'			=> 72,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'Chef meeting his lovers',
									'old_id'			=> 555,
									'new_id'			=> null,
									'post_format'		=> 'gallery',
									'tag'				=> array(122,123,121),
									'cat'				=> array(116),
									'thumb_id'			=> 73,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'New majesty branch now available',
									'old_id'			=> 565,
									'new_id'			=> null,
									'post_format'		=> 'link',
									'tag'				=> array(124,126,125),
									'cat'				=> array(120),
									'thumb_id'			=> 70,
									'meta'				=> array( '_sama_post_layout'	=> 'leftsidebar' )
								),
								array(
									'title'				=> 'Now new branch in maadi is open',
									'old_id'			=> 568,
									'new_id'			=> null,
									'post_format'		=> 'image',
									'tag'				=> array(128,121,127),
									'cat'				=> array(120),
									'thumb_id'			=> 70,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'Buy two pizza and get one free',
									'old_id'			=> 575,
									'new_id'			=> null,
									'post_format'		=> 'quote',
									'tag'				=> array(122,123,121),
									'cat'				=> array(116),
									'thumb_id'			=> 71,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'Viemo buy two pizza and get one',
									'old_id'			=> 582,
									'new_id'			=> null,
									'post_format'		=> 'video',
									'tag'				=> array(124,126,125),
									'cat'				=> array(120),
									'thumb_id'			=> 72,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_sama_oembed' => 'https://vimeo.com/23851992' )
								),
								array(
									'title'				=> 'Now new branch in maadi is open',
									'old_id'			=> 3702,
									'new_id'			=> null,
									'post_format'		=> '',
									'tag'				=> array(128,121,127),
									'cat'				=> array(116),
									'thumb_id'			=> 72,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar' )
								),
								array(
									'title'				=> 'Come and taste our dinner',
									'old_id'			=> 149,
									'new_id'			=> null,
									'post_format'		=> 'audio',
									'tag'				=> array(128,121,127),
									'cat'				=> array(116),
									'thumb_id'			=> 72,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_sama_oembed' => 'https://soundcloud.com/henry-saiz/anubis-1' )
								),
								array(
									'title'				=> 'html5 Chef meeting his lovers',
									'old_id'			=> 1400,
									'new_id'			=> null,
									'post_format'		=> 'video',
									'tag'				=> array(122,123,152,153),
									'cat'				=> array(116),
									'thumb_id'			=> 70,
									'meta'				=> array( '_sama_post_layout' => 'fullwidth', '_sama_video_mp4' => '', '_sama_video_webm' => '', '_sama_video_poster' => '')
								),
								array(
									'title'				=> 'Now new branch in maadi is open',
									'old_id'			=> 1405,
									'new_id'			=> null,
									'post_format'		=> 'video',
									'tag'				=> array(124,126,125),
									'cat'				=> array(116,120),
									'thumb_id'			=> 74,
									'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_sama_oembed' => 'https://youtu.be/HAeWL6I25rc' )
								)
				
				),
				'team_members'	=> array(
									array(
										'title'				=> 'Jhon Smith',
										'old_id'			=> 1412,
										'new_id'			=> null,
										'thumb_id'			=> 179,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'PASTA CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'Mark Doe',
										'old_id'			=> 1414,
										'new_id'			=> null,
										'thumb_id'			=> 178,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'CAFE CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'Sarah Doe',
										'old_id'			=> 1416,
										'new_id'			=> null,
										'thumb_id'			=> 177,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'BAKERY CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'Jhon Smith',
										'old_id'			=> 1418,
										'new_id'			=> null,
										'thumb_id'			=> 176,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'PASTA CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'Mark Doe',
										'old_id'			=> 1420,
										'new_id'			=> null,
										'thumb_id'			=> 175,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'CAFE CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'Sarah Doe',
										'old_id'			=> 1422,
										'new_id'			=> null,
										'thumb_id'			=> 174,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'BAKERY CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'JHON LOE',
										'old_id'			=> 1424,
										'new_id'			=> null,
										'thumb_id'			=> 173,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'FISH CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'MARK HENRY',
										'old_id'			=> 1426,
										'new_id'			=> null,
										'thumb_id'			=> 172,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'GRILL CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									),
									array(
										'title'				=> 'MARK DOE',
										'old_id'			=> 1428,
										'new_id'			=> null,
										'thumb_id'			=> 175,
										'meta'				=> array( '_sama_post_layout'	=> 'rightsidebar', '_byline'	=> 'HEAD CHEF', '_twitter' => 'Jhon', '_linkedin' => 'https://www.linkedin.com/', '_googleplus' => 'https://google.com', '_facebook' => 'https://www.facebook.com/', '_contact_email' => 'majesty@msn.com' )
									)
				),
				'products'	=> array(
									array(
										'title'				=> 'Food Name 1',
										'old_id'			=> 3172,
										'new_id'			=> null,
										'cat'				=> array(105),
										'tag'				=> array(106),
										'thumb_id'			=> 159,
										'on_sale'			=> false,
										'sku'				=> 201
									),
									array(
										'title'				=> 'Food Name 2',
										'old_id'			=> 3175,
										'new_id'			=> null,
										'cat'				=> array(104,105),
										'tag'				=> array(106),
										'thumb_id'			=> 158,
										'on_sale'			=> false,
										'sku'				=> 202
									),
									array(
										'title'				=> 'Food Name 3',
										'old_id'			=> 3177,
										'new_id'			=> null,
										'cat'				=> array(107,105),
										'tag'				=> array(106),
										'thumb_id'			=> 157,
										'on_sale'			=> true,
										'sku'				=> 203
									),
									array(
										'title'				=> 'Food Name 4',
										'old_id'			=> 3179,
										'new_id'			=> null,
										'cat'				=> array(107,105),
										'tag'				=> array(106),
										'thumb_id'			=> 156,
										'on_sale'			=> false,
										'sku'				=> 204
									),
									array(
										'title'				=> 'Food Name 5',
										'old_id'			=> 3181,
										'new_id'			=> null,
										'cat'				=> array(107,105),
										'tag'				=> array(106),
										'thumb_id'			=> 155,
										'on_sale'			=> false,
										'sku'				=> 205
									),
									array(
										'title'				=> 'Food Name 6',
										'old_id'			=> 3183,
										'new_id'			=> null,
										'cat'				=> array(107,105),
										'tag'				=> array(106),
										'thumb_id'			=> 154,
										'on_sale'			=> false,
										'sku'				=> 206
									),
									array(
										'title'				=> 'Food Name 7',
										'old_id'			=> 3185,
										'new_id'			=> null,
										'cat'				=> array(107,108),
										'tag'				=> array(109),
										'thumb_id'			=> 165,
										'on_sale'			=> false,
										'sku'				=> 207
									),
									array(
										'title'				=> 'Food Name 8',
										'old_id'			=> 3187,
										'new_id'			=> null,
										'cat'				=> array(107,104),
										'tag'				=> array(109),
										'thumb_id'			=> 166,
										'on_sale'			=> false,
										'sku'				=> 208
									),
									array(
										'title'				=> 'Food Name 9',
										'old_id'			=> 3189,
										'new_id'			=> null,
										'cat'				=> array(105),
										'tag'				=> array(110,111),
										'thumb_id'			=> 167,
										'on_sale'			=> false,
										'sku'				=> 209
									),
									array(
										'title'				=> 'Food Name 10',
										'old_id'			=> 3191,
										'new_id'			=> null,
										'cat'				=> array(104,108),
										'tag'				=> array(111),
										'thumb_id'			=> 154,
										'on_sale'			=> false,
										'sku'				=> 210
									),
									array(
										'title'				=> 'Food Name 11',
										'old_id'			=> 3193,
										'new_id'			=> null,
										'cat'				=> array(107,105),
										'tag'				=> array(112),
										'thumb_id'			=> 153,
										'on_sale'			=> false,
										'sku'				=> 211
									),
									array(
										'title'				=> 'Food Name 12',
										'old_id'			=> 3195,
										'new_id'			=> null,
										'cat'				=> array(104),
										'tag'				=> array(111),
										'thumb_id'			=> 167,
										'on_sale'			=> false,
										'sku'				=> 212
									),
									array(
										'title'				=> 'Food Name 13',
										'old_id'			=> 3196,
										'new_id'			=> null,
										'cat'				=> array(105,107),
										'tag'				=> array(106),
										'thumb_id'			=> 166,
										'on_sale'			=> true,
										'sku'				=> 213
									),
									array(
										'title'				=> 'Food Name 14',
										'old_id'			=> 3197,
										'new_id'			=> null,
										'cat'				=> array(104,107),
										'tag'				=> array(111),
										'thumb_id'			=> 165,
										'on_sale'			=> false,
										'sku'				=> 214
									),
									array(
										'title'				=> 'Food Name 15',
										'old_id'			=> 3198,
										'new_id'			=> null,
										'cat'				=> array(107),
										'tag'				=> array(112),
										'thumb_id'			=> 163,
										'on_sale'			=> false,
										'sku'				=> 215
									),
									array(
										'title'				=> 'Food Name 16',
										'old_id'			=> 3199,
										'new_id'			=> null,
										'cat'				=> array(104,107),
										'tag'				=> array(112),
										'thumb_id'			=> 161,
										'on_sale'			=> false,
										'sku'				=> 216
									),
									array(
										'title'				=> 'Food Name 17',
										'old_id'			=> 3200,
										'new_id'			=> null,
										'cat'				=> array(107),
										'tag'				=> array(112),
										'thumb_id'			=> 160,
										'on_sale'			=> true,
										'sku'				=> 217
									),
									array(
										'title'				=> 'Food Name 18',
										'old_id'			=> 3201,
										'new_id'			=> null,
										'cat'				=> array(107,108),
										'tag'				=> array(109),
										'thumb_id'			=> 152,
										'on_sale'			=> false,
										'sku'				=> 218
									)
				),
				'contactforms'	=> array(
										array(
											'title'				=> 'Reservation Full Width',
											'old_id'			=> 1889,
											'new_id'			=> null,
											'content'			=> '<div class="reserv_form clearfix">
												<div class="form-group row">
													<div class="col-md-3 col-sm-6 col-sx-12">
														[text* name class:form-control placeholder "YOUR NAME *"]
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12">
														[email* email class:form-control placeholder "YOUR EMAIL *"]
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12">
														[tel* tel-104 id:telephone-rev class:form-control placeholder "PHONE NUM *"]
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12">
														
											<div class="select_wrap">
											[select* preferred class:form-control first_as_label "NUMBER OF PERSONS *" "1" "2" "3" "4" "5" "6" "7" "8" "9" "10"]</div>
															
													</div>
												</div>
												<div class="form-group row">
													<div class="col-md-3 col-sm-6 col-sx-12">
													<div class="select_wrap">
															[select* preferred class:form-control first_as_label "PREFERRED FOOD *" "one" "two" "three" "four" "five"]
														</div>
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12">
															<div class="select_wrap"> 
													[select* branchname id:branchname class:form-control first_as_label "BRANCH NAME *" "BRANCH ONE" "BRANCH TWO" "BRANCH THREE" "BRANCH FOUR" "BRANCH FIVE"]
															</div>
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12 datepicker">
														   [date* date-res class:form-control min:today max:today+100days placeholder "DATE *"]<i class="fa fa-calendar"></i>
													</div>
													<div class="col-md-3 col-sm-6 col-sx-12">
																  <div class="select_wrap"> 
													[select* revtime id:revtime class:form-control first_as_label "TIME*" "7:00" "8:00" "9:00" "10:00" "11:00" "12:00" "13:00" "14:00" "15:00" "16:00" "17:00" "18:00" "19:00" "12:00"]
															</div> 
													</div>
												</div>
												<div class="form-group">
													[textarea* message class:text class:textarea placeholder "MESSAGE *"]
												</div>
												<div class="col-md-12 text-center">
													[submit class:btn class:btn class:btn-gold class:white "BOOK YOUR TABLE"]
												</div>
											</div>'
										),
										array(
											'title'				=> 'Reservation 2 Columns',
											'old_id'			=> 1874,
											'new_id'			=> null,
											'content'			=> '<div class="reserv_form reserv_style2 clearfix">
																	<div class="col-md-6">
																		<div class="row">
																			<div class="form-group row">
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					[text* name class:form-control placeholder "YOUR NAME *"]
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					[email* email class:form-control placeholder "YOUR EMAIL *"]
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					[tel* tel-104 id:telephone-rev class:form-control placeholder "PHONE NUM *"]
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					<div class="select_wrap">
																[select* preferred class:form-control first_as_label "NUMBER OF PERSONS *" "1" "2" "3" "4" "5" "6" "7" "8" "9" "10"]</div>
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					<div class="select_wrap">
																				[select* preferred class:form-control first_as_label "PREFERRED FOOD *" "one" "two" "three" "four" "five"]
																			</div>
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					<div class="select_wrap"> 
																		[select* branchname id:branchname class:form-control first_as_label "BRANCH NAME *" "BRANCH ONE" "BRANCH TWO" "BRANCH THREE" "BRANCH FOUR" "BRANCH FIVE"]
																				</div>
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12 datepicker">
																					 [date* date-res class:form-control min:today placeholder "DATE *"]<i class="fa fa-calendar"></i>
																				</div>
																				<div class="col-md-6 col-sm-6 col-sx-12">
																					 <div class="select_wrap"> 
																		[select* revtime id:revtime class:form-control first_as_label "TIME *" "7:00" "8:00" "9:00" "10:00" "11:00" "12:00" "13:00" "14:00" "15:00" "16:00" "17:00" "18:00" "19:00" "12:00"]
																				</div> 
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="row">
																	<div class="col-md-6">
																			
																		<div class="form-group">[textarea* message class:text class:textarea placeholder "MESSAGE *"]</div>
																	   </div>
																	</div>
																	<div class="col-md-12 text-center">[submit class:btn class:btn class:btn-gold class:white "BOOK YOUR TABLE"]</div>
																</div>'
										),
										array(
											'title'				=> 'Contact Full Width',
											'old_id'			=> 1814,
											'new_id'			=> null,
											'content'			=> '<div class="contact-form clearfix contact-page">
																	<div class="form-group">
																		<div class="col-md-4 col-sm-4 col-sx-12">
																			[text* name class:form-control class:text  placeholder "NAME *"]
																		</div>
																		<div class="col-md-4 col-sm-4 col-sx-12">
																			[email* email class:form-control class:text placeholder "EMAIL *"]
																		</div>
																		<div class="col-md-4 col-sm-4 col-sx-12">
																			[text subject class:form-control class:text placeholder "SUBJECT *"]
																		</div>
																	</div>
																	<div class="col-md-12">
																		<div class="form-group">
																			<div class="element">
																				[textarea* message class:text class:textarea placeholder "MESSAGE *"]
																			</div>
																		</div>
																	</div>
																	<div class="col-md-12">
																	[submit class:btn class:btn-black "Send"]
																	</div>
																</div>'
										),
										array(
											'title'				=> 'Contact 2 Columns',
											'old_id'			=> 1817,
											'new_id'			=> null,
											'content'			=> '<div class="contact-form style1 clearfix">
																	<div class="col-md-6 nopadding">
																		<div class="form-group">
																			<div class="col-md-12">
																				[text* name class:form-control class:text  placeholder "NAME *"]
																			</div>
																			<div class="col-md-12">
																				[email* email class:form-control class:text placeholder "EMAIL *"]
																			</div>
																			<div class="col-md-12">
																				[text subject class:form-control class:text placeholder "SUBJECT *"]
																			</div>
																		</div>
																	</div>
																	<div class="col-md-6 nopadding">
																		<div class="form-group">
																			<div class="col-md-12">
																				<div class="element">
																					[textarea* message class:text class:textarea 1x1 placeholder "MESSAGE *"]
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col-md-12">
																	[submit class:btn class:btn-gold "Send"]
																	</div>
																</div>'
										)
				),
							
				'pages'	=> array(
								array(
										'title'				=> 'Home',
										'old_id'			=> 1527,
										'tpl'				=> 'home',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'zoom',
																	'timer'			=> 1000,
																	'efftimer'		=> 15000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 34,23,38 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase margin-tb-30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'About Us',
										'old_id'			=> 1086,
										'tpl'				=> 'about',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Where We All Strart',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Blank Page',
										'old_id'			=> 3078,
										'tpl'				=> 'blank',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-blank.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_slider_type' 		=> '',
															)
								),
								array(
										'title'				=> 'Blog',
										'old_id'			=> 1066,
										'tpl'				=> '',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'All About Majesty',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Booking',
										'old_id'			=> 2954,
										'tpl'				=> 'booking',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-fullwidth.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Book Your Table',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Cart',
										'old_id'			=> 1305,
										'tpl'				=> '',
										'content'			=> '[woocommerce_cart]',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-fullwidth.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Shop With Us',
																'_sama_page_bg_id'			=> 5,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Certifications',
										'old_id'			=> 2729,
										'tpl'				=> 'certifications',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Start With Quality, Destination Wil Be Excellence',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Checkout',
										'old_id'			=> 1306,
										'tpl'				=> '',
										'content'			=> '[woocommerce_checkout]',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-fullwidth.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Every Thing You Know About Majesty',
																'_sama_page_bg_id'			=> 5,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'CLIENTS',
										'old_id'			=> 2734,
										'tpl'				=> 'clients',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Everything You Want, You Will Find On Majesty',
																'_sama_page_bg_id'			=> 6,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Coming Soon Default',
										'old_id'			=> 3089,
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-blank.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'fullscreenbg',
																'_sama_fullscreenbg_settings' => array(array(
																	'image_id'		=> $comming_default_id,
																	'image'			=> $comming_default_img,
																	'overlay'		=> 'transparent-bg-3',
																	'content'		=> '<div class="coming-soon"><div class="slider-content dark">
																						<div id="logo">
																							<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																						</div>
																						<h3>WE ARE GLAD TO SEE YOU, BUT PLEASE BE PATIENT, THIS PAGE IS UNDER CONSTRUCTION</h3>
																					[sama_countdown date="'. $date .'" rtl="false" dayslabel="Days" hourslabel="Hours" minuteslabel="Minutes" secondslabel="Seconds" cssclass=""]<h3>This is Countdown Message if Countdown Expire.</h3>[/sama_countdown]
																					<p>If you have any questions, comments, or suggestions, please click <a class="underline" href="mailto:someone@yoursite.com">here</a> to mail me.</p>
																						<ul class="social">
																							<li><a href="#" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Instagram"><i class="fa fa-instagram"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Behance"><i class="fa fa-behance"></i></a></li>
																						</ul>
																					</div></div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Coming Soon Slider',
										'old_id'			=> 3095,
										'tpl'				=> '',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-blank.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'fade',
																	'timer'			=> 5000,
																	'efftimer'		=> 5000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 23,34,37 ),
																	'content'		=> '<div class="coming-soon"><div class="slider-content dark">
																						<div id="logo">
																							<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																						</div>
																						<h3>WE ARE GLAD TO SEE YOU, BUT PLEASE BE PATIENT, THIS PAGE IS UNDER CONSTRUCTION</h3>
																					[sama_countdown date="'. $date .'" rtl="false" dayslabel="Days" hourslabel="Hours" minuteslabel="Minutes" secondslabel="Seconds" cssclass=""]<h3>This is Countdown Message if Countdown Expire.</h3>[/sama_countdown]
																					<p>If you have any questions, comments, or suggestions, please click <a class="underline" href="mailto:someone@yoursite.com">here</a> to mail me.</p>
																						<ul class="social">
																							<li><a href="#" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Instagram"><i class="fa fa-instagram"></i></a></li>
																							<li><a href="#" data-toggle="tooltip" title="Behance"><i class="fa fa-behance"></i></a></li>
																						</ul>
																					</div></div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Coming Soon Video',
										'old_id'			=> 3093,
										'tpl'				=> '',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-blank.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'h5video',
																'_sama_h5video_settings' => array(array(
																	'mp4'			=> $mb4,
																	'webm'			=> $webm,
																	'poster_id'		=> $poster_id,
																	'poster'		=> $poster_url,
																	'overlay'		=> 'transparent-bg-3',
																	'autoplay'		=> 'true',
																	'loop'			=> 'true',
																	'content'		=> '<div class="coming-soon"><div class="slider-content">
																						<div id="logo">
																						<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																						</div>
																							<h3>We are glad to see you, But please be patient, This page is under construction</h3>
																							[sama_countdown date="'. $date .'" rtl="false" dayslabel="Days" hourslabel="Hours" minuteslabel="Minutes" secondslabel="Seconds" cssclass=""]<h3>This is Countdown Message if Countdown Expire.</h3>[/sama_countdown]
																							<p>If you have any questions, comments, or suggestions, please click <a class="underline" href="mailto:someone@yoursite.com">here</a> to mail me.</p>
																								<ul class="social">
																								  <li><a href="#" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
																								  <li><a href="#" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
																								 <li><a href="#" data-toggle="tooltip" title="Instagram"><i class="fa fa-instagram"></i></a></li>
																								  <li><a href="#" data-toggle="tooltip" title="Behance"><i class="fa fa-behance"></i></a></li>
																								</ul>
																						</div></div>',
																)),
										
															)
									),
									array(
										'title'				=> 'CONTACT US',
										'old_id'			=> 2747,
										'tpl'				=> 'contactus',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Get In Touch',
																'_sama_page_bg_id'			=> 6,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Events',
										'old_id'			=> 2974,
										'tpl'				=> '',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Every Day Events',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Locations',
										'old_id'			=> 2975,
										'tpl'				=> '',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Our Events Locations',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'My Bookings',
										'old_id'			=> 2978,
										'tpl'				=> '',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Our Booking',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Events Calendar',
										'old_id'			=> 2981,
										'tpl'				=> '',
										'content'			=> '<p class="fontsize15" style="text-align: center;">Below is an example of how you can use other plugins alongside Events Manager to create magnificent results! This is plugin we created ourselves for displaying events using the jQuery FullCalendar by Adam Shaw, and have consequently published it for free on the WordPress.org repo.</p>[fullcalendar]',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Every Day Events',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Events Calendar Default',
										'old_id'			=> 2990,
										'tpl'				=> '',
										'content'			=> '[events_calendar full=1 long_events=1]',
										'meta'				=> array(
																'_wp_page_template' 		=> 'default',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Every Day Events',
																'_sama_page_bg_id'			=> 22,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Home One Page Parallax',
										'old_id'			=> 1588,
										'tpl'				=> 'home-one-page-parallax',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'parallaxbg',
																'_sama_parallaxbg_settings' => array(array(
																	'image_id'		=> 39,
																	'image'			=> '',
																	'overlay'		=> 'transparent-bg-3',
																	'content'		=> '<div class="slider-content">
																						<ul class="text-rotator">
																						<li>
																						<h1 class="text-uppercase">Come in & Taste</h1>
																						</li>
																						<li>
																						<h1 class="text-uppercase">HIGH CLASS PROFESSIONAL SERVICE</h1>
																						</li>
																						<li>
																						<h1 class="text-uppercase">From an Outstanding Chef</h1>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="mt30 font2">We Create Sweet Memories</p>
																						<a href="#ourmenu" class="btn btn-gold white scroll-down">Our Menu</a>
																						</div>
																						<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home American',
										'old_id'			=> 1602,
										'tpl'				=> 'home-american',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'dark-bottom-center',
																'_sama_slider_type' => 'swiper',
																'_sama_swiper_settings' => array(array(
																	'direction'		=> 'horizontal',
																	'effect'		=> 'slide',
																	'loop'			=> 'true',
																	'speed'			=> '300',
																	'arrows'		=> 'true',
																	'bullet'		=> 'false',
																)),
																'_sama_swiper_slides' => array(
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_1_id,
																			'image'			=> $swiper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>HOME OF THE BEST CUISINE</h1>
																								<h4 class="text-capitalize">We open 24 Hours.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'video',
																			'image_id'		=> $poster_id,
																			'image'			=> $poster_url,
																			'overlay'		=> 'transparent-bg-6',
																			'mp4'			=> $mb4,
																			'webm'			=> $webm,
																			'content'		=> '<div class="slider-content ">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_2_id,
																			'image'			=> $swiper_slide_2_url,
																			'overlay'		=> 'transparent-bg-5',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo"><a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a></div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home Animate',
										'old_id'			=> 1532,
										'tpl'				=> 'home-animate',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'zoom',
																	'timer'			=> 1000,
																	'efftimer'		=> 15000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 34,23,38 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase margin-tb-30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Asian',
										'old_id'			=> 1537,
										'tpl'				=> 'home-asian',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'slideDown',
																	'timer'			=> 4000,
																	'efftimer'		=> 3000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 43,45,46 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase mt30 mb30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Bakery',
										'old_id'			=> 1550,
										'tpl'				=> 'home-bakery',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'fade',
																	'timer'			=> 4000,
																	'efftimer'		=> 2000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 26,27,28 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase mt30 mb30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Burger',
										'old_id'			=> 1544,
										'tpl'				=> 'home-burger',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'zoom',
																	'timer'			=> 6000,
																	'efftimer'		=> 2000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 29,30,31 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase mt30 mb30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Cafe',
										'old_id'			=> 1555,
										'tpl'				=> 'home-cafe',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'zoom',
																	'timer'			=> 4000,
																	'efftimer'		=> 3000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 32,33,35 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase mt30 mb30">We Create Sweet Memories</p>
																						<a href="'. $this->site_url .'shop/" class="btn btn-gold white">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Layout 2',
										'old_id'			=> 1566,
										'tpl'				=> 'home-layout-2',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'light-center-transparent',
																'_sama_slider_type' => 'youtubebg',
																'_sama_youtube_settings' => array(array(
																	'url'	=> 'https://youtu.be/HAeWL6I25rc',
																	'poster_id'		=> $poster_id,
																	'poster'		=> $poster_url,
																	'overlay'		=> 'transparent-bg-3',
																	'autoplay'		=> 'true',
																	'loop'			=> 'true',
																	'mute'			=> 'false',
																	'showcontrols'	=> 'false',
																	'ratio'			=> 'auto',
																	'quality'		=> 'hd1080',
																	'content'		=> '<div class="container dark slider-content">
																						<i class="icon-top-draw"></i>
																						<div id="text-transform" class="owl-carousel">
																						<div class="item">
																						<h1>Premium Restaurant Theme</h1>
																						</div>
																						<div class="item">
																						<h1>KEEP CALM & TASTE OUR FOOD</h1>
																						</div>
																						<div class="item">
																						<h1>Premium Restaurant Themes</h1>
																						</div>
																						</div>
																						<p class="font2">We Create Delicous Memories</p>
																						<i class="icon-bottom-draw"></i>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Menu No Woocommerce Menu Filter Dark',
										'old_id'			=> 1086,
										'tpl'				=> 'no-Woocommerce-filter-dark',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce Menu Filter Light',
										'old_id'			=> 5398,
										'tpl'				=> 'no-Woocommerce-filter-light',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce Menu List',
										'old_id'			=> 3392,
										'tpl'				=> 'no-Woocommerce-menu-list',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce Menu List 2',
										'old_id'			=> 5410,
										'tpl'				=> 'no-Woocommerce-menu-list-2',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce Menu List 3',
										'old_id'			=> 5440,
										'tpl'				=> 'no-Woocommerce-menu-list-3',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce menu list 4',
										'old_id'			=> 5420,
										'tpl'				=> 'no-Woocommerce-menu-list-4',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Menu No Woocommerce Menu List 5',
										'old_id'			=> 5429,
										'tpl'				=> 'no-Woocommerce-menu-list-5',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'My Account',
										'old_id'			=> 1307,
										'tpl'				=> '',
										'content'			=> '[woocommerce_my_account]',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Open Table',
										'old_id'			=> 3526,
										'tpl'				=> 'open-table',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'privacy',
										'old_id'			=> 2161,
										'tpl'				=> 'privacy',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'RESERVATION',
										'old_id'			=> 2759,
										'tpl'				=> 'reservation',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Menu Without WooCommerce',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Shop',
										'old_id'			=> 1304,
										'meta'				=> array(
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'ShortCodes',
										'old_id'			=> 2026,
										'tpl'				=> 'shortcodes',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Accordions',
										'old_id'			=> 2077,
										'tpl'				=> 'accordions',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Alert Message',
										'old_id'			=> 2784,
										'tpl'				=> 'alert-message',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Blockquote',
										'old_id'			=> 2824,
										'tpl'				=> 'blockquote',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Button',
										'old_id'			=> 2048,
										'tpl'				=> 'button',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'MODAL BOX',
										'old_id'			=> 2136,
										'tpl'				=> 'modal-box',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Panels',
										'old_id'			=> 2058,
										'tpl'				=> 'panels',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Pricing Table',
										'old_id'			=> 2119,
										'tpl'				=> 'pricing-table',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Progress Bar',
										'old_id'			=> 2107,
										'tpl'				=> 'progress-bar',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Table',
										'old_id'			=> 2116,
										'tpl'				=> 'table',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Tabs',
										'old_id'			=> 2071,
										'tpl'				=> 'tabs',
										'meta'				=> array(
																'_wp_page_template' 		=> 'page-templates/page-leftsidebar.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'VC Pricing Columns',
										'old_id'			=> 2759,
										'tpl'				=> 'vc-pricing',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Visual comoser Pricing Column',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce',
										'old_id'			=> 3559,
										'tpl'				=> 'woocommerce',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu ALL',
										'old_id'			=> 3366,
										'tpl'				=> 'woocommerce-menu-all',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu Grid',
										'old_id'			=> 3344,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-20"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="37" per_page="12"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu grid 4 col',
										'old_id'			=> 3412,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-20"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="36" per_page="16" orderby="menu_order"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu Grid Full Width',
										'old_id'			=> 3352,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-20"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="35" per_page="12"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu List',
										'old_id'			=> 3380,
										'tpl'				=> 'woocommerce-menu-list',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu list 2',
										'old_id'			=> 5442,
										'tpl'				=> 'woocommerce-menu-list-2',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Woocommerce Menu List Filter',
										'old_id'			=> 3364,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-20"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="31" per_page="12"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
							array(
										'title'				=> 'Woocommerce Menu Masonry',
										'old_id'			=> 3355,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-100"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="33" per_page="10"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
						array(
										'title'				=> 'Woocommerce Menu Masonry Full Width',
										'old_id'			=> 3362,
										'content'			=> '[vc_row full_width="stretch_row" box_padding="padding-b-100"][vc_column extra_css="nopadding"][vc_woo_filters cats="breakfast,lunch,dinner,drinks" display="34" per_page="10"][/vc_column][/vc_row]',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_menu_type'			=> 'Light-default-transparent',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Your Taste Is Our Goal',
																'_sama_page_bg_id'			=> 3,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Home One Page Animation BG',
										'old_id'			=> 1582,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'movementbg',
																'_sama_movementbg_settings' => array(array(
																	'image_id'		=> $movementbg_id,
																	'image'			=> $movementbg_img,
																	'overlay'		=> 'transparent-bg-3',
																	'content'		=> '<div class="slider-content">
																							<ul id="fade">
																							<li>
																							<h1 class="text-uppercase">Come in & Taste</h1>
																							</li>
																							<li>
																							<h1 class="text-uppercase">HIGH CLASS PROFESSIONAL SERVICE</h1>
																							</li>
																							<li>
																							<h1 class="text-uppercase">From an Outstanding Chef</h1>
																							</li>
																							</ul>
																							<i class="icon-home-ico"></i>
																							<p class="mt30 font2">We Create Sweet Memories</p>
																							<a href="#ourmenu" class="btn btn-gold white scroll-down">Our Menu</a>
																							</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home One Page Fade',
										'old_id'			=> 1519,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'skipper',
																'_sama_skipper_settings' => array(array(
																	'type'		=> 'fullheight',
																	'transition'	=> 'fade',
																	'speed'			=> 500,
																	'arrows'		=> 'false',
																	'navtype'		=> 'block',
																	'autoplay'		=> 'true',
																	'duration'		=> 5000,
																	'hideprev'		=> 'true',

																)),
																'_sama_skipper_slides' => array(
																		array(
																			'image_id'		=> $skipper_slide_1_id,
																			'image'			=> $skipper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_2_id,
																			'image'			=> $skipper_slide_2_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>HIGH CLASS PROFESSIONAL SERVICE</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_3_id,
																			'image'			=> $skipper_slide_3_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home One Page Full Width',
										'old_id'			=> 1523,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'skipper',
																'_sama_skipper_settings' => array(array(
																	'type'			=> 'fullwidth',
																	'transition'	=> 'fade',
																	'speed'			=> 500,
																	'arrows'		=> 'false',
																	'navtype'		=> 'bubble',
																	'autoplay'		=> 'true',
																	'duration'		=> 5000,
																	'hideprev'		=> 'false',

																)),
																'_sama_skipper_slides' => array(
																		array(
																			'image_id'		=> $skipper_slide_1_id,
																			'image'			=> $skipper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_2_id,
																			'image'			=> $skipper_slide_2_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>HIGH CLASS PROFESSIONAL SERVICE</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_3_id,
																			'image'			=> $skipper_slide_3_url,
																			'overlay'		=> 'transparent-bg-5',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home One Page Fullscreen',
										'old_id'			=> 1579,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'fullscreenbg',
																'_sama_fullscreenbg_settings' => array(array(
																	'image_id'		=> $skipper_slide_2_id,
																	'image'			=> $skipper_slide_2_url,
																	'overlay'		=> 'transparent-bg-3',
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h1 class="text-uppercase">Come in & Taste</h1>
																						</li>
																						<li>
																						<h1 class="text-uppercase">HIGH CLASS PROFESSIONAL SERVICE</h1>
																						</li>
																						<li>
																						<h1 class="text-uppercase">From an Outstanding Chef</h1>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="mt30 font2">We Create Sweet Memories</p>
																						</div>
																						<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home One page Horizental',
										'old_id'			=> 1504,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'skipper',
																'_sama_skipper_settings' => array(array(
																	'type'			=> 'fullheight',
																	'transition'	=> 'slide',
																	'speed'			=> 500,
																	'arrows'		=> 'true',
																	'navtype'		=> 'block',
																	'autoplay'		=> 'true',
																	'duration'		=> 5000,
																	'hideprev'		=> 'false',

																)),
																'_sama_skipper_slides' => array(
																		array(
																			'image_id'		=> $skipper_slide_1_id,
																			'image'			=> $skipper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_2_id,
																			'image'			=> $skipper_slide_2_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<h1>HIGH CLASS PROFESSIONAL SERVICE</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_3_id,
																			'image'			=> $skipper_slide_3_url,
																			'overlay'		=> 'transparent-bg-5',
																			'content'		=> '<div class="slider-content">
																								<h1>Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2">We Create Sweet Memories</p>
																								</div>
																								<a href="about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home One Page Interactive',
										'old_id'			=> 1585,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'interactivebg',
																'_sama_interactivebg_settings' => array(array(
																	'image_id'		=> $interactivebg_id,
																	'image'			=> $interactivebg_img,
																	'overlay'		=> 'transparent-bg-3',
																	'strength'		=> '',
																	'scale'			=> '',
																	'animationspeed' => '100ms',
																	'content'		=> '<div class="slider-content">
																							<ul class="text-rotator">
																							<li>
																							<h1 class="text-uppercase">Come in & Taste</h1>
																							</li>
																							<li>
																							<h1 class="text-uppercase">HIGH CLASS PROFESSIONAL SERVICE</h1>
																							</li>
																							<li>
																							<h1 class="text-uppercase">From an Outstanding Chef</h1>
																							</li>
																							</ul>
																							<i class="icon-home-ico"></i>
																							<p class="mt30 font2">We Create Sweet Memories</p>
																							</div>
																							 <a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home One page Slide Top',
										'old_id'			=> 1604,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'swiper',
																'_sama_swiper_settings' => array(array(
																	'direction'		=> 'vertical',
																	'effect'		=> 'slide',
																	'loop'			=> 'true',
																	'speed'			=> '300',
																	'arrows'		=> 'false',
																	'bullet'		=> 'true',
																)),
																'_sama_swiper_slides' => array(
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_1_id,
																			'image'			=> $swiper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<h1 class="text-capitalize">From an Outstanding Chef</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2 skipperparaghraph">We Create Sweet Memories</p>
																								</div>'
																		),
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $skipper_slide_2_id,
																			'image'			=> $skipper_slide_2_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<h1 class="text-capitalize">HIGH CLASS PROFESSIONAL SERVICE</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2 skipperparaghraph">We Create Sweet Memories</p>
																								</div>'
																		),
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $skipper_slide_3_id,
																			'image'			=> $skipper_slide_3_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<h1 class="text-capitalize">Come in & Taste</h1>
																								<i class="icon-home-ico"></i>
																								<p class="mt30 font2 skipperparaghraph">We Create Sweet Memories</p>
																								</div>'
																		),
																)
															)
									),
									array(
										'title'				=> 'Home One page Slider',
										'old_id'			=> 1632,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'swiper',
																'_sama_swiper_settings' => array(array(
																	'direction'		=> 'horizontal',
																	'effect'		=> 'slide',
																	'loop'			=> 'true',
																	'speed'			=> '300',
																	'arrows'		=> 'true',
																	'bullet'		=> 'false',
																)),
																'_sama_swiper_slides' => array(
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_1_id,
																			'image'			=> $swiper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>HOME OF THE BEST CUISINE</h1>
																								<h4 class="text-capitalize">We open 24 Hours.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'video',
																			'image_id'		=> $poster_id,
																			'image'			=> $poster_url,
																			'overlay'		=> 'transparent-bg-6',
																			'mp4'			=> $mb4,
																			'webm'			=> $webm,
																			'content'		=> '<div class="slider-content ">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_2_id,
																			'image'			=> $swiper_slide_2_url,
																			'overlay'		=> 'transparent-bg-5',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo"><a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a></div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home One Page Vertical',
										'old_id'			=> 1513,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'vertical-menu',
																'_sama_slider_type' => 'skipper',
																'_sama_skipper_settings' => array(array(
																	'type'			=> 'fullheight',
																	'transition'	=> 'slide',
																	'speed'			=> 500,
																	'arrows'		=> 'true',
																	'navtype'		=> 'block',
																	'autoplay'		=> 'true',
																	'duration'		=> 5000,
																	'hideprev'		=> 'false',

																)),
																'_sama_skipper_slides' => array(
																		array(
																			'image_id'		=> $skipper_slide_1_id,
																			'image'			=> $skipper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<span class="text-center"><img src="'. $logo_url.'" class="img-responsive aligncenter" alt=""></span>
																								<i class="icon-home-ico"></i>
																								<h1 class="text-uppercase" data-caption-animate="fadeInUp">Come in & Taste</h1>
																								</div>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_2_id,
																			'image'			=> $skipper_slide_2_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<span class="text-center"><img src="'. $logo_url.'" class="img-responsive aligncenter" alt=""></span>
																								<i class="icon-home-ico"></i>
																								<h1 class="text-uppercase" data-caption-animate="fadeInUp">HIGH CLASS PROFESSIONAL SERVICE</h1>
																								</div>'
																		),
																		array(
																			'image_id'		=> $skipper_slide_3_id,
																			'image'			=> $skipper_slide_3_url,
																			'overlay'		=> 'transparent-bg-3',
																			'content'		=> '<div class="slider-content">
																								<span class="text-center"><img src="'. $logo_url.'" class="img-responsive aligncenter" alt=""></span>
																								<i class="icon-home-ico"></i>
																								<h1 class="text-uppercase" data-caption-animate="fadeInUp">We Create Sweet Memories</h1>
																								</div>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home One page Youtube',
										'old_id'			=> 1562,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'youtubebg',
																'_sama_youtube_settings' => array(array(
																	'url'	=> 'https://youtu.be/HAeWL6I25rc',
																	'poster_id'		=> $poster_id,
																	'poster'		=> $poster_url,
																	'overlay'		=> 'transparent-bg-3',
																	'autoplay'		=> 'true',
																	'loop'			=> 'true',
																	'mute'			=> 'false',
																	'showcontrols'	=> 'false',
																	'ratio'			=> 'auto',
																	'quality'		=> 'hd1080',
																	'startat'		=> '',
																	'stopat'		=> '',
																	'content'		=> '<div class="container dark slider-content">
																						<i class="icon-top-draw"></i>
																						<div id="text-transform" class="owl-carousel">
																						<div class="item">
																						<h1>Premium Restaurant Theme</h1>
																						</div>
																						<div class="item">
																						<h1>KEEP CALM & TASTE OUR FOOD</h1>
																						</div>
																						<div class="item">
																						<h1>Premium Restaurant Themes</h1>
																						</div>
																						</div>
																						<p class="font2">We Create Delicous Memories</p>
																						<i class="icon-bottom-draw"></i>
																						</div>
																						<a data-scroll-nav="1" href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home One Page Zooming',
										'old_id'			=> 1535,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'bgndgallery',
																'_sama_bgslider_settings' => array(array(
																	'transition'	=> 'zoom',
																	'timer'			=> 1000,
																	'efftimer'		=> 15000,
																	'overlay'		=> 'transparent-bg-3',
																	'images'		=> array( 43,45,46 ),
																	'content'		=> '<div class="slider-content">
																						<ul id="fade">
																						<li>
																						<h2 class="text-uppercase">Come in & Taste</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious food</h2>
																						</li>
																						<li>
																						<h2 class="text-uppercase">most delicious desserts</h2>
																						</li>
																						</ul>
																						<i class="icon-home-ico"></i>
																						<p class="text-uppercase">We Create Sweet Memories</p>
																						<a href="#about" class="btn btn-gold white scroll-down">DISCOVER MORE</a>
																						</div>',
																)),
										
															)
									),
									array(
										'title'				=> 'Home Onepage Video',
										'old_id'			=> 1572,
										'tpl'				=> 'home-one-page-animation-bg',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'h5video',
																'_sama_h5video_settings' => array(array(
																	'mp4'			=> $mb4,
																	'webm'			=> $webm,
																	'poster_id'		=> $poster_id,
																	'poster'		=> $poster_url,
																	'overlay'		=> 'transparent-bg-3',
																	'autoplay'		=> 'true',
																	'loop'			=> 'true',
																	'content'		=> '<div class="video-content">
																						  <ul class="text-rotator">
																							<li><h1 class="text-uppercase">Come in & Taste</h1></li>
																							 <li><h1 class="text-uppercase">HIGH CLASS PROFESSIONAL SERVICE</h1></li> 
																							<li><h1 class="text-uppercase">From an Outstanding Chef</h1></li>
																							 </ul><i class="icon-home-ico"></i><p class="mt30 font2">We Create Sweet Memories</p></div><a href="#about" class="go-down"><i class="fa fa-angle-double-down"></i></a>'
																)),
										
															)
									),
									array(
										'title'				=> 'Home Pizza',
										'old_id'			=> 1562,
										'tpl'				=> 'home-pizza',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'light-center-transparent',
																'_sama_slider_type' => 'youtubebg',
																'_sama_youtube_settings' => array(array(
																	'url'			=> 'https://youtu.be/7yYKL__hSCs',
																	'poster_id'		=> $poster_id,
																	'poster'		=> $poster_url,
																	'overlay'		=> 'transparent-bg-3',
																	'autoplay'		=> 'true',
																	'loop'			=> 'true',
																	'mute'			=> 'false',
																	'showcontrols'	=> 'false',
																	'ratio'			=> 'auto',
																	'quality'		=> 'hd1080',
																	'startat'		=> '',
																	'stopat'		=> '',
																	'content'		=> '<div class="container dark slider-content">
																						<i class="icon-top-draw"></i>
																						<div id="text-transform" class="owl-carousel">
																						<div class="item">
																						<h2>Premium Restaurant Theme</h2>
																						</div>
																						<div class="item">
																						<h2>KEEP CALM & TASTE OUR FOOD</h2>
																						</div>
																						<div class="item">
																						<h2>Premium Restaurant Themes</h2>
																						</div>
																						</div>
																						<p class="font2">We Create Delicous Memories</p>
																						<i class="icon-bottom-draw"></i>
																						</div>'
																)),
										
															)
									),
									array(
										'title'				=> 'Home Solid Header Dark Center',
										'old_id'			=> 5214,
										'tpl'				=> 'home-solid-header-dark-center',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'dark-center-solid',
																'_sama_slider_type' => ''
															)
									),
									array(
										'title'				=> 'Sections',
										'old_id'			=> 3135,
										'tpl'				=> 'sections',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Everything You Want, You Will Find On Majesty',
																'_sama_page_bg_id'			=> 6,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Sections 2',
										'old_id'			=> 3606,
										'tpl'				=> 'sections-2',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'Everything You Want, You Will Find On Majesty',
																'_sama_page_bg_id'			=> 6,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'SERVICES',
										'old_id'			=> 2764,
										'tpl'				=> 'services',
										'meta'				=> array(
																'_wpb_vc_js_status'			=> 'true',
																'_wp_page_template' 		=> 'page-templates/page-builder-with-title.php',
																'_sama_page_with_bg' 		=> 'yes',
																'_sama_page_display_icon'	=> 'yes',
																'_sama_page_icon_css'		=> 'icon-home-ico',
																'_sama_page_subtitle'		=> 'EXPERIENCES OUR BEST OF WORLD CLASS CUISINE',
																'_sama_page_bg_id'			=> 6,
																'_sama_page_bg'				=> '',
																'_sama_page_bg_parallax'	=> 'yes',
															)
								),
								array(
										'title'				=> 'Home Layout 3',
										'old_id'			=> 1595,
										'tpl'				=> 'home-layout-3',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'dark-bottom-center',
																'_sama_slider_type' => 'swiper',
																'_sama_swiper_settings' => array(array(
																	'direction'		=> 'horizontal',
																	'effect'		=> 'slide',
																	'loop'			=> 'true',
																	'speed'			=> '300',
																	'arrows'		=> 'true',
																	'bullet'		=> 'false',
																)),
																'_sama_swiper_slides' => array(
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_1_id,
																			'image'			=> $swiper_slide_1_url,
																			'overlay'		=> 'transparent-bg-3',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>HOME OF THE BEST CUISINE</h1>
																								<h4 class="text-capitalize">We open 24 Hours.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'video',
																			'image_id'		=> $poster_id,
																			'image'			=> $poster_url,
																			'overlay'		=> 'transparent-bg-6',
																			'mp4'			=> $mb4,
																			'webm'			=> $webm,
																			'content'		=> '<div class="slider-content ">
																								<div class="logo">
																								<a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a>
																								</div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		),
																		array(
																			'type'			=> 'image',
																			'image_id'		=> $swiper_slide_2_id,
																			'image'			=> $swiper_slide_2_url,
																			'overlay'		=> 'transparent-bg-5',
																			'mp4'			=> '',
																			'webm'			=> '',
																			'content'		=> '<div class="slider-content">
																								<div class="logo"><a href="'. $this->site_url .'" class="light-logo"><img src="'. $logo_url.'" alt="Logo"></a></div>
																								<h1>FROM AN an Outstanding Chef</h1>
																								<h4 class="text-capitalize">We Create Sweet Memories.</h4>
																								</div>'
																		)
																)
															)
									),
									array(
										'title'				=> 'Home Layout 4',
										'old_id'			=> 3536,
										'tpl'				=> 'home-layout-4',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'dark-center-solid',
																'_sama_slider_type' => ''
															)
									),
									array(
										'title'				=> 'Home Layout 5',
										'old_id'			=> 5249,
										'tpl'				=> 'home-layout-5',
										'meta'				=> array(
																'_wpb_vc_js_status'	=> 'true',
																'_wp_page_template' => 'page-templates/page-builder.php',
																'_sama_menu_type'	=> 'Light-default-transparent',
																'_sama_slider_type' => 'fullscreenbg',
																'_sama_fullscreenbg_settings' => array(array(
																	'image_id'		=> $skipper_slide_2_id,
																	'image'			=> $skipper_slide_2_url,
																	'overlay'		=> 'transparent-bg-2',
																	'content'		=> '<div class="container dark slider-content">
																						<i class="icon-top-draw"></i>
																						<div id="text-transform" class="owl-carousel">
																						<div class="item">
																						<h1>Premium <span>Restaurant</span> Theme</h1>
																						</div>
																						<div class="item">
																						<h1>KEEP CALM & TASTE OUR FOOD</h1>
																						</div>
																						<div class="item">
																						<h1>Premium <span>Majesty</span> Restaurant</h1>
																						</div>
																						</div>
																						<p class="font2">We Create Delicous Memories</p>
																						<i class="icon-bottom-draw"></i>
																						</div>',
																)),
										
															)
									),
				)	
				
			);
						
			return $args;
		}
		
		
		
		function old_new_id() {
			$args = array(
				'post_cat' 			=> array(),
				'post_tag' 			=> array(),
				'team_cats'			=> array(),
				'product_cats'		=> array(), 
				'product_tags'		=> array(),
				'media'				=> array(),
				'posts'				=> array(),
				'team_members'		=> array(),
				'products'			=> array(),
				'contactforms'		=> array(),
				'pages'				=> array(),
				'scroll_menu_id'	=> '',
				'mega_menu_id'		=> '',
				'mega_slider_id'	=> '',
			);

			
			
			$options = get_option( self::OPTION_NAME, array() );
			$majesty_options = wp_parse_args( $options, $args );
			
			return $majesty_options;
		}
		
		function create_scroll_menu() {
			$error 		= array();
			$success 	= 'true';
			
			$menu_items = $this->menu_scroll_options();
			
			$menu_name 		= 'scroll menu';
			$menu_exists 	= wp_get_nav_menu_object( $menu_name );
			
			if( !$menu_exists ){
				$menu_id = wp_create_nav_menu($menu_name);
				
				foreach( $menu_items as $item ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title' 	=> $item['title'],
						'menu-item-url' 	=> $item['url'],
						'menu-item-type' 	=> 'custom',
						'menu-item-status' 	=> 'publish'
					));	
				}
				$this->old_new_id['scroll_menu_id'] = $menu_id;
				$this->processed['add_scroll_menu'] = true;
				wp_cache_delete( self::OPTION_NAME );
				wp_cache_delete( self::PROCESSED_NAME );
				update_option( self::OPTION_NAME, $this->old_new_id );
				update_option( self::PROCESSED_NAME, $this->processed );
				
				$locations = get_theme_mod('nav_menu_locations');
				$locations['top-menu-2'] = $menu_id;
				set_theme_mod('nav_menu_locations', $locations);
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Mega Menu'
			);
		}
		
		function menu_scroll_options() {

			$args = array(
					array(
						'title'			=> 'Home',
						'url'			=> '#wrapper',
					),
					array(
						'title'			=> 'About',
						'url'			=> '#about',
					),
					array(
						'title'			=> 'Team',
						'url'			=> '#team',
					),
					array(
						'title'			=> 'service',
						'url'			=> '#service',
					),
					array(
						'title'			=> 'Menu',
						'url'			=> '#ourmenu',
					),
					array(
						'title'			=> 'reservation',
						'url'			=> '#reservation',
					),
					array(
						'title'			=> 'News',
						'url'			=> '#blog',
					),
					array(
						'title'			=> 'Contact',
						'url'			=> '#contactus',
					),
			);
			
			return $args;
		}
		
		function create_mega_menu() {
			$error 		= array();
			$success 	= 'true';
			
			$menu_items = $this->mega_menu_options();
			
			$menu_name 		= 'theme mega menu';
			$menu_exists 	= wp_get_nav_menu_object( $menu_name );
			
			if( !$menu_exists ){
				$menu_id = wp_create_nav_menu($menu_name);
				$menu_old_new_id = array();
				
				foreach( $menu_items as $item ) {
					
					$menu_args = array();
					$menu_args['menu-item-title']  = $item['title'];
					$menu_args['menu-item-status'] = 'publish';
					if( $item['type'] === 'link' ) {
						$menu_args['menu-item-type'] = 'custom';
						$menu_args['menu-item-url']  = $item['url'];
					} else {
						$menu_args['menu-item-object']  = 'page';
						$menu_args['menu-item-type']  = 'post_type';
						$menu_args['menu-item-object-id']  = $this->old_new_id['pages'][$item['page_id']];
					}
					if( ! empty(  $item['css_class'] ) ) {
						$menu_args['menu-item-classes']  = $item['css_class'];
					}
					if( ! empty(  $item['parent_id'] ) ) {
						$parent_id = $menu_old_new_id[$item['parent_id']];
						$menu_args['menu-item-parent-id']  = $parent_id;
					}
					
					$new_item_id = wp_update_nav_menu_item( $menu_id, 0, $menu_args );
					$menu_old_new_id[$item['old_id']] = $new_item_id;
					
					if( isset( $item['megamenu'] ) && $item['megamenu'] === '4col' ) {
						$args = array( 'panel_columns' => '4', 'type' => 'megamenu' );
						add_post_meta( $new_item_id, '_megamenu', $args, true );
					}
					if( isset( $item['megamenu'] ) && $item['megamenu'] === 'menu' ) {
						$this->old_new_id['mega_slider_id'] = $new_item_id;
						$args = array( 'type' => 'megamenu', 'panel_columns' => '1', 'submenu_ordering' => 'forced' ); 
						add_post_meta( $new_item_id, '_megamenu', $args, true );
					}
					
				}
				
				$locations = get_theme_mod('nav_menu_locations');
				$locations['top-menu'] = $menu_id;
				set_theme_mod('nav_menu_locations', $locations);
				
				
				$menu_op_args = array(
					0					=> false,
					'css'				=> 'disabled',
					'second_click'		=> 'close',
					'top-menu'			=> array(
						'enabled' 		=> 1,
						'event'			=> 'hover',
						'effect'		=> 'disabled',
						'theme'			=> 'default',
					)
				);
				update_option( 'megamenu_settings', $menu_op_args );
				
				
				$this->old_new_id['mega_menu_id'] = $menu_id;
				$this->processed['add_mega_menu'] = true;
				wp_cache_delete( self::OPTION_NAME );
				wp_cache_delete( self::PROCESSED_NAME );
				update_option( self::OPTION_NAME, $this->old_new_id );
				update_option( self::PROCESSED_NAME, $this->processed );
				
				
			}
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add The Mega Menu'
			);
		}
		
		function mega_menu_options() {
			
			$args = array(
					array(
						'old_id'		=> 1,
						'parent_id'		=> '',
						'type'			=> 'link',
						'title'			=> 'Home',
						'css_class'		=> '',
						'url'			=> get_home_url(),
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 2,
						'parent_id'		=> '',
						'type'			=> 'link',
						'title'			=> 'Menu',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> '',
						'megamenu'		=> 'menu'
					),
					array(
						'old_id'		=> 3,
						'parent_id'		=> '',
						'type'			=> 'link',
						'title'			=> 'Reservations',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 4,
						'parent_id'		=> 3,
						'type'			=> 'page',
						'title'			=> 'By Contact Form 7',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 2759,
					),
					array(
						'old_id'		=> 5,
						'parent_id'		=> 3,
						'type'			=> 'page',
						'title'			=> 'By Reservation Plugin',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 2954,
					),
					array(
						'old_id'		=> 6,
						'parent_id'		=> 3,
						'type'			=> 'page',
						'title'			=> 'By Open Table',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3526,
					),
					array(
						'old_id'		=> 7,
						'parent_id'		=> '',
						'type'			=> 'page',
						'title'			=> 'Blog',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 1066,
					),
					array(
						'old_id'		=> 8,
						'parent_id'		=> '',
						'type'			=> 'page',
						'title'			=> 'Events',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 2974,
					),
					array(
						'old_id'		=> 9,
						'parent_id'		=> '',
						'type'			=> 'link',
						'title'			=> 'Pages',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> '',
						'megamenu'		=> '4col'
					),
					array(
						'old_id'		=> 10,
						'parent_id'		=> 9,
						'type'			=> 'link',
						'title'			=> 'About Us',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 11,
						'parent_id'		=> 10,
						'type'			=> 'page',
						'title'			=> 'About',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> 1086,
					),
					array(
						'old_id'		=> 12,
						'parent_id'		=> 10,
						'type'			=> 'page',
						'title'			=> 'CONTACT',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> 2747,
					),
					array(
						'old_id'		=> 13,
						'parent_id'		=> 10,
						'type'			=> 'page',
						'title'			=> 'SERVICES',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> 2764,
					),
					array(
						'old_id'		=> 14,
						'parent_id'		=> 10,
						'type'			=> 'link',
						'title'			=> 'Team Memeber',
						'css_class'		=> '',
						'url'			=> $this->site_url . 'team-members/',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 15,
						'parent_id'		=> 9,
						'type'			=> 'link',
						'title'			=> 'Menu without Commerce',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 16,
						'parent_id'		=> 15,
						'type'			=> 'page',
						'title'			=> 'Menu List 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5410,
					),
					array(
						'old_id'		=> 17,
						'parent_id'		=> 15,
						'type'			=> 'page',
						'title'			=> 'Menu List 4',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5420,
					),
					array(
						'old_id'		=> 18,
						'parent_id'		=> 15,
						'type'			=> 'page',
						'title'			=> 'Filter 1',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 1086,
					),
					array(
						'old_id'		=> 19,
						'parent_id'		=> 15,
						'type'			=> 'page',
						'title'			=> 'Filter 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5398,
					),
					array(
						'old_id'		=> 20,
						'parent_id'		=> 9,
						'type'			=> 'link',
						'title'			=> 'WooCommerce Menu',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 21,
						'parent_id'		=> 20,
						'type'			=> 'page',
						'title'			=> 'Menu List',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3380,
					),
					array(
						'old_id'		=> 22,
						'parent_id'		=> 20,
						'type'			=> 'page',
						'title'			=> 'Menu list 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5442,
					),
					array(
						'old_id'		=> 23,
						'parent_id'		=> 20,
						'type'			=> 'page',
						'title'			=> 'Menu Filter',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3364,
					),
					array(
						'old_id'		=> 24,
						'parent_id'		=> 20,
						'type'			=> 'page',
						'title'			=> 'Menu Masonry',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3355,
					),
					array(
						'old_id'		=> 25,
						'parent_id'		=> 9,
						'type'			=> 'link',
						'title'			=> 'WooCommerce Menu',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 26,
						'parent_id'		=> 25,
						'type'			=> 'page',
						'title'			=> 'Masonry Full Width',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3362,
					),
					array(
						'old_id'		=> 27,
						'parent_id'		=> 25,
						'type'			=> 'page',
						'title'			=> 'Menu List 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5442,
					),
					array(
						'old_id'		=> 28,
						'parent_id'		=> 25,
						'type'			=> 'page',
						'title'			=> 'Menu List 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5442,
					),
					array(
						'old_id'		=> 29,
						'parent_id'		=> 25,
						'type'			=> 'page',
						'title'			=> 'Menu List 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5442,
					),
					array(
						'old_id'		=> 30,
						'parent_id'		=> '',
						'type'			=> 'link',
						'title'			=> 'Features',
						'css_class'		=> '',
						'url'			=> '#',
						'page_id'		=> '',
						'megamenu'		=> '4col'
					),
					array(
						'old_id'		=> 31,
						'parent_id'		=> 30,
						'type'			=> 'link',
						'title'			=> 'Header & menu',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 32,
						'parent_id'		=> 31,
						'type'			=> 'page',
						'title'			=> 'Dark Center',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5214,
					),
					array(
						'old_id'		=> 33,
						'parent_id'		=> 31,
						'type'			=> 'page',
						'title'			=> 'Dark Center',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5214,
					),
					array(
						'old_id'		=> 34,
						'parent_id'		=> 31,
						'type'			=> 'page',
						'title'			=> 'Dark Center',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 5214,
					),
					array(
						'old_id'		=> 35,
						'parent_id'		=> 30,
						'type'			=> 'link',
						'title'			=> 'Visual Composer',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 36,
						'parent_id'		=> 35,
						'type'			=> 'page',
						'title'			=> 'Sections',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3135,
					),
					array(
						'old_id'		=> 37,
						'parent_id'		=> 35,
						'type'			=> 'page',
						'title'			=> 'Sections 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3606,
					),
					array(
						'old_id'		=> 38,
						'parent_id'		=> 35,
						'type'			=> 'page',
						'title'			=> 'Woocommerce',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3559,
					),
					array(
						'old_id'		=> 39,
						'parent_id'		=> 30,
						'type'			=> 'link',
						'title'			=> 'Utlities',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 40,
						'parent_id'		=> 39,
						'type'			=> 'page',
						'title'			=> 'Coming Soon Default',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3089,
					),
					array(
						'old_id'		=> 41,
						'parent_id'		=> 39,
						'type'			=> 'page',
						'title'			=> 'Coming Soon Slider',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3095,
					),
					array(
						'old_id'		=> 42,
						'parent_id'		=> 39,
						'type'			=> 'page',
						'title'			=> 'Coming Soon Video',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3093,
					),
					array(
						'old_id'		=> 43,
						'parent_id'		=> 30,
						'type'			=> 'link',
						'title'			=> 'Others',
						'css_class'		=> 'menu-title',
						'url'			=> '#',
						'page_id'		=> '',
					),
					array(
						'old_id'		=> 44,
						'parent_id'		=> 43,
						'type'			=> 'page',
						'title'			=> 'Home Layout 2',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 1566,
					),
					array(
						'old_id'		=> 45,
						'parent_id'		=> 43,
						'type'			=> 'page',
						'title'			=> 'Home Layout 3',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 1595,
					),
					array(
						'old_id'		=> 46,
						'parent_id'		=> 43,
						'type'			=> 'page',
						'title'			=> 'Home Layout 4',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 3536,
					),
					array(
						'old_id'		=> 47,
						'parent_id'		=> '',
						'type'			=> 'page',
						'title'			=> 'Shop',
						'css_class'		=> '',
						'url'			=> '',
						'page_id'		=> 1304,
					),
			
			);
			
			return $args;
		}
		
		function add_footer_widget() {
			$error 		= array();
			$success 	= 'true';
			// Footer 1
			$target_sidebar = 'footer';
			$widget_data = array(
				'title' => 'OUR LOCATIONS',
				'content' => '<div class="our_location">
<p>Majesty  Head Office:</p>
<span>1422 1st St. Santa Rosa,t CA 94559. USA</span>
<p class="mt30">Call for Reservations:<span>(001) 123-4567</span></p>
<p>E-mail: <span>admin@e-mail.com</span> </p>
<ul class="social mt30">
	<li><a href="#" data-toggle="tooltip" title="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a></li>
	<li><a href="#" data-toggle="tooltip" title="" data-original-title="Twitter"><i class="fa fa-twitter"></i></a></li>
	<li><a href="#" data-toggle="tooltip" title="" data-original-title="Instagram"><i class="fa fa-instagram"></i></a></li>
	<li><a href="#" data-toggle="tooltip" title="" data-original-title="Behance"><i class="fa fa-behance"></i></a></li>
</ul>
</div>',
			);
			$this->insert_widget_in_sidebar('custom_html', $widget_data, $target_sidebar);
			
			// Footer 2
			$target_sidebar = 'footer-2';
			$widget_data = array(
				'title' 	=> 'LATEST POST',
				'num' 			=> 3,
				'excerptnum' 	=> 0,
				'catid'			=> -1
			);
			$this->insert_widget_in_sidebar('widget-recent-posts', $widget_data, $target_sidebar);
			// Footer 3
			$target_sidebar = 'footer-3';
			$widget_data = array(
				'title' => 'OPENING TIMES',
				'day-1'			=> 'Monday',
				'day-2'			=> 'Tuesday',
				'day-3'			=> 'Wednesday',
				'day-4'			=> 'Thursday',
				'day-5'			=> 'Friday',
				'day-6'			=> 'Saturday',
				'day-7'			=> 'Sunday',
				'day-1-t'		=> '1 pm - 10 pm',
				'day-2-t'		=> '1 pm - 10 pm',
				'day-3-t'		=> '1 pm - Midnight',
				'day-4-t'		=> '1 pm - 10 pm',
				'day-5-t'		=> '1 pm - Midnight',
				'day-6-t'		=> 'Closed',
				'day-7-t'		=> '1 pm - 10 pm',
				'day-1-x'		=> 0,
				'day-2-x'		=> 0,
				'day-3-x'		=> 0,
				'day-4-x'		=> 0,
				'day-5-x'		=> 0,
				'day-6-x'		=> 1,
				'day-7-x'		=> 0
			);
			$this->insert_widget_in_sidebar('widget_opening_times', $widget_data, $target_sidebar);
			// Footer 4
			$target_sidebar = 'footer-4';
			$widget_data = array(
				'title' 	=> 'Tags',
				'count' 	=> 0,
				'taxonomy' 	=> 'post_tag',
			);
			$this->insert_widget_in_sidebar('tag_cloud', $widget_data, $target_sidebar);
			
			return array(
				'error'				=> $error,
				'success'			=> $success,
				'message' 			=> 'Add Home Magazine Footer Widget'
			);
		}
		
		function get_content_page_template( $template_name ) {
		
			$template = 'pages/'. $template_name . '.php';
			
			ob_start();
			require( $template );
			$content = ob_get_clean();
			return $content;
		}
		function insert_widget_in_sidebar( $widget_id, $widget_data, $sidebar ) {
			// Retrieve sidebars, widgets and their instances
			$sidebars_widgets = get_option( 'sidebars_widgets', array() );
			$widget_instances = get_option( 'widget_' . $widget_id, array() );
			// Retrieve the key of the next widget instance
			$numeric_keys = array_filter( array_keys( $widget_instances ), 'is_int' );
			$next_key = $numeric_keys ? max( $numeric_keys ) + 1 : 2;
			// Add this widget to the sidebar
			if ( ! isset( $sidebars_widgets[ $sidebar ] ) ) {
				$sidebars_widgets[ $sidebar ] = array();
			}
			$sidebars_widgets[ $sidebar ][] = $widget_id . '-' . $next_key;
			// Add the new widget instance
			$widget_instances[ $next_key ] = $widget_data;
			// Store updated sidebars, widgets and their instances
			update_option( 'sidebars_widgets', $sidebars_widgets );
			update_option( 'widget_' . $widget_id, $widget_instances );
		}
		function add_theme_options() {
			$template = 'pages/theme-options.php';
			ob_start();
			require( $template );
			$content = ob_get_clean();
			$error 		= array();
			$success 	= 'true';
			return array(
					'error'				=> $error,
					'success'			=> $success,
					'message' 			=> 'Add Theme options'
				);
		}
	}

	$majestimport = new Majesty_Import_Demo_Content();
}
?>