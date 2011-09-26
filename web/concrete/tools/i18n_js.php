<?
defined('C5_EXECUTE') or die("Access Denied.");

header('Content-type: text/javascript'); ?>

var ccmi18n = { 
	
	error: "<?=t('Error')?>",
	deleteBlock: "<?=t('Delete')?>",
	deleteBlockMsg: "<?=t('The block has been removed successfully.')?>",
	addBlock: "<?=t('Add Block')?>",
	addBlockNew: "<?=t('Add Block')?>",
	addBlockStack: "<?=t('Add Stack')?>",
	addBlockPaste: "<?=t('Paste from Clipboard')?>",
	changeAreaCSS: "<?=t('Design')?>",
	editAreaLayout: "<?=t('Edit Layout')?>",
	addAreaLayout: "<?=t('Add Layout')?>",
	moveLayoutUp: "<?=t('Move Up')?>",
	moveLayoutDown: "<?=t('Move Down')?>",
	moveLayoutAtBoundary: "<?=t('This layout section can not be moved further in this direction.')?>", 
	lockAreaLayout: "<?=t('Lock Layout')?>", 
	unlockAreaLayout: "<?=t('Unlock Layout')?>", 
	deleteLayout: "<?=t('Delete')?>",
	deleteLayoutOptsTitle: "<?=t('Delete Layout')?>", 
	confirmLayoutPresetDelete: "<?=t('Are you sure you want to delete this layout preset?')?>",
	setAreaPermissions: "<?=t('Set Permissions')?>",
	addBlockMsg: "<?=t('The block has been added successfully.')?>",
	updateBlock: "<?=t('Update Block')?>",
	updateBlockMsg: "<?=t('The block has been saved successfully.')?>",
	copyBlockToScrapbookMsg: "<?=t('The block has been added to your clipboard.')?>",
	closeWindow: "<?=t('Close')?>",
	editBlock: "<?=t('Edit')?>",
	editStackContents: "<?=t('Manage Stack Contents')?>",
	compareVersions: "<?=t('Compare Versions')?>",
	blockAreaMenu: "<?=t("Add Block")?>",
	arrangeBlock: "<?=t('Move')?>",
	arrangeBlockMsg: "<?=t('Blocks arranged successfully.')?>",
	copyBlockToScrapbook: "<?=t('Copy to Clipboard')?>",
	changeBlockTemplate: "<?=t('Custom Template')?>",
	changeBlockCSS: "<?=t("Design")?>",
	errorCustomStylePresetNoName: "<?=t('You must give your custom style preset a name.')?>",
	changeBlockBaseStyle: "<?=t("Set Block Styles")?>",
	confirmCssReset: "<?=t("Are you sure you want to remove all of these custom styles?")?>",
	confirmCssPresetDelete: "<?=t("Are you sure you want to delete this custom style preset?")?>",
	setBlockPermissions: "<?=t('Set Permissions')?>",
	setBlockAlias: "<?=t('Setup on Child Pages')?>",
	setBlockComposerSettings: "<?=t("Composer Settings")?>",
	clear: "<?=t('Clear')?>",
	helpPopup: "<?=t('Help')?>",
	community: "<?=t('concrete5 Community')?>",
	noIE6: "<?=t('Concrete5 does not support Internet Explorer 6 in edit mode.')?>",
	helpPopupLoginMsg: "<?=t('Get more help on your question by posting it to the concrete5 help center on concrete5.org')?>",
	marketplaceErrorMsg: "<?=t('<p>You package could not be installed.  An unknown error occured.</p>')?>",
	marketplaceInstallMsg: "<?=t('<p>Your package will now be downloaded and installed.</p>')?>",
	marketplaceLoadingMsg: "<?=t('<p>Retrieving information from the Concrete5 Marketplace.</p>')?>",
	marketplaceLoginMsg: "<?=t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>')?>",
	marketplaceLoginSuccessMsg: "<?=t('<p>You have successfully logged into the concrete5 Marketplace.</p>')?>",
	marketplaceLogoutSuccessMsg: "<?=t('<p>You are now logged out of concrete5 Marketplace.</p>')?>",
	deleteAttributeValue: "<?=t('Are you sure you want to remove this value?')?>",
	customizeSearch: "<?=t('Customize Search')?>",
	properties: "<?=t('Properties')?>",
	savePropertiesMsg: "<?=t('Page Properties saved.')?>",
	ok: "<?=t('Ok')?>",
	x: "<?=t('x')?>"
}

