<?
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard');
if (!$sh->canAccessComposer()) {
	die(t('Access Denied'));
}

$entry = ComposerPage::getByID($_REQUEST['cID'], 'RECENT');
if (!is_object($entry)) {
	die(t('Access Denied'));
}

$ct = CollectionType::getByID($entry->getCollectionTypeID());


switch($ct->getCollectionTypeComposerPublishMethod()) {
	case 'PAGE_TYPE': 
		Loader::model('page_list');
		$pages = array();
		$pl = new PageList();
		$pl->sortByName();
		$pl->filterByCollectionTypeID($ct->getCollectionTypeComposerPublishPageTypeID());
		$pages = $pl->get();
		
		?>
	
	<h1><?=t("Where do you want to publish this page?")?></h1>
	<ul class="icon-select-list">
	<? foreach($pages as $p) { ?>
		<li class="icon-select-page"><a href="javascript:void(0)" onclick="ccm_composerSelectParentPageAndSubmit(<?=$p->getCollectionID()?>)"><?=$p->getCollectionName()?></a></li>
	<? } ?>
	</ul>
	
	<?
		break;
	case 'CHOOSE':
		$args['sitemapCombinedMode'] = $sitemapCombinedMode;
		$args['select_mode'] = 'select_page';
		$args['callback'] = 'ccm_composerSelectParentPageAndSubmit';
		$args['display_mode'] = 'full';
		$args['instance_id'] = time();
		Loader::element('dashboard/sitemap', $args);	
		break;
}

exit;