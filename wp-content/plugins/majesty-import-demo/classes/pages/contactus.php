<?php
// '. $this->site_url .'
$art_1_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][1]));

$bg_wood_2 = $this->old_new_id['media'][67];

$chef_png  = esc_url(wp_get_attachment_url($this->old_new_id['media'][79]));

// Slider_image
$slide_1 = $this->old_new_id['media'][182];
$slide_2 = $this->old_new_id['media'][183];
$slide_3 = $this->old_new_id['media'][184];

$map = $this->old_new_id['media'][88];

$contact_form = $this->old_new_id['contactforms'][1814] ;//1889
$parallax_image = $this->old_new_id['media'][70];




$content = '[vc_row box_padding="padding-100"][vc_column text_align="text-center" extra_css="nopadding"][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="SEND MESSAGE" subtitle="Keep in Touch"][contact-form-7 id="'. $contact_form .'"][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-8" parallax="image" parallax_image="'. $parallax_image .'"][vc_column width="1/3"][vc_feature_box text_align="text-left" position="icon_left" icon="fa fa-road" title="ADRESS"]1422 1st St. Santa Rosa,t CA.
USA.[/vc_feature_box][/vc_column][vc_column width="1/3"][vc_feature_box text_align="text-left" position="icon_left" icon="fa fa-phone" title="PHONE"]Telephone:(001) 123-4567

Mobile:(001) 123-4567[/vc_feature_box][/vc_column][vc_column width="1/3"][vc_feature_box text_align="text-left" position="icon_left" icon="fa fa-envelope-o" title="E-MAIL"]Support:<a href="mailto:admin@e-mail.com">admin@e-mail.com</a>

Sales:<a href="mailto:admin@e-mail.com">admin@e-mail.com</a>[/vc_feature_box][/vc_column][/vc_row][vc_row full_width="stretch_row" box_padding="no-padding"][vc_column extra_css="nopadding"][vc_add_gmaps zoom="17" title="Frostbyte Interactive" latlang="45.088530, -64.367951" image="'.$map.'"]
<h4>Majest</h4>
1422 1st St. Santa Rosa,t CA 94559. USA

[/vc_add_gmaps][/vc_column][/vc_row]';

echo $content;