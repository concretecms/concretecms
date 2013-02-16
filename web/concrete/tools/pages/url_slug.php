<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	Loader::library('3rdparty/urlify');
	$name = URLify::filter($_REQUEST['name']);

	$ret = Events::fire('on_page_urlify', $_REQUEST['name']);
	if ($ret) {
  		$name = $ret;
	}

	echo $name;
}
