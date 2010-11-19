<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$displayGroups = true;
$displayUsers = true;

if ($_REQUEST['mode'] == 'users') {
	$displayGroups = false;
} else if ($_REQUEST['mode'] == 'groups') {
	$displayUsers = false;
}

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
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

<?php  if ($displayGroups && $displayUsers) { ?>

<ul class="ccm-dialog-tabs" id="ccm-ug-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-select-group"><?php echo t('Groups')?></a></li>
<li><a href="javascript:void(0)" id="ccm-select-user"><?php echo t('Users')?></a></li>
</ul>

<?php  } ?>

<?php  if ($displayGroups) { ?>

<div id="ccm-select-group-tab">

<h1><?php echo t('Select Group')?></h1>
<?php  include(DIR_FILES_TOOLS_REQUIRED . '/select_group.php'); ?>

</div>

<?php  } ?>

<?php  if ($displayUsers) { ?>

<div id="ccm-select-user-tab" style="display: none">
<h1><?php echo t('Select User')?></h1>

<?php  
$mode = 'choose_multiple';
include(DIR_FILES_TOOLS_REQUIRED . '/users/search_dialog.php'); ?>

</div>

<?php  } ?>