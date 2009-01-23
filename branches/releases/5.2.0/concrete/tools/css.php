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
				$val = Cache::get('preview_theme_style', $pt->getThemeID());
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
				Cache::set($au->theme, $au->file, $style, 10800);
			}
		
			print $style;
		}
	}
}