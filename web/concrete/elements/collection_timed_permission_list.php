<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<?
$assignments = $cp->getAllTimedAssignmentsForPage();
if (count($assignments) > 0) { ?>

<table class="ccm-permission-grid">
<?
foreach($assignments as $ppc) {
	$pk = $ppc->getPermissionKeyObject();
	?>
	<tr>
	<td>
	<strong><?=tc('PermissionKeyName', $pk->getPermissionKeyName())?></strong>
	<?=t('Permission on ')?><?
		if ($pk instanceof AreaPermissionKey) {  ?>
			<strong><?=$pk->getPermissionObject()->getAreaHandle() ?></strong>.
		<? } else if ($pk instanceof BlockPermissionKey) { 
			$bt = BlockType::getByID($pk->getPermissionObject()->getBlockTypeID());
			$obj = $pk->getPermissionObject();
			if ($obj->getBlockName() != '') { ?>

			<?=t('the %s block named <strong>%s</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $obj->getBlockName(), $pk->getPermissionObject()->getAreaHandle())?>
			
			<? } else { ?>
			
			<?=t('<strong>%s Block</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $pk->getPermissionObject()->getAreaHandle())?>
			
			<? } ?>		
		<? } else { ?>
			<strong><?=t('Entire Page')?></strong>.
		<? } ?>
		<?
		$pd = $ppc->getDurationObject();
		?>
		<div>
		<? 
		$assignee = t('Nobody');
		$pae = $ppc->getAccessEntityObject();
		if (is_object($pae)) {
			$assignee = $pae->getAccessEntityLabel();
		}?>
		<?=t('Assigned to <strong>%s</strong>. ', $assignee)?>
		<?=$pd->getTextRepresentation()?>
		</div>
	</td>
	</tr>
<? } ?>
</table>

<? } else { ?>
	<p><?=t('No timed permission assignments')?></p>
<? } ?>

</div>
