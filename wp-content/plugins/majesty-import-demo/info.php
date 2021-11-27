<?php

//$d = 'uploads/logo/';
$d = 'uploads/';
//$d = 'http://localhost/majestyxml/wp-content/plugins/majesty-import-demo/uploads';

if (is_dir($d)) {
    if ($dh = opendir($d)) {
		$i = 209;
        while (($file = readdir($dh)) !== false) {
			//echo "filename: $file " . "\n<br/>";
			//echo $file  . "\n<br/>";
            //echo "filename: $file : filetype: " . filetype($d . $file) . "\n<br/>";
			echo 'array("title" =>"'. $file . '", "old_id" =>'. $i . ', "new_id" => \'\' ),<br/>';
			$i++;
        }
        closedir($dh);
    }
} else {
	echo '5555';
}