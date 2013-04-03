<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? if (is_object($composer)) { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($composer->getComposerName(), false, false, false)?>
	<form method="post" data-form="composer" class="form-horizontal">
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
		<button type="button" id="ccm-composer-btn-publish" class="btn btn-primary pull-right" style="margin-left: 10px"><?=t('Publish')?></button>
		<button type="button" id="ccm-composer-btn-save" class="btn pull-right" style="margin-left: 10px"><?=t('Save and Exit')?></button>
		<button type="button" id="ccm-composer-btn-exit" class="btn pull-right"><?=t('Back to Drafts')?></button>
		<button type="button" id="ccm-composer-btn-discard" class="btn btn-danger pull-left"><?=t('Discard Draft')?></button>
	</div>


	</form>

	<script type="text/javascript">
	var ccm_saveComposerDraftURL = '<?=$saveURL?>';
	var ccm_discardComposerDraftURL = '<?=$discardURL?>';

	ccm_saveComposerDraft = function(onComplete) {
		var $f = $('form[data-form=composer]'),
			formData = $f.serializeArray();

		$.ajax({
			dataType: 'json',
			type: 'post',
			data: formData,
			url: ccm_saveComposerDraftURL,
			success: function(r) {
				$("#composer-save-status").html('<div class="alert alert-info"><?=t("Page saved at ")?>' + r.time + '</div>');
				if (r.saveurl) {
					ccm_saveComposerDraftURL = r.saveurl;
				}
				if (r.discardurl) {
					ccm_discardComposerDraftURL = r.discardurl;
				}
			}
		});
	}

	$(function() {

		setInterval(function() {
			ccm_saveComposerDraft()
		}, 10000);

		$('#ccm-composer-btn-exit').on('click', function() {
			window.location.href = "<?=$this->url('/dashboard/composer/drafts')?>";
		});

		$('#ccm-composer-btn-discard').on('click', function() {
			window.location.href = ccm_discardComposerDraftURL;
		});

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