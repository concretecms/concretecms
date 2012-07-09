<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Drafts'))?>

<? 
$today = Loader::helper('date')->getLocalDateTime('now', 'Y-m-d');
if (count($drafts) > 0) { ?>

<table class="table table-striped">
<tr>
	<th width="60%"><?=t('Page Name')?></th>
	<th width="20%"><?=t('Page Type')?></th>
	<th width="20%"><?=t('Last Modified')?></th>
</tr>
<? foreach($drafts as $dr) { ?>
<tr>
	<td><a href="<?=$this->url('/dashboard/composer/write', 'edit', $dr->getCollectionID())?>"><? if (!$dr->getCollectionName()) {
		print t('(Untitled Page)');
	} else {
		print $dr->getCollectionName();
	} ?></a></td>
	<td><?=$dr->getCollectionTypeName()?></td>
	<td><?
		$mask = DATE_APP_GENERIC_MDYT;
		if ($today == $dr->getCollectionDateLastModified("Y-m-d")) {
			$mask = DATE_APP_GENERIC_T;
		}
		print $dr->getCollectionDateLastModified($mask)?></td>
<? } ?>
</table>

<? } else { ?>
	
	<p><?=t('You have not created any drafts. <a href="%s">Visit Composer &gt;</a>', $this->url('/dashboard/composer/write'))?></p>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>