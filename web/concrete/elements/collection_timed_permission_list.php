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
	<strong><?=$pk->getPermissionKeyName()?></strong>
	<?=t('Permission on ')?><?
		if ($pk instanceof AreaPermissionKey) {  ?>
			<strong><?=$pk->getPermissionObject()->getAreaHandle() ?></strong>.
		<? } else if ($pk instanceof BlockPermissionKey) { 
			$bt = BlockType::getByID($pk->getPermissionObject()->getBlockTypeID());
			?>		
			<?=t('<strong>%s Block</strong> in <strong>%s</strong> Area. ', $bt->getBlockTypeName(), $pk->getPermissionObject()->getAreaHandle())?>
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
</div>

<? } else { ?>
	<p><?=t('No timed permission assignments')?></p>
<? } ?>

</div>
