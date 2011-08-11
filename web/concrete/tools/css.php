<?
defined('C5_EXECUTE') or die("Access Denied.");
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
					header("Content-Type: text/css");
					$values = $pt->mergeStylesFromPost($val);
					$pt->outputStyleSheet($au->file, $values);
					exit;
				}
			}
			$stat = filemtime($pt->getThemeDirectory() . '/' . $au->file);

			$style = Cache::get(str_replace('-','_', $au->theme), $au->file, $stat);
			
			if ($style == '') {
				$style = $pt->parseStyleSheet($au->file);
				Cache::set(str_replace('-','_', $au->theme), $au->file, $style, CACHE_LIFETIME);
			}
			header("Content-Type: text/css");
			header("Date: ". date("D, j M Y G:i:s", $stat) ." GMT");
			header("Expires: ". gmdate("D, j M Y H:i:s", time() + DAY) ." GMT");
			header("Cache-Control: max-age=86400, must-revalidate"); // HTTP/1.1
			header("Pragma: cache_asset");        // HTTP/1.0	

			echo $style; 
		}
	}
}