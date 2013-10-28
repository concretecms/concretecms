<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section>
	<div data-panel-menu="accordion" class="ccm-panel-header-accordion">
	<nav>
	<span></span>
	<ul class="ccm-panel-header-accordion-dropdown">
		<li><a data-panel-accordion-tab="dashboard" <? if (!in_array($tab, array('favorites'))) { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=t('Dashboard')?></a></li>
		<li><a data-panel-accordion-tab="favorites" <? if ($tab == 'favorites') { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=t('Favorites')?></a></>
	</ul>
	</nav>
	</div>

	<menu>
		<? foreach($nav as $cc) {
			$cp = new Permissions($cc);
			if ($cp->canViewPage() && $cc->getAttribute('exclude_nav') != true) { ?>
				<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($cc)?>"><?=$cc->getCollectionName()?></a></li>
			<? }
		} ?>

	</menu>

	<div class="ccm-panel-dashboard-footer">
		<p><?=t('Logged in as %s', $ui->getUserDisplayName());?>.</p>
		<a href="<?=URL::to('/login', 'logout', Loader::helper('validation/token')->generate('logout'))?>"><?=t('Sign Out.')?></a>
	</div>
</section>