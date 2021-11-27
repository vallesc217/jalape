<?php
$rest_4 = $this->old_new_id['media'][108];
$rest_2 = $this->old_new_id['media'][106];
$rest_5 = $this->old_new_id['media'][109];

$break_fast_png = $this->old_new_id['media'][93];
$lunch_png = $this->old_new_id['media'][100];
$dinner_png = $this->old_new_id['media'][97];
$dessert_png = $this->old_new_id['media'][96];

$parallax_image_1 = $this->old_new_id['media'][40];

$video_mp4_url  = esc_url(wp_get_attachment_url($this->old_new_id['media'][80]));
$video_webm_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][81]));
$poster_url  	= $this->old_new_id['media'][87];


$content = '[vc_row box_padding="padding-100"][vc_column width="1/3"][vc_overlay_box title="RELAXING AMBIENCE" image="'.$rest_4.'"]

Creating good mood

[/vc_overlay_box][/vc_column][vc_column width="1/3"][vc_overlay_box title="SUNDAY" image="'.$rest_2.'"]

We Open

[/vc_overlay_box][/vc_column][vc_column width="1/3"][vc_overlay_box title="DIET FOODS" image="'.$rest_5.'"]

We Offer Diet Food

[/vc_overlay_box][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" parallax="image" parallax_image="'. $parallax_image_1 .'" el_id="service" el_class="vc-bg-parallax"][vc_column width="1/2" offset="vc_col-md-3"][vc_feature_box_image title="BREAKFAST" image="'. $break_fast_png .'"]

6.00 am 10.00 am

[/vc_feature_box_image][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_feature_box_image title="LUNCH" image="'. $lunch_png .'"]

10.00 am 2.00 pm

[/vc_feature_box_image][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_feature_box_image title="DINNER" image="'. $dinner_png .'"]

4.00 pm 12.00 pm

[/vc_feature_box_image][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][vc_feature_box_image title="DESSERT" image="'. $dessert_png .'"]

All Day

[/vc_feature_box_image][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column text_align="text-center"][vc_raw_html]

JTNDaDIlMjBjbGFzcyUzRCUyMnRleHQtdXBwZXJjYXNlJTIyJTNFQ29tZSUyMCUyNmFtcCUzQiUyMCUzQ3NwYW4lM0VFeHBlcmllbmNlcyUzQyUyRnNwYW4lM0UlMjBvdXIlMjBiZXN0JTIwb2YlMjB3b3JsZCUyMGNsYXNzJTIwY3Vpc2luZSUzQyUyRmgyJTNF

[/vc_raw_html][vc_empty_space height="20px"][vc_feature_button title="GET IN TOUCH" bootstrap_size="" href="'. $this->site_url .'contact/"][/vc_column][/vc_row][vc_row box_padding="padding-150" theme_color="dark" parallax="html5video" parallax_image="'. $poster_url .'" mp4="'. $video_mp4_url .'" webm="'. $video_webm_url .'"][vc_column text_align="text-center" offset="vc_col-md-offset-2 vc_col-md-8"][vc_column_text]
<h2 class="heading text-uppercase">Video Background</h2>
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.[/vc_column_text][/vc_column][/vc_row]
';
echo $content;