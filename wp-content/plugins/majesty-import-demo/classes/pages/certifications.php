<?php
$certi  = esc_url(wp_get_attachment_url($this->old_new_id['media'][77]));

$content = '[vc_row box_padding="padding-100"][vc_column width="1/2" offset="vc_col-md-6"][vc_column_text]<img class="img-responsive" src="'. $certi .'" alt="certificate" />[/vc_column_text][/vc_column][vc_column width="1/2" offset="vc_col-md-6"][vc_empty_space height="60px"][vc_column_text]In a professional context it often happens that private or corporate clients corder a publication to be made and presented with the actual content still not being ready. Think of a news blog that\'s filled with content hourly on the day of going live. However, reviewers tend to be distracted by comprehensible content, say, a random text copied from a newspaper or the internet.

The are likely to focus on the text, disregarding the layout and its elements. Besides, random text risks to be unintendedly humorous or offensive, an unacceptable risk in corporate environments. Lorem ipsum and its many variants have been employed.[/vc_column_text][/vc_column][/vc_row]';

echo $content;