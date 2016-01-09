<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$included = $permissionAccess->getAccessListItems();
$form = Loader::helper('form');

if (count($included) > 0) { ?>

	<h3><?=t('New Message Approval')?></h3>

	<? foreach($included as $assignment) {
		$entity = $assignment->getAccessEntityObject();
	?>


<div class="form-group">
	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('addMessageApproval[' . $entity->getAccessEntityID() . ']', array('A' => t('Approved'), 'U' => t('Pending')), $assignment->getNewConversationMessageApprovalStatus())?>
</div>

<? }

} else {  ?>
	<p><?=t('No access entities selected.')?></p>
<? } ?>