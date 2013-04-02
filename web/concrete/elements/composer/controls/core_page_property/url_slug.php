<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="url_slug">
		<?=$form->text($this->field('url_slug'))?>
		<img src="<?=ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" id="ccm-url-slug-loader" style="display: none" />
	</div>
</div>

