<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $included = $permissionKey->getAssignmentList(); ?>
<? $excluded = $permissionKey->getAssignmentList(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?
Loader::model('search/group');
$gl = new GroupSearch();
$gl->filter('gID', REGISTERED_GROUP_ID, '>');
$gl->sortBy('gID', 'asc');
$gIDs = $gl->get();
$gArray = array();
foreach($gIDs as $gID) {
	$groups[] = Group::getByID($gID);
}
?>
<? $form = Loader::helper('form'); ?>

<form id="ccm-user-permissions-search-user-form" onsubmit="return false" method="post" action="<?=$permissionKey->getPermissionKeyToolsURL()?>">

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<div class="well clearfix">

<? if (count($included) > 0) { ?>

<h3><?=t('Who can assign what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('groupsIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Groups'), 'C' => t('Custom')), $assignment->getGroupsAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getGroupsAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($groups as $g) { ?>
			<li><label><input type="checkbox" name="gIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$g->getGroupID()?>" <? if ($assignment->getGroupsAllowedPermission() == 'A' || in_array($g->getGroupID(), $assignment->getGroupsAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=$g->getGroupName()?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>

<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t assign what?')?></h3>

<? foreach($excluded as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('groupsExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Groups'), 'C' => t('Custom')), $assignment->getGroupsAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getGroupsAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($groups as $g) { ?>
			<li><label><input type="checkbox" name="gIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $assignment->getGroupsAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=$g->getGroupName()?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>



<? }

} ?>


<input type="submit" class="btn primary ccm-button-right" onclick="$('#ccm-user-permissions-search-user-form').submit()" value="<?=t('Update Custom Settings')?>" />
</div>

<? } ?>

</form>

<script type="text/javascript">
$(function() {
	$("#ccm-user-permissions-search-user-form select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('ul.inputs-list').show();
		} else {
			$(this).parent().find('ul.inputs-list').hide();
		}
	});
	
	$("#ccm-user-permissions-search-user-form").ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
});
</script>