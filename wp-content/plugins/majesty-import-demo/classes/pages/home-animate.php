<?php
$left_image = esc_url(wp_get_attachment_url($this->old_new_id['media'][83]));
$right_image = esc_url(wp_get_attachment_url($this->old_new_id['media'][116]));
$parallax_image = $this->old_new_id['media'][23];
$rest_1 = $this->old_new_id['media'][105];
$rest_2 = $this->old_new_id['media'][106];
$rest_3 = $this->old_new_id['media'][107];
$bg_2slider_2 = $this->old_new_id['media'][34];
$bg_wood = $this->old_new_id['media'][67];

$left_bg_png = $this->old_new_id['media'][82]; // 1912
$right_bg_top_png = $this->old_new_id['media'][114]; // 1899
$right_bg_bo_png = $this->old_new_id['media'][115]; //1911

$contact_form = $this->old_new_id['contactforms'][1889] ;//1889

$content = '[vc_row box_padding="padding-100" extra_css="welcome-block"][vc_column css_animation_type="fadeInLeft" offset="vc_col-md-4"][vc_column_text]<img class="img-responsive" src="'. $left_image .'" alt="Left-Image" />[/vc_column_text][/vc_column][vc_column text_align="text-center" css_animation_type="fadeInUp" offset="vc_col-md-4"][vc_row_inner][vc_column_inner offset="vc_hidden-lg vc_hidden-md"][vc_empty_space height="40px"][/vc_column_inner][/vc_row_inner][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="ART OF FOOD" subtitle="Welcome to Majesty"][vc_column_text]Majesty is a team work of Majesty Restaurant Cafe, we aim at promoting the foodstuff industry through the branches , we establish and through the new dishes. and integrate between different cultures.</p>
<p><a class="btn btn-gold dark welcome-m-b" href="'. $this->site_url . 'about-us/">READ MORE</a>[/vc_column_text][/vc_column][vc_column css_animation_type="fadeInRight" offset="vc_col-md-4"][vc_column_text]<img class="img-responsive" src="'. $right_image .'" alt="right-image" />[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" extra_css="discover" parallax="image" parallax_image="'.$parallax_image.'"][vc_column text_align="text-center" css_animation_type="fadeInUp"][vc_column_text]
<h1>WE CREATE DELICOUS MEMORIES</h1>
<p class="text-uppercase">You can promote your creative parallax effects</p>
<p><a class="btn btn-gold white" href="'. $this->site_url . 'shop/">DISCOVER MORE</a></p>[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column width="1/3" css_animation_type="fadeInUp" css_animation_delay="200"][vc_overlay_box title="RELAXING AMBIENCE" image="'. $rest_1 .'"]Creating good mood[/vc_overlay_box][/vc_column][vc_column width="1/3" css_animation_type="fadeInDown" css_animation_delay="800"][vc_overlay_box title="SUNDAY" image="'. $rest_2 .'"]We Open[/vc_overlay_box][/vc_column][vc_column width="1/3" css_animation_type="fadeInUp" css_animation_delay="1200"][vc_overlay_box title="DIET FOODS" image="'. $rest_3 .'"]We Offer Diet Food[/vc_overlay_box][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" parallax="image" parallax_image="'.$bg_2slider_2.'"][vc_column css_animation_type="fadeIn"][vc_add_heading title="RESERVATION" subtitle="Book your table"][contact-form-7 id="'. $contact_form .'"][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column extra_css="nopadding"][vc_add_heading icon_type="iconmoon" icon="icon-intro" css_animation_type="fadeInDown" title="ART OF FOOD" subtitle="Welcome to Majesty"][/vc_column][vc_column css_animation_type="fadeInUp"][vc_woo_slider_by_category cats="breakfast,lunch,dinner,drinks" per_page="4" excerpt_length="13"][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" extra_css="video" parallax="image" parallax_image="'. $bg_wood .'"][vc_column extra_css="div-absolute"][vc_single_image extra_css="left_bg" image="'. $left_bg_png .'" img_size="full" alignment="center"][vc_single_image extra_css="right_bg" image="'. $right_bg_top_png .'" img_size="full" alignment="center"][vc_single_image extra_css="right_bg2" image="'. $right_bg_bo_png .'" img_size="full" alignment="center"][/vc_column][vc_column css_animation_type="fadeInLeft" offset="vc_col-md-5"][vc_column_text]
<div class="embed-responsive embed-responsive-16by9 embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://player.vimeo.com/video/23851992" width="500" height="281"></iframe></div>
[/vc_column_text][/vc_column][vc_column text_align="text-center" css_animation_type="fadeInRight" offset="vc_col-md-7"][vc_column_text]
<h1 class="">MAJESTY IN VIDEO</h1>
<p><b>You can add all videos format</b></p>
<p class="italic mt40">Nulla facilisi. Nunc id mi enim. Phasellus tortor nisl, auctor id elit congue, consectetur pharetra leo. Suspendisse eu libero nunc. Phasellus at justo ut lorem semper fringilla. Ut tincidunt, odio sit amet pellentesque venenatis, felis erat pulvinar enim, eu tristique mi lectus at purus. Curabitur iaculis congue laoreet. Vestibulum dignissim.</p>
[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column extra_css="nopadding" css_animation_type="fadeInDown"][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="LATEST NEWS" subtitle="Stay up to Date"][/vc_column][vc_column css_animation_type="fadeInUp"][vc_custom_blog_grid category="-1" num="3"][/vc_column][/vc_row]';

echo $content;