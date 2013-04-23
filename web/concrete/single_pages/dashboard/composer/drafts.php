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
<?
$num = 0;
foreach($drafts as $dr) { 
	$pdr = new Permissions($dr);
	if ($pdr->canEditComposerDraft()) { 
		$num++;
		$d = $dr->getComposerDraftCollectionObject();
		$pageName = ($d->getCollectionName()) ? $d->getCollectionName() : t('(Untitled Page)');
		?>
	<tr>
		<td><a href="<?=$this->url('/dashboard/composer/write', 'draft', $dr->getComposerDraftID())?>"><?=$pageName?></a></td>
		<td><?=$d->getCollectionTypeName()?></td>
		<td><?
			$mask = DATE_APP_GENERIC_MDYT;
			if ($today == $d->getCollectionDateLastModified("Y-m-d")) {
				$mask = DATE_APP_GENERIC_T;
			}
			print $d->getCollectionDateLastModified($mask)?></td>
	<? } ?>

<? } ?>
</table>

<? } 

if ($num == 0) { ?>
	
	<p><?=t('There are no drafts. <a href="%s">Visit Composer &gt;</a>', $this->url('/dashboard/composer/write'))?></p>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>