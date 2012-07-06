<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Dashboard'), false, false, false); ?>

<div class="ccm-pane-body">


<?
print '<table style="border-spacing: 10px; border-collapse: separate" width="100%">';
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	
	<? if ($i % 4 == 0 && $i > 0) { ?>
		</tr>
		<tr>
	<? } ?>
	
	<? if ($i == 0) { ?>
		<tr>
	<? } ?>
	
	<td width="25%" class="well" style="vertical-align: top; padding: 10px 0px">


	<ul class="nav nav-list">
	<li class="nav-header"><?=t($cat->getCollectionName())?></li>
		
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead()) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<? foreach($show as $subcat) { ?>
	
	<li>
	<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><?=t($subcat->getCollectionName())?></a>
	</li>
	
	<? } ?>
	
	
	<? } else { ?>
	
	<li>
		<a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><?=t('Home')?></a>
	</li>
		
	<? } ?>

	</ul>

	</td>
	
	
<? } ?>

<? if ($i % 4 != 0 && $i > 0) { ?>
	</tr>
<? } ?>

</table>
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
