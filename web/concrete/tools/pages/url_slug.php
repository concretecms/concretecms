<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	Loader::library('3rdparty/urlify');
	print URLify::filter($_REQUEST['name']);
}