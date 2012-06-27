<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Dashboard'), false, false, false); ?>

<div class="ccm-pane-body">


<?
print '<div class="row">';
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	
	<? if ($i % 4 == 0) { ?>
		</div>
		<div class="row">
	<? } ?>
	
	<div class="span-pane-fourth">
	


	<div class="ccm-dashboard-system-category">
	<h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><?=t($cat->getCollectionName())?></a></h3>
	</div>
	
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead() && $subcat->getAttribute('exclude_nav') != 1) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<div class="ccm-dashboard-system-category-inner">
	
	<? foreach($show as $subcat) { ?>
	
	<div>
	<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><?=t($subcat->getCollectionName())?></a>
	</div>
	
	<? } ?>
	
	</div>
	
	<? } else { ?>
	
	<div class="ccm-dashboard-system-category-inner">
		<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><?=t('Home')?></a></div>
	</div>
		
	<? } ?>
	</div>
	
	
<? } ?>

</div>
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
	if ($tpa->canInstallPackages()) { ?>
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


<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
