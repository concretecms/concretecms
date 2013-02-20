<?
defined('C5_EXECUTE') or die("Access Denied.");
$co = Request::get();
$v = View::getInstance();
$au = $co->getAuxiliaryData();
if (isset($au->theme) && isset($au->file)) {
	$pt = PageTheme::getByHandle($au->theme);
	$val = Cache::get('preview_theme_style', $pt->getThemeID(), false, true);
	header("Content-Type: text/css");
	if (is_array($val)) {
		$values = $pt->mergeStylesFromPost($val);
	}
	$pt->outputStyleSheet($au->file, $values);

}