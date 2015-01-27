<? defined('C5_EXECUTE') or die("Access Denied."); 
$parents = Loader::helper('navigation')->getTrailToCollection($c);
$pageIDs = array();
foreach($parents as $pc) {
	$pageIDs[] = $pc->getCollectionID();
}
?>
<section>
	<div data-panel-menu="accordion" class="ccm-panel-header-accordion">
	<nav>
	<span><? if (!in_array($tab, array('favorites'))) { ?><?=t('Dashboard')?><? } else { ?><?=t('Favorites')?><? } ?></span>
	<ul class="ccm-panel-header-accordion-dropdown">
		<li><a data-panel-accordion-tab="dashboard" <? if (!in_array($tab, array('favorites'))) { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=t('Dashboard')?></a></li>
		<li><a data-panel-accordion-tab="favorites" <? if ($tab == 'favorites') { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=t('Favorites')?></a></li>
	</ul>
	</nav>
	</div>

		<? if ($tab == 'favorites') { ?>
			<menu>
			<? foreach($nav as $cc) {
				$active = ($cc->getCollectionID() == $c->getCollectionID() || (in_array($cc->getCollectionID(), $pageIDs)));
				$cp = new Permissions($cc);
				if ($cp->canViewPage()) { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($cc)?>" <? if ($active) { ?>class="ccm-panel-dashboard-nav-active"<? } ?>><?=t($cc->getCollectionName())?></a></li>
				<? } ?>
			<? } ?>
			</menu>
		<? } else { ?>
			<? $nav->render('dashboard_navigation'); ?>
		<? } ?>

	
	<div class="ccm-panel-dashboard-footer">
		<p><?=t('Logged in as <a href="%s">%s</a>', URL::to('/account'), $ui->getUserDisplayName());?>. </p>
		<a href="<?=URL::to('/login', 'logout', Loader::helper('validation/token')->generate('logout'))?>"><?=t('Sign Out.')?></a>
	</div>
</section>
