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

<h2><?=t('Requires Password to Access')?></h2>

<p><?=t('Leave the following form field blank in order to allow everyone to download this file.')?></p>

<form method="post" id="ccm-file-password-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?=$form->hidden('task', 'set_password')?>
<?=$form->hidden('fID', $f->getFileID())?>
<?=$form->text('fPassword', $f->getPassword(), array('style' => 'width: 150px'))?>
<?=$ih->button_js(t('Save Password'), 'ccm_alSubmitPasswordForm()')?>
</div>

</form>

<div class="ccm-spacer">&nbsp;</div>
<br/>
<div class="ccm-note"><?=t('Users who access files through the file manager will not be prompted for a password.')?></div>

<script type="text/javascript">
$(function() {
	ccm_alSetupPasswordForm();
});
</script>
