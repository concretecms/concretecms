<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? if (is_object($composer)) { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($composer->getComposerName(), false, false, false)?>
	<form method="post" data-form="composer" class="form-horizontal" action="<?=$this->action('save', $composer->getComposerID())?>">
	<div class="ccm-pane-body">

	<div id="composer-save-status"></div>

	<? foreach($fieldsets as $cfl) { ?>
		<fieldset style="margin-bottom: 0px">
			<? if ($cfl->getComposerFormLayoutSetName()) { ?>
				<legend><?=$cfl->getComposerFormLayoutSetName()?></legend>
			<? } ?>
			<? $controls = ComposerFormLayoutSetControl::getList($cfl);

			foreach($controls as $con) { 
				if (is_object($draft)) { // we are loading content in
					$con->setComposerDraftObject($draft);
				}
				$cnp = new Permissions($con);
				if ($cnp->canAccessComposerFormLayoutSetControl()) { ?>
					<? $con->render(); ?>
				<? } ?>
				
			<? } ?>

		</fieldset>

	<? } ?>

	</div>
	<div class="ccm-pane-footer">
		<button type="submit" class="btn btn-primary pull-right"><?=t('Publish')?></button>
	</div>
	</form>

	<script type="text/javascript">
	ccm_saveComposerDraft = function(onComplete) {
		var $f = $('form[data-form=composer]'),
			formData = $f.serializeArray();
		
			/*
		$('#ccm-dashboard-composer-form').ajaxSubmit({
			'dataType': 'json',
			'success': function(r) {
				$('input[name=autosave]').val('0');
				ccm_composerLastSaveTime = new Date();
				$("#composer-save-status").html('<div class="alert alert-info"><?=t("Page saved at ")?>' + r.time + '</div>');
				$(".ccm-composer-hide-on-approved").show();
				$('#ccm-submit-save').attr('disabled',false);
				if (callback) {
					callback();
				}
			}

		$.ajax({
			dataType: 'json',
			type: 'post',
			data: formData,
			url: '<?=$this->action("save", $composer->getComposerID())?>',
			success: function(r) {

			}
		});
		*/

	}

	$(function() {

		setInterval(function() {
			ccm_saveComposerDraft()
		}, 10000);

	});
	</script>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>

	<? if (count($composers) > 0) { ?>
	<h3><?=t('What would you like to write?')?></h3>
	<ul class="item-select-list">
	<? foreach($composers as $cmp) { ?>
		<li class="item-select-page"><a href="<?=$this->url('/dashboard/composer/write', 'composer', $cmp->getComposerID())?>"><?=$cmp->getComposerName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<? } ?>