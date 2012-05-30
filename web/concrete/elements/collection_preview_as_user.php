<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$form = Loader::helper("form");
$u = new User();
$date = Loader::helper('form/date_time');
$us = Loader::helper('form/user_selector'); ?>

<div class="ccm-ui">
<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<form id="ccm-collection-preview-as-user-form" class="form-horizontal" method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_as_user" target="ccm-collection-preview-as-user-frame">
	<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>" />
	<div class="control-group">
	
	<label class="control-label"><?=t('Preview As')?></label>
	<div class="controls">
		<label class="radio inline"><input type="radio" value="guest" name="ccm-collection-preview-as" checked="checked" /> <?=t('Guest')?></label>
		<label class="radio inline"><input type="radio" value="registered" name="ccm-collection-preview-as" /> <?=t('Registered User')?>

		</label>
		
		&nbsp;&nbsp;
		<?=$us->quickSelect('customUser', $u->getUserName(), array('class' => 'span3', 'disabled' => 'disabled'))?>

	</div>
	</div>

	<div class="control-group">
	<?=$form->label('onDate_dt', t('On Date'))?>
	<div class="controls">
		<?=$date->datetime('onDate')?>
		<input type="submit" value="<?=t('Go')?>" class="btn" />
	</div>
	</div>		

</form>
</div>

<?
$assignments = $cp->getAllTimedAssignmentsForPage();
if (count($assignments) > 0) { ?>
	<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$c->getCollectionID()?>&ctask=view_timed_permission_list" dialog-width="500" dialog-height="300" dialog-title="<?=t('View Timed Permission Assignments')?>" class="dialog-launch" onclick="" id="ccm-list-view-customize-top"><span class="ccm-menu-icon ccm-icon-clock"></span><? 
		if (count($assignments) == 1) { ?><?=t('1 Timed Permission Found')?><? } else { ?><?=t('%s Timed Permissions Found', count($assignments))?><? } ?></a>
<? } ?>
</div>
<br/>
<iframe width="100%" height="200" style="border: 0px" border="0" frameborder="0" id="ccm-collection-preview-as-user-frame" name="ccm-collection-preview-as-user-frame" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_as_user?cID=<?=$c->getCollectionID()?>"></iframe>

</div>


<script type="text/javascript">
$(function() {
	$('input[name=ccm-collection-preview-as]').change(function() {
		if ($(this).val() == 'registered') { 
			$('input[name=customUser]').prop('disabled', false);
		} else { 
			$('input[name=customUser]').prop('disabled', true);
		}
	});
	
	var h = $('#ccm-collection-preview-as-user-form').closest('.ui-dialog-content').height();
	h = h - 120;
	$('#ccm-collection-preview-as-user-frame').css('height', h + 'px');
});
</script>