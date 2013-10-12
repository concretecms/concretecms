<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label col-lg-2"><?=$label?></label>
	<div class="col-lg-6" data-composer-field="url_slug">
		<?=$form->text($this->field('url_slug'), $control->getPageTypeComposerControlDraftValue(), array('class' => 'span4'))?>
<!--		<img src="<?=ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" id="ccm-url-slug-loader" style="display: none" />//-->
	</div>
</div>