<?php defined('C5_EXECUTE') or die("Access Denied.");
$navigation = Loader::helper('navigation');
$th = Loader::helper('text');
$sh = Loader::helper('concrete/dashboard');
if (!$sh->canAccessComposer()) {
	die(t('Access Denied'));
}

$entry = ComposerPage::getByID($_REQUEST['cID'], 'RECENT');
if (!is_object($entry)) {
	die(t('Access Denied'));
}

$ct = CollectionType::getByID($entry->getCollectionTypeID());
$function = 'ccm_composerSelectParentPage';
if ($_REQUEST['submitOnChoose']) {
	$function = 'ccm_composerSelectParentPageAndSubmit';
}

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
	<ul class="item-select-list">
	<? foreach($pages as $p) { 
		$trail = $navigation->getTrailToCollection($p);
		$crumbs = array();
		if(is_array($trail) && count($trail)) {
			$trail = array_reverse($trail,false);
			foreach($trail as $t) { 
				$crumbs[] = $th->shortText($t->getCollectionName(),10);
			}
		}
		?>
		<li class="item-select-page"><a href="javascript:void(0)" onclick="<?=$function?>(<?=$p->getCollectionID()?>)"><?=$p->getCollectionName()?></a>
			<div class="ccm-note" style="padding-left: 8px;"><?php echo implode(" &gt; ",$crumbs)?></div>
		</li>
	<? } ?>
	</ul>
	
	<?
		break;
	case 'CHOOSE':
		$args['sitemapCombinedMode'] = $sitemapCombinedMode;
		$args['select_mode'] = 'select_page';
		$args['callback'] = $function;
		$args['display_mode'] = 'full';
		$args['instance_id'] = time();
		Loader::element('dashboard/sitemap', $args);	
		break;
}

exit;