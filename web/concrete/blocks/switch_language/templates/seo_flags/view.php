<?php defined('C5_EXECUTE') or die('Access Denied.');
$ih = Loader::helper("interface/flag", 'multilingual');
$interfacePageHelper = Loader::helper('interface/page', 'multilingual');
$navigationHelper = Loader::helper('navigation');
$page = Page::getCurrentPage();
?><div class="ccm-multilingual-switch-language-flags"><?php
	if(strlen($label)) {
		?><div class="ccm-multilingual-switch-language-flags-label"><?php echo $label; ?></div><?php
	}
	foreach($languageSections as $ml) {
		?><a href="<?php echo $navigationHelper->getLinkToCollection($interfacePageHelper->getTranslatedPageWithAliasSupport($page, $ml, true)); ?>" class="<?php if($activeLanguage == $ml->getCollectionID()) { ?>ccm-multilingual-active-flag<?php } ?>"><?php
			echo $ih->getSectionFlagIcon($ml);
		?></a><?php
	}
?></div>