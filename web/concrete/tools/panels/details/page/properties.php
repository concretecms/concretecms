<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	$pk = PermissionKey::getByHandle('edit_page_properties');
	$asl = $pk->getMyAssignment();
	$dt = Loader::helper('form/date_time');
	$uh = Loader::helper('form/user_selector');
	if ($cp->canEditPageProperties()) { ?>

		<section class="ccm-ui">
			<header><?=t('Properties')?></header>
			<form method="post" class="form-horizontal" action="<?=$c->getCollectionAction()?>">
			<input type="hidden" name="approveImmediately" value="<?=$approveImmediately?>" />
			<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />
			
			<? if ($asl->allowEditName()) { ?>
			<div class="form-group">
				<label for="cName" class="form-label"><?=t('Name')?></label>
				<div class="input"><input type="text" id="cName" name="cName" value="<?=htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" />
					<span class="help-inline"><?=t("Page ID: %s", $c->getCollectionID())?></span>
				</div>
			</div>
			<? } ?>

			<? if ($asl->allowEditDateTime()) { ?>
			<div class="form-group">
				<label for="cDatePublic" class="form-label"><?=t('Public Date/Time')?></label>
				<div class="input"><? print $dt->datetime('cDatePublic', $c->getCollectionDatePublic(null, 'user')); ?></div>
			</div>
			<? } ?>
			
			<? if ($asl->allowEditUserID()) { ?>
			<div class="form-group">
			<label class="form-label"><?=t('Owner')?></label>
			<div class="input">
				<? 
				print $uh->selectUser('uID', $c->getCollectionUserID());
				?>
			</div>
			</div>
			<? } ?>
			

			<? if ($asl->allowEditDescription()) { ?>
			<div class="clearfix">
			<label for="cDescription"><?=t('Description')?></label>
			<div class="input"><textarea id="cDescription" name="cDescription" class="ccm-input-text" style="width: 495px; height: 50px"><?=$c->getCollectionDescription()?></textarea></div>
			</div>
			<? } ?>

			</form>			
		</section>
	<? }
}
?>