<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');

$ih = Loader::helper('concrete/interface'); 
$f = File::getByID($_REQUEST['fID']);
$cp = new Permissions($f);
if (!$cp->canAdmin()) {
	die(_("Access Denied."));
}
$form = Loader::helper('form');

if ($_POST['task'] == 'set_password') {
	$f->setPassword($_POST['fPassword']);
	exit;
}
?>

<ul class="ccm-dialog-tabs" id="ccm-file-permissions-tabs" style="display:<?=($_REQUEST['addOnly']!=1)?'block':'none'?>">
	<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-permissions-advanced"><?=t('Permissions')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-file-password"><?=t('Protect with Password')?></a></li>
</ul>

<div id="ccm-file-permissions-advanced-tab">

<br/>

<h2><?=t('File Permissions')?></h2>

<form method="post" id="ccm-file-permissions-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?=$form->hidden('task', 'set_advanced_permissions')?>
<?=$form->hidden('fID', $f->getFileID())?>

<div class="ccm-important">
<? if (!$f->overrideFileSetPermissions()) { ?>
	<?=t('Permissions for this file are currently dependent on set and global settings. If you override those permissions here, they will not match those of the file\'s sets.')?><br/><br/>
<? } else { ?>
	<?=t("Permissions for this file currently override file set and global settings. To revert these permissions, click the button below.")?><br/><br/>
<? } ?>	
</div>




</form>

</div>

<div id="ccm-file-password-tab" style="display: none">
<br/>

<h2><?=t('Requires Password to Access')?></h2>

<p><?=t('Leave the following form field blank in order to allow everyone to download this file.')?></p>

<form method="post" id="ccm-file-password-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?=$form->hidden('task', 'set_password')?>
<?=$form->hidden('fID', $f->getFileID())?>
<?=$ih->button_js(t('Save Password'), 'ccm_alSubmitPasswordForm()')?>
<?=$form->text('fPassword', $f->getPassword(), array('style' => 'width: 250px'))?>

</form>

<div class="ccm-spacer">&nbsp;</div>
<br/>
<div class="ccm-note"><?=t('Users who access files through the file manager will not be prompted for a password.')?></div>

</div>


<script type="text/javascript">
	

var ccm_fpActiveTab = "ccm-file-permissions-advanced";

$("#ccm-file-permissions-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_fpActiveTab + "-tab").hide();
	ccm_fpActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_fpActiveTab + "-tab").show();
});


$(function() {
	ccm_alSetupPasswordForm();
});
</script>
