<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('section', 'multilingual');
// first we get the selected language

if (isset($_POST['ccmMultilingualSiteDefaultLanguage'])) {
	// this is coming from "set default" custom template
	
	if (isset($_POST['ccmMultilingualSiteRememberDefault']) && $_POST['ccmMultilingualSiteRememberDefault']) {
		 setcookie('DEFAULT_LOCALE', $_POST['ccmMultilingualSiteDefaultLanguage'], time()+60*60*24*365, DIR_REL . '/');
	}
	if (empty($_POST['ccmMultilingualSiteRememberDefault'])) {
		 setcookie('DEFAULT_LOCALE', '', time() - 3600, DIR_REL . '/');
	}
	$lang = MultilingualSection::getByLocale($_REQUEST['ccmMultilingualSiteDefaultLanguage']);
	
} else {
	$lang = MultilingualSection::getByID($_REQUEST['ccmMultilingualChooseLanguage']);
}

if (is_object($lang)) {
	if (isset($_REQUEST['ccmMultilingualCurrentPageID'])) {
		$page = Page::getByID($_REQUEST['ccmMultilingualCurrentPageID']);
		if (!$page->isError()) {
			$relatedID = $lang->getTranslatedPageID($page);
			if ($relatedID) {
				$pc = Page::getByID($relatedID);
				header('Location: ' . Loader::helper('navigation')->getLinkToCollection($pc, true));
				exit;
			} elseif($page->isGeneratedCollection()) {
				$_SESSION['DEFAULT_LOCALE'] = (string) $lang->getLocale();
				header('Location: ' . Loader::helper('navigation')->getLinkToCollection($page, true));
				exit;
			}
		}
	}
	header('Location: ' . Loader::helper('navigation')->getLinkToCollection($lang, true));
	exit;
}


header('Location: ' . BASE_URL . DIR_REL . '/');
exit;
