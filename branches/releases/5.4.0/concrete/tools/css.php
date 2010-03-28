<?php 
header("Content-Type: text/css");
$co = Request::get();
$v = View::getInstance();
$au = $co->getAuxiliaryData();
if (isset($au->theme) && isset($au->file)) {
	$pt = PageTheme::getByHandle($au->theme);
	if (is_object($pt)) {
		if (file_exists($pt->getThemeDirectory() . '/' . $au->file)) {
			if ($_REQUEST['mode'] == 'preview') {
				$val = Cache::get('preview_theme_style', $pt->getThemeID(), false, true);
				if (is_array($val)) {
					$values = $pt->mergeStylesFromPost($val);
					$pt->outputStyleSheet($au->file, $values);
					exit;
				}
			}
			$stat = filemtime($pt->getThemeDirectory() . '/' . $au->file);
			$style = Cache::get($au->theme, $au->file, $stat);
			if ($style == '') {
				$style = $pt->parseStyleSheet($au->file);
				Cache::set($au->theme, $au->file, $style, 10800, true);
			}
		
			//if we're doing a cache refresh, we need to print the css still 
			$noCache = ($_SERVER['HTTP_PRAGMA'] == 'no-cache'); 
			
			//see if we have a cache time to check 
			$moded = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false; 
			
			//if we have are cache refreshing || the browser cache has expired 
			if ($noCache || $moded || $stat > $moded) { 
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', $stat).' GMT'); 
				echo $style; 
			} else { 
				header('HTTP/1.1 304 Not Modified'); 
			} 

		}
	}
}