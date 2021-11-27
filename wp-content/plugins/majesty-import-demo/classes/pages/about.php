<?php
// '. $this->site_url .'
$art_1_url = esc_url(wp_get_attachment_url($this->old_new_id['media'][1]));

$bg_wood_2 = $this->old_new_id['media'][67];

$chef_png  = esc_url(wp_get_attachment_url($this->old_new_id['media'][79]));

// Slider_image
$slide_1 = $this->old_new_id['media'][182];
$slide_2 = $this->old_new_id['media'][183];
$slide_3 = $this->old_new_id['media'][184];


$content = '[vc_row box_padding="padding-100" extra_css="welcome-block"][vc_column offset="vc_col-md-6"][vc_column_text]<img class="img-responsive" src="'. $art_1_url .'" alt="art" />[/vc_column_text][/vc_column][vc_column text_align="text-center" offset="vc_col-md-5"][vc_empty_space height="40px"][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="ART OF FOOD" subtitle="Welcome to Majesty"][vc_column_text]Majesty is a team work of Majesty Restaurant Cafe, we aim at promoting the foodstuff industry through the branches , we establish and through the new dishes. and integrate between different cultures, by merging different Oriental and its western recipes[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" overlay="transparent-bg-3" extra_css="chef-message" parallax="image" parallax_image="'. $bg_wood_2  .'" el_id="chiefmessages"][vc_column text_align="text-center" offset="vc_col-md-7"][vc_column_text]
<h2 class="heading">CHEF MESSAGE</h2>
We aim to home-produce as much as possiblefor the best quality, and to reduce food miles. Our delicious cakes, traditional Devon scones, breads, soups, sauces and accompaniments are produced in our own kitchens – and we use herbs from our gardens when in season. Even our bottled water is produced in-house, using a sophisticated seven-stage filtration.
<h2 class="signature">Micheal</h2>
[/vc_column_text][/vc_column][vc_column width="5/12" extra_css="col-absolute"][vc_column_text]<img class="img-responsive" src="'. $chef_png .'" alt="chef" />[/vc_column_text][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="OUR CHEFS" subtitle="The Friendlist Professional Chef"][vc_team_members_carousel num="6"][/vc_column][/vc_row][vc_row box_padding="padding-100" theme_color="dark" extra_css="black-bg"][vc_column text_align="text-center"][vc_raw_html]JTNDaDIlMjBjbGFzcyUzRCUyMnRleHQtdXBwZXJjYXNlJTIyJTNFQ29tZSUyMCUyNmFtcCUzQiUyMCUzQ3NwYW4lM0VFeHBlcmllbmNlcyUzQyUyRnNwYW4lM0UlMjBvdXIlMjBiZXN0JTIwb2YlMjB3b3JsZCUyMGNsYXNzJTIwY3Vpc2luZSUzQyUyRmgyJTNFJTNDYSUyMGhyZWYlM0QlMjJodHRwJTNBJTJGJTJGZGVtby5zYW1hdGhlbWVzLmNvbSUyRm1hamVzdHklMkZjb250YWN0JTJGJTIyJTIwY2xhc3MlM0QlMjJidG4lMjBibGFjayUyMGJ0bi1nb2xkJTIwbXQyMCUyMiUzRUdFVCUyMElOJTIwVE9VQ0glM0MlMkZhJTNF[/vc_raw_html][/vc_column][/vc_row][vc_row box_padding="padding-100"][vc_column][vc_add_heading icon_type="iconmoon" icon="icon-intro" title="OUR MISSION" subtitle="Goals &amp; Vision"][/vc_column][vc_column offset="vc_col-md-5"][vc_images_owl_carousel type="slider" images="'. $slide_1.','. $slide_2.','. $slide_3.'" onclick="link_no" pagination="false"][/vc_column][vc_column text_align="text-left" offset="vc_col-md-7"][vc_column_text]Cras porta dolor ut velit imperdiet, quis sodales tellus facilisis. Vestibulum magna turpis, tincidun blandit semper lorem. Mauris nodesterin feugiat neque, a aliquet ligula. Sed non felis tincidunt, facilisis felis vitae, mollis est. Nulla sapien dui, feugiat sed velit a, egestas porta magna. Maecenas aliquet laoreet risus at vehicula lorem ipsum dolor.

Phasellus dignissim metus lorem, eu hendrerit lectus ultricies ullamcorper. Interdum etros malesuda fames ac ante ipsum primis in faucibus. quis sodales tellus facilisis. Vestibulum magna turpis, tincidun blandit semper lorem Donec sit amet iaculis elit, sed gravida justo. Nulla eget viverra massa, at ornare ellentesque mi sed est accumsan maximus. Cras porta dolor ut velit imperdiet, quis sodales tellus facilisis Cras porta dolor ut velit imperdiet, quis sodales tellus facilisis.[/vc_column_text][/vc_column][/vc_row]';

echo $content;