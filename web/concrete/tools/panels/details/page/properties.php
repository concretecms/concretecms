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
			<div class="container">
			<header><?=t('Properties')?></header>
			<form method="post" class="" action="<?=$c->getCollectionAction()?>">

			<? if ($asl->allowEditName()) { ?>
			<div class="form-group">
				<label for="cName" class="control-label"><?=t('Name')?></label>
				<div>
				<input type="text" class="form-control" id="cName" name="cName" value="<?=htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" />
				</div>
			</div>
			<? } ?>

			<? if ($asl->allowEditDateTime()) { ?>
			<div class="form-group">
				<label for="cName" class="control-label"><?=t('Created Time')?></label>
				<div>
					<? print $dt->datetime('cDatePublic', $c->getCollectionDatePublic()); ?>
				</div>
			</div>
			<? } ?>
			
			<? if ($asl->allowEditUserID()) { ?>
			<div class="form-group">
				<label for="cName" class="control-label"><?=t('Author')?></label>
				<div>
				<? 
				print $uh->selectUser('uID', $c->getCollectionUserID());
				?>
				</div>
			</div>
			<? } ?>
			

			<? if ($asl->allowEditDescription()) { ?>
			<div class="form-group">
				<label for="cDescription" class="control-label"><?=t('Description')?></label>
				<div>
					<textarea id="cDescription" name="cDescription" class="form-control" rows="8"><?=$c->getCollectionDescription()?></textarea>
				</div>
			</div>
			<? } ?>

			</form>		

			<div class="ccm-panel-detail-form-actions">
				<button class="pull-left btn" type="button"><?=t('Cancel')?></button>
				<button class="pull-right btn btn-primary" type="button"><?=t('Save Changes')?></button>
			</div>
			</div>	
		</section>
	<? }
}
?>