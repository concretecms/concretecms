<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<div data-composer-field="name">
		<?=$form->text($this->field('name'), $control->getPageTypeComposerControlDraftValue())?>
	</div>
</div>

<script type="text/javascript">
var ccm_composerAddPageTimer = false;
$(function() {
	$('div[data-composer-field=name] input').on('keyup', function() {
		var val = $(this).val();
		var frm = $(this);
		clearTimeout(ccm_composerAddPageTimer);
		ccm_composerAddPageTimer = setTimeout(function() {
			$('#ccm-url-slug-loader').show();
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug', {
				'token': '<?=Loader::helper('validation/token')->generate('get_url_slug')?>',
				'name': val
			}, function(r) {
				$('#ccm-url-slug-loader').hide();
				$('div[data-composer-field=url_slug] input').val(r);
			});
		}, 150);
	});
});
</script>