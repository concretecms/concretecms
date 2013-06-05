<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$u = new User();
	if ($u->isRegistered()) {
		$ihm = Loader::helper('concrete/interface/menu');
		/*
		$ihm->addPageHeaderMenuItem('sitemap', t('Sitemap'), 'right', array(
			'dialog-title' => t('Navigate to Page'),
			'href' => REL_DIR_FILES_TOOLS_REQUIRED . '/sitemap_search_selector?callback=ccm_goToSitemapNode&sitemap_select_mode=select_page',
			'dialog-on-open' => "$(\'#ccm-page-edit-nav-sitemap\').removeClass(\'ccm-nav-loading\')",
			'dialog-width' => '90%',
			'dialog-height' => "70%",
			'dialog-modal' => "false"
		));
	
		$ihm->addPageHeaderMenuItem('filemanager', t('File Manager'), 'right', array(
			'dialog-title' => t('Navigate to Page'),
			'href' => REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_dialog?disable_choose=1',
			'dialog-on-open' => "$(\'#ccm-page-edit-nav-filemanager\').removeClass(\'ccm-nav-loading\')",
			'dialog-width' => '90%',
			'dialog-height' => "70%",
			'dialog-modal' => "false"
		));
		*/
	
	}