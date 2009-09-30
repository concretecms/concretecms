<?php  header('Content-type: text/javascript'); ?>

var ccmi18n = { 
	
	deleteBlock: '<?php echo t('Delete')?>',
	closeWindow: '<?php echo t('Close')?>',
	editBlock: '<?php echo t('Edit')?>',
	compareVersions: '<?php echo t('Compare Versions')?>',
	blockAreaMenu: '<?php echo t("Add Block")?>',
	arrangeBlock: '<?php echo t('Move')?>',
	copyBlockToScrapbook: '<?php echo t('Copy to Scrapbook')?>',
	changeBlockTemplate: '<?php echo t('Custom Template')?>',
	changeBlockCSS: "<?php echo t("Design")?>",
	changeBlockBaseStyle: "<?php echo t("Set Block Styles")?>",
	confirmBlockCssReset: "<?php echo t("Are you sure you want to remove all of this block's styles?")?>",
	setBlockPermissions: '<?php echo t('Set Permissions')?>',
	setBlockAlias: '<?php echo t('Setup on Child Pages')?>',
	clear: '<?php echo t('Clear')?>',
	helpPopup: '<?php echo t('Help')?>',
	noIE6: '<?php echo t('Concrete5 does not support Internet Explorer 6 in edit mode.')?>',
	helpPopupLoginMsg: '<?php echo t('Get more help on your question by posting it to the concrete5 help center on concrete5.org')?>',
	marketplaceErrorMsg: '<?php echo t('<p>You package could not be installed.  An unknown error occured.</p>')?>',
	marketplaceInstallMsg: '<?php echo t('<p>Your package will now be downloaded and installed.</p>')?>',
	marketplaceLoadingMsg: '<?php echo t('<p>Retrieving information from the Concrete5 Marketplace.</p>')?>',
	marketplaceLoginMsg: '<?php echo t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>')?>',
	marketplaceLoginSuccessMsg: '<?php echo t('<p>You have successfully logged into the concrete5 Marketplace.</p>')?>',
	marketplaceLogoutSuccessMsg: '<?php echo t('<p>You are now logged out of concrete5 Marketplace.</p>')?>',
	deleteAttributeValue: '<?php echo t('Are you sure you want to remove this value?')?>',
	customizeSearch: '<?php echo t('Customize Search')?>',
	properties: '<?php echo t('Properties')?>',
	ok: '<?php echo t('Ok')?>',
	x: '<?php echo t('x')?>'
}

var ccmi18n_sitemap = {

	visitExternalLink: '<?php echo t('Visit')?>',
	editExternalLink: '<?php echo t('Edit External Link')?>',
	deleteExternalLink: '<?php echo t('Delete')?>',
	addExternalLink: '<?php echo t('Add External Link')?>',
	
	visitPage: '<?php echo t('Visit')?>',
	pageProperties: '<?php echo t('Properties')?>',
	setPagePermissions: '<?php echo t('Set Permissions')?>',
	pageDesign: '<?php echo t('Design')?>',
	pageVersions: '<?php echo t('Versions')?>',
	deletePage: '<?php echo t('Delete')?>',
	addPage: '<?php echo t('Add Page')?>',
	moveCopyPage: '<?php echo t('Move/Copy')?>',
	reorderPage: '<?php echo t('Change Page Order')?>',
	reorderPageMessage: '<?php echo t('Move or reorder pages by dragging their icons.')?>',
	moveCopyPageMessage: '<?php echo t('Choose a new parent page from the sitemap.')?>',
	
	searchPages: '<?php echo t('Search Pages')?>',
	backToSitemap: '<?php echo t('Back to Sitemap')?>',
	searchResults: '<?php echo t('Search Results')?>',
	createdBy: '<?php echo t('Created By')?>',
	
	viewing: '<?php echo t('Viewing')?>',
	results: '<?php echo t('Result(s)')?>',
	max: '<?php echo t('max')?>',
	noResults: '<?php echo t('No results found.')?>',
	areYouSure: '<?php echo t('Are you sure?')?>',
	loadError: '<?php echo t('Unable to load sitemap data. Response received: ')?>',
	on: '<?php echo t('on')?>'
	
	
}

var ccmi18n_spellchecker = {

	resumeEditing: '<?php echo t('Resume Editing')?>',
	noSuggestions: '<?php echo t('No Suggestions')?>'
	
}

var ccmi18n_filemanager = {
	
	view: '<?php echo t('View')?>',
	download: '<?php echo t('Download')?>',
	select: '<?php echo t('Choose')?>',
	clear: '<?php echo t('Clear')?>',
	edit: '<?php echo t('Edit')?>',
	replace: '<?php echo t('Replace')?>',
	chooseNew: '<?php echo t('Choose New File')?>',
	sets: '<?php echo t('Sets')?>',
	permissions: '<?php echo t('Access & Permissions')?>',
	properties: '<?php echo t('Properties')?>',
	deleteFile: '<?php echo t('Delete')?>',
	title: '<?php echo t('File Manager')?>',
	uploadErrorChooseFile: '<?php echo t('You must choose a file.')?>',
	rescan: '<?php echo t('Rescan')?>',
	pending: '<?php echo t('Pending')?>',
	uploadComplete: '<?php echo t('Upload Complete')?>',
	
	PTYPE_CUSTOM: '<?php echo FilePermissions::PTYPE_CUSTOM?>',
	PTYPE_NONE: '<?php echo FilePermissions::PTYPE_NONE?>',
	PTYPE_ALL: '<?php echo FilePermissions::PTYPE_ALL?>',

	FTYPE_IMAGE: '<?php echo FileType::T_IMAGE?>',	
	FTYPE_VIDEO: '<?php echo FileType::T_VIDEO?>',	
	FTYPE_TEXT: '<?php echo FileType::T_TEXT?>',	
	FTYPE_AUDIO: '<?php echo FileType::T_AUDIO?>',	
	FTYPE_DOCUMENT: '<?php echo FileType::T_DOCUMENT?>',	
	FTYPE_APPLICATION: '<?php echo FileType::T_APPLICATION?>'
	
}
