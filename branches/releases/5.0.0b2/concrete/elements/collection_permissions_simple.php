<? if ($cp->canAdminPage()) {
$home = Page::getByID($_REQUEST['cID']);
$gl = new GroupList($home);
$gArrayTmp = $gl->getGroupList();
$gArray = array();
foreach($gArrayTmp as $gi) {
	if ($gi->getGroupID() == GUEST_GROUP_ID) {
		$ggu = $gi;
	} else if ($gi->getGroupID() == REGISTERED_GROUP_ID) {
		$gru = $gi;
	} else {
		$gArray[] = $gi;
	}
}
?>

<div class="ccm-pane-controls">
<form method="post" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

<h1>Page Access</h1>

<div class="ccm-form-area">

<div class="ccm-field">

<h2>Who can view this page?</h2>

<input type="radio" name="view" value="ANYONE" style="vertical-align: middle" <? if ($ggu->canRead()) { ?> checked <? } ?> /> Anyone
&nbsp;&nbsp;
<input type="radio" name="view" value="USERS" style="vertical-align: middle" <? if ($gru->canRead()) { ?> checked <? } ?> /> Registered users
&nbsp;&nbsp;
<input type="radio" name="view" value="PRIVATE" style="vertical-align: middle" <? if ((!$ggu->canRead()) && (!$gru->canRead())) { ?> checked <? } ?> /> Only the administrator
&nbsp;&nbsp;

</div>

<div class="ccm-field">

<h2>Who can edit this page?</h2>

<?

foreach ($gArray as $g) {
?>

<input type="checkbox" name="gID[]" value="<?=$g->getGroupID()?>" <? if ($g->canWrite()) { ?> checked <? } ?> /> <?=$g->getGroupName()?><br/>

<? } ?>


</div>
</div>

<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="ccm_submit()" class="ccm-button-right accept"><span>Save</span></a>
</div>	
<input type="hidden" name="update_permissions" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
ccm_submit = function() {
	//ccm_showTopbarLoader();
	$('form[name=ccmPermissionsForm]').get(0).submit();
}
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
<? } ?>