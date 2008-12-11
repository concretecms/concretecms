<?
header("Content-Type: text/css");
$co = Request::get();
$v = View::getInstance();
$au = $co->getAuxiliaryData();
if (isset($au->theme) && isset($au->file)) {
	$pt = PageTheme::getByHandle($au->theme);
	if ($_REQUEST['mode'] == 'preview') {
	 	$val = Cache::get('preview_theme', $pt->getThemeID());
		if (is_array($val)) {
			$values = $pt->mergeStylesFromPost($val);
			$pt->outputStyleSheet($au->file, $values);
			exit;
		}
	}
	
	$pt->outputStyleSheet($au->file);
}