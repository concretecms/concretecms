<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Dashboard'), false, false, false, false, false, false); ?>

<div class="ccm-pane-body" style="padding-bottom: 0px">


<?
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	<div class="dashboard-icon-list">
	<div class="well" style="visibility: hidden">

	<ul class="nav nav-list">
	<li class="nav-header"><?=t($cat->getCollectionName())?></li>
		
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead() && !$subcat->getAttribute('exclude_nav')) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<? foreach($show as $subcat) { ?>
	
	<li>
	<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><i class="<?=$subcat->getAttribute('icon_dashboard')?>"></i> <?=t($subcat->getCollectionName())?></a>
	</li>
	
	<? } ?>
	
	
	<? } else { ?>
	
	<li>
		<a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><i class="<?=$cat->getAttribute('icon_dashboard')?>"></i> <?=t('Home')?></a>
	</li>
		
	<? } ?>

	</ul>
	</div>
	</div>
	
	
<? } ?>

	<div class="clearfix"></div>
	
</div>

<div class="ccm-pane-footer">
<?
	$newsPage = Page::getByPath('/dashboard/news');
	$newsPageP = new Permissions($newsPage);
	if ($newsPageP->canRead()) { ?>
		<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($newsPage, false, true)?>"><strong><?=t('News')?></strong></a> - <?=t('Learn about your site and concrete5.')?></div>
	<? }

	$settingsPage = Page::getByPath('/dashboard/system');
	$settingsPageP = new Permissions($settingsPage);
	if ($settingsPageP->canRead()) { ?>
		<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($settingsPage, false, true)?>"><strong><?=t('System &amp; Settings')?></strong></a> - <?=t('Secure and setup your site.')?></div>
	<? }
	
	$tpa = new TaskPermission();
	$extendPage = Page::getByPath('/dashboard/extend');
	$extendPageP = new Permissions($extendPage);
	if ($tpa->canInstallPackages() && $extendPageP->canRead()) { ?>
		<div><a href="<?php echo View::url('/dashboard/extend') ?>"><strong><?php echo t("Extend concrete5") ?></strong></a> – 
		<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>
		<?php echo sprintf(t('<a href="%s">Install</a>, <a href="%s">update</a> or download more <a href="%s">themes</a> and <a href="%s">add-ons</a>.'),
			View::url('/dashboard/extend/install'),
			View::url('/dashboard/extend/update'),
			View::url('/dashboard/extend/themes'),
			View::url('/dashboard/extend/add-ons')); ?>
		<? } else { ?>
		<?php echo sprintf(t('<a href="%s">Install</a> or <a href="%s">update</a> packages.'),
			View::url('/dashboard/extend/install'),
			View::url('/dashboard/extend/update')); 
		} ?>
		</div>
	<? } ?>
	
</div>
<script type="text/javascript">
$(function() {
	ccm_dashboardEqualizeMenus();
	$(window).resize(function() {
		ccm_dashboardEqualizeMenus();
	});
});
</script>


<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
