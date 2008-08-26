<?

$displayGroups = true;
$displayUsers = true;

if ($_REQUEST['mode'] == 'users') {
	$displayGroups = false;
} else if ($_REQUEST['mode'] == 'groups') {
	$displayUsers = false;
}

$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if (!$cp->canAdminPage()) {
	exit;
}

$gl = new GroupList(null, true);
$gArray = $gl->getGroupList();

?>

<script type="text/javascript">
var ccm_areaActiveTab = "ccm-select-group";

$("#ccm-ug-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_areaActiveTab + "-tab").hide();
	ccm_areaActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_areaActiveTab + "-tab").show();
});

$("a.ccm-group-inner").click(function() {
	ccm_addGroup($(this).attr('group-id'), $(this).attr('group-name'));
	jQuery.fn.dialog.closeTop();
});

</script>

<? if ($displayGroups && $displayUsers) { ?>

<ul class="ccm-dialog-tabs" id="ccm-ug-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-select-group">Groups</a></li>
<li><a href="javascript:void(0)" id="ccm-select-user">Users</a></li>
</ul>

<? } ?>

<? if ($displayGroups) { ?>

<div id="ccm-select-group-tab">

<h1>Select Group</h1>

<? foreach ($gArray as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" id="g<?=$g->getGroupID()?>" group-id="<?=$g->getGroupID()?>" group-name="<?=$g->getGroupName()?>" href="javascript:void(0)" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$g->getGroupName()?></a>
	</div>


<? } ?>
</div>

<? } ?>

<? if ($displayUsers) { ?>

<div id="ccm-select-user-tab" style="display: none">
<h1>Select User</h1>

<? include(DIR_FILES_TOOLS_REQUIRED . '/select_user.php'); ?>

</div>

<? } ?>