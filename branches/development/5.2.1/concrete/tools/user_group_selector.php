<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$displayGroups = true;
$displayUsers = true;

if ($_REQUEST['mode'] == 'users') {
	$displayGroups = false;
} else if ($_REQUEST['mode'] == 'groups') {
	$displayUsers = false;
}

$c1 = Page::getByPath('/dashboard/users');
$cp1 = new Permissions($c1);
$c2 = Page::getByPath('/dashboard/users/groups');
$cp2 = new Permissions($c2);
if ((!$cp1->canRead()) && (!$cp2->canRead())) {
	die(_("Access Denied."));
}


?>

<script type="text/javascript">
var ccm_ugActiveTab = "ccm-select-group";

$("#ccm-ug-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_ugActiveTab + "-tab").hide();
	ccm_ugActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_ugActiveTab + "-tab").show();
});

</script>

<? if ($displayGroups && $displayUsers) { ?>

<ul class="ccm-dialog-tabs" id="ccm-ug-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-select-group"><?=t('Groups')?></a></li>
<li><a href="javascript:void(0)" id="ccm-select-user"><?=t('Users')?></a></li>
</ul>

<? } ?>

<? if ($displayGroups) { ?>

<div id="ccm-select-group-tab">

<h1><?=t('Select Group')?></h1>

<? include(DIR_FILES_TOOLS_REQUIRED . '/select_group.php'); ?>

</div>

<? } ?>

<? if ($displayUsers) { ?>

<div id="ccm-select-user-tab" style="display: none">
<h1><?=t('Select User')?></h1>

<? include(DIR_FILES_TOOLS_REQUIRED . '/select_user.php'); ?>

</div>

<? } ?>