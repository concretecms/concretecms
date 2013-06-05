<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	$text = Loader::helper('text');
	$name = $text->urlify($_REQUEST['name']);
	$ret = Events::fire('on_page_urlify', $_REQUEST['name']);
	if ($ret) {
  		$name = $ret;
	}

	echo $name;
}
