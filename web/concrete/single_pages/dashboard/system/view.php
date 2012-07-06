<?
$upToPage = Page::getByPath("/dashboard");
?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'), false, false, true, -1, $upToPage); ?>

<?
print '<table style="border-spacing: 10px; border-collapse: separate" width="100%">';
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	
	<? if ($i % 3 == 0 && $i > 0) { ?>
		</tr>
		<tr>
	<? } ?>
	
	<? if ($i == 0) { ?>
		<tr>
	<? } ?>
	
	<td width="33%" class="well" style="vertical-align: top; padding: 10px 0px">


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

<? if ($i % 3 != 0 && $i > 0) { ?>
	</tr>
<? } ?>

</table>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
