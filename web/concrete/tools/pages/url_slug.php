<?php
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	$lang = Localization::activeLanguage();
	if (isset($_REQUEST['parentID']) && $multilingual = Package::getByHandle('multilingual') ) {
		$ms = MultilingualSection::getBySectionOfSite(Page::getByID($_REQUEST['parentID']));
		if (is_object($ms)) {
			$lang = $ms->getLanguage();
		}
	}
	$text = Loader::helper('text');
	$name = $text->urlify($_REQUEST['name'], Config::get('concrete.seo.segment_max_length'), $lang);

	echo $name;
}
