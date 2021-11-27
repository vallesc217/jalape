<?php
$client_1 = esc_url(wp_get_attachment_url($this->old_new_id['media'][190]));
$client_2 = esc_url(wp_get_attachment_url($this->old_new_id['media'][197]));
$client_3 = esc_url(wp_get_attachment_url($this->old_new_id['media'][198]));
$client_4 = esc_url(wp_get_attachment_url($this->old_new_id['media'][199]));

$client_12 = esc_url(wp_get_attachment_url($this->old_new_id['media'][193]));
$client_14 = esc_url(wp_get_attachment_url($this->old_new_id['media'][195]));
$client_15 = esc_url(wp_get_attachment_url($this->old_new_id['media'][196]));

$parallax_1 = $this->old_new_id['media'][67];
// Slider_image
$slide_1 = $this->old_new_id['media'][200];
$slide_2 = $this->old_new_id['media'][201];
$slide_3 = $this->old_new_id['media'][202];
$slide_4 = $this->old_new_id['media'][191];
$slide_5 = $this->old_new_id['media'][192];

$content = '[vc_row box_padding="padding-100" extra_css="clients-container"][vc_column][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="OUR HAPPY CLIENTS" subtitle="Light Background"][/vc_column][vc_column width="1/2" offset="vc_col-md-3 vc_col-xs-6"][vc_column_text]<img class="img-responsive" src="'. $client_1 .'" alt="client_1" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][vc_empty_space height="30px"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3 vc_col-xs-6"][vc_column_text]<img class="img-responsive" src="'. $client_2 .'" alt="client_2" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][vc_empty_space height="30px"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3 vc_col-xs-6"][vc_column_text]<img class="img-responsive" src="'. $client_3 .'" alt="client_3" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3 vc_col-xs-6"][vc_column_text]<img class="img-responsive" src="'. $client_4 .'" alt="client_4" />[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" parallax="image" parallax_image="'.$parallax_1.'"][vc_column][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="CAROUSAL CLIENTS" subtitle="The Friendlist Professional Partners"][/vc_column][vc_column][vc_images_owl_carousel images="'. $slide_1 .','. $slide_2 .','. $slide_3 .','. $slide_4 .','. $slide_5 .'" onclick="link_no" navigation="false" pagination_marg_top="50px" items="4" itemsdesktop="4" itemsdesktopsmall="3" itemstablet="3" itemstabletsmall="1" itemsmobile="1"][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" extra_css="black-bg" el_class="clients-container"][vc_column text_align="text-center"][vc_column_text]
<h1>DARK CLIENTS</h1>
[/vc_column_text][vc_empty_space height="60px"][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_column_text]<img class="img-responsive" src="'. $client_12 .'" alt="client_12" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][vc_empty_space height="30px"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_column_text]<img class="img-responsive" src="'. $client_12 .'" alt="client_13" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][vc_empty_space height="30px"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_column_text]<img class="img-responsive" src="'. $client_14 .'" alt="client_14" />[/vc_column_text][vc_row_inner][vc_column_inner offset="vc_hidden-lg"][vc_empty_space height="30px"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_column_text]<img class="img-responsive" src="'. $client_15 .'" alt="client_15" />[/vc_column_text][/vc_column][/vc_row]';

echo $content;