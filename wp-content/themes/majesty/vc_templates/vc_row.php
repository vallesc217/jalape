<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $full_width
 * @var $full_height
 * @var $equal_height
 * @var $columns_placement		- not used in theme
 * @var $content_placement		- not used in theme
 * @var $parallax
 * @var $parallax_image
 * @var $css
 * @var $el_id
 * @var $video_bg				- not used in theme
 * @var $video_bg_url			- not used in theme
 * @var $video_bg_parallax		- not used in theme
 * @var $parallax_speed_bg		- not used in theme
 * @var $parallax_speed_video	- not used in theme
 * @var $content - shortcode content
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row
 */
$output = $after_output = '';
$el_class = $full_height = $parallax_speed_bg = $parallax_speed_video = $full_width = $equal_height = $flex_row = $columns_placement = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax = $css_animation = $disable_element = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'wpb_composer_front_js' );

if( $parallax == 'image' ) {
	//wp_enqueue_script('skrollr');
}

$css_container = 'container';
$css_row	   = 'row';
if ( ! empty( $full_width ) ) {
	$css_row = 'row no-gutter';
}

$el_class = $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );

$css_classes = array(
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);

if ( 'yes' === $disable_element ) {
	if ( vc_is_page_editable() ) {
		$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( ! empty( $atts['rtl_reverse'] ) ) {
	$css_classes[] = 'vc_rtl-columns-reverse';
}

if ( ! empty( $atts['gap'] ) ) {
	$css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

$css_classes[] = 'section';
if( ! empty($theme_color) ) {
	$css_classes[] = $theme_color;
	if( $theme_color == 'theme-color' ) {
		$css_classes[] = 'dark';
	}
}
if( ! empty( $extra_css ) ) {
	$css_classes[] = $extra_css;
}
$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
if ( ! empty( $full_width ) ) {
	$css_container = 'fluid-container';
}
if( ! empty($box_padding) && $box_padding != 'no-padding' ) {
	$css_container .= ' '. $box_padding;
}
if ( ! empty( $full_height ) ) {
	$css_classes[] = 'fullheight vc-row-full-height';
}

if( $parallax == 'html5video' ) {
	$css_classes[] = 'bg_video';
}

$has_parallax_img    = false;
$has_parallax_video  = false;
$parallax_image_src  = '';
$html_image_parallax = '';
$has_transparent 	 = false;
$parallax_keys = 'data-center="background-position: 50% 0px;" data-bottom-top="background-position: 50% 100px;" data-top-bottom="background-position: 50% -100px;"';
if( ! empty( $parallax_image ) ) {
	$parallax_image_id = preg_replace( '/[^\d]/', '', $parallax_image );
	$image_src 		   = wp_get_attachment_image_src( $parallax_image_id, 'full' );
	if ( ! empty( $image_src[0] ) ) {
		$parallax_image_src = $image_src[0];
	}
}
if ( ! empty( $parallax ) && $parallax == 'image' && ! empty( $parallax_image_src ) ) {
	$has_parallax_img = true;
	if ( empty( $el_id ) ) {
		$el_id = 'parallax-bg-'. rand(1,999);
		$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
	}
} 
if ( ! empty( $parallax ) && $parallax == 'youtube' && ! empty( $youtube ) ) {
	wp_enqueue_script('YTPlayer');
	$css_classes[] = 'has-yt-bg-player';
	if ( empty( $el_id ) ) {
		$el_id = 'parallax-bg-'. rand(1,999);
		$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
	}
}
$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';


$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
if ( ! empty( $parallax ) && $parallax == 'youtube' && ! empty( $youtube ) ) {
	$output .= '<div class="youtube-video-wrap yt-bg-player" data-video="'. esc_url( $youtube ) . '" data-container="#'. esc_attr( $el_id ) . '" data-poster="'. esc_url( $parallax_image_src ) . '"></div>';
	/*$wrapper_attributes[] = ';
	$wrapper_attributes[] = ';
	$wrapper_attributes[] = ';*/
}

if( $has_parallax_img ) {
	$output .= '<div class="bcg" data-parallax-image="'. esc_url($parallax_image_src) .'">';	
} elseif ( ! empty( $parallax ) && $parallax == 'html5video' && ! empty( $mp4 ) ) {
	$has_parallax_video = true;
	$output .= '<div class="video-wrap">
        <video poster="'. esc_url( $parallax_image_src ) .'" preload="auto" loop autoplay>
          <source src="'. esc_url( $mp4 ) .'" type="video/mp4">
          <source src="'. esc_url( $webm ) .'" type="video/webm">
        </video>
      </div>';
}
if( ! empty($theme_color) && $theme_color == 'dark' ) {
	if( ! empty( $overlay ) ) {
		$has_transparent = true;
		if( $overlay == '' ) {
			$output .= '<div class="'. esc_attr( $overlay ) .'">';
		} else {
			$output .= '<div class="bg-transparent '. esc_attr( $overlay ) .'">';
		}
	}
}

$output .= '<div class="'. $css_container .'">';
$output .= '<div class="' . esc_attr( trim( $css_row ) ) . '">';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';
$output .= '</div>';
if( $has_parallax_img ) {
	$output .= '</div>';
}
if( $has_transparent ) {
	$output .= '</div>';
}
$output .= '</div>';// End Of section
$output .= $after_output;
$output .= $this->endBlockComment( $this->getShortcode() );
echo $output;