var ccmi18n_sitemap = {

	visitExternalLink: "<?=t('Visit')?>",
	editExternalLink: "<?=t('Edit External Link')?>",
	deleteExternalLink: "<?=t('Delete')?>",
	addExternalLink: "<?=t('Add External Link')?>",
	sendToTop: "<?=t('Send To Top')?>",
	sendToBottom: "<?=t('Send To Bottom')?>",
	
	visitPage: "<?=t('Visit')?>",
	pageProperties: "<?=t('Properties')?>",
	pagePropertiesTitle: "<?=t('Page Properties')?>",
	setPagePermissions: "<?=t('Set Permissions')?>",
	setPagePermissionsMsg: "<?=t('Page permissions updated successfully.')?>",
	pageDesignMsg: "<?=t('Theme and page type updated successfully.')?>",
	pageDesign: "<?=t('Design')?>",
	pageVersions: "<?=t('Versions')?>",
	deletePage: "<?=t('Delete')?>",
	deletePageSuccessMsg: "<?=t('The page has been removed successfully.')?>",
	addPage: "<?=t('Add Page')?>",
	moveCopyPage: "<?=t('Move/Copy')?>",
	reorderPage: "<?=t('Change Page Order')?>",
	reorderPageMessage: "<?=t('Move or reorder pages by dragging their icons.')?>",
	moveCopyPageMessage: "<?=t('Choose a new parent page from the sitemap.')?>",
	editInComposer: "<?=t('Edit in Composer')?>",
	
	searchPages: "<?=t('Search Pages')?>",
	explorePages: "<?=t('Flat View')?>",
	backToSitemap: "<?=t('Back to Sitemap')?>",
	searchResults: "<?=t('Search Results')?>",
	createdBy: "<?=t('Created By')?>",
	
	viewing: "<?=t('Viewing')?>",
	results: "<?=t('Result(s)')?>",
	max: "<?=t('max')?>",
	noResults: "<?=t('No results found.')?>",
	areYouSure: "<?=t('Are you sure?')?>",
	loadError: "<?=t('Unable to load sitemap data. Response received: ')?>",
	loadErrorTitle: "<?=t('Unable to load sitemap data.')?>",
	on: "<?=t('on')?>"	
	
}

var ccmi18n_spellchecker = {

	resumeEditing: "<?=t('Resume Editing')?>",
	noSuggestions: "<?=t('No Suggestions')?>"
	
}

var ccmi18n_filemanager = {
	
	view: "<?=t('View')?>",
	download: "<?=t('Download')?>",
	select: "<?=t('Choose')?>",
	duplicateFile: "<?=t('Copy File')?>",
	clear: "<?=t('Clear')?>",
	edit: "<?=t('Edit')?>",
	replace: "<?=t('Replace')?>",
	duplicate: "<?=t('Copy')?>",
	chooseNew: "<?=t('Choose New File')?>",
	sets: "<?=t('Sets')?>",
	permissions: "<?=t('Access & Permissions')?>",
	properties: "<?=t('Properties')?>",
	deleteFile: "<?=t('Delete')?>",
	title: "<?=t('File Manager')?>",
	uploadErrorChooseFile: "<?=t('You must choose a file.')?>",
	rescan: "<?=t('Rescan')?>",
	pending: "<?=t('Pending')?>",
	uploadComplete: "<?=t('Upload Complete')?>",
	
	PTYPE_CUSTOM: "<?=FilePermissions::PTYPE_CUSTOM?>",
	PTYPE_NONE: "<?=FilePermissions::PTYPE_NONE?>",
	PTYPE_ALL: "<?=FilePermissions::PTYPE_ALL?>",

	FTYPE_IMAGE: "<?=FileType::T_IMAGE?>",	
	FTYPE_VIDEO: "<?=FileType::T_VIDEO?>",	
	FTYPE_TEXT: "<?=FileType::T_TEXT?>",	
	FTYPE_AUDIO: "<?=FileType::T_AUDIO?>",	
	FTYPE_DOCUMENT: "<?=FileType::T_DOCUMENT?>",	
	FTYPE_APPLICATION: "<?=FileType::T_APPLICATION?>"
	
}
