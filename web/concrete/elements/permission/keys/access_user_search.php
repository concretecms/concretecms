<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? $included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE); ?>
<? $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?
Loader::model('search/group');
$gl = new GroupSearch();
$gl->filter('gID', GUEST_GROUP_ID, '>');
$gl->sortBy('gID', 'asc');
$gIDs = $gl->get();
$gArray = array();
foreach($gIDs as $gID) {
	$groups[] = Group::getByID($gID['gID']);
}
?>
<? $form = Loader::helper('form'); ?>

<? if (count($included) > 0 || count($excluded) > 0) { ?>

<? if (count($included) > 0) { ?>

<h3><?=t('Who can search what?')?></h3>

<? foreach($included as $assignment) {
	$entity = $assignment->getAccessEntityObject(); 
?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('groupsIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Groups'), 'C' => t('Custom')), $assignment->getGroupsAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <? if ($assignment->getGroupsAllowedPermission() != 'C') { ?>style="display: none"<? } ?>>
		<? foreach($groups as $g) { ?>
			<li><label><input type="checkbox" name="gIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $assignment->getGroupsAllowedArray())) { ?> checked="checked" <? } ?> /> <span><?=$g->getGroupName()?></span></label></li>
		<? } ?>
	</ul>
	</div>
</div>

<? }

} ?>


<? if (count($excluded) > 0) { ?>

<h3><?=t('Who can\'t search what?')?></h3>

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

<? } else {  ?>
	<p><?=t('No users or groups selected.')?></p>
<? } ?>

<script type="text/javascript">
$(function() {
	$("#ccm-tab-content-custom-options select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('ul.inputs-list').show();
		} else {
			$(this).parent().find('ul.inputs-list').hide();
		}
	});
});
</script>