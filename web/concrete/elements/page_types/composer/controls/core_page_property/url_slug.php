<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group ccm-composer-url-slug" data-composer-field="url_slug" style="position: relative">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
    <i class="fa-refresh fa-spin fa ccm-composer-url-slug-loading"></i>
	<?=$form->text($this->field('url_slug'), $control->getPageTypeComposerControlDraftValue(), array('class' => 'span4'))?>
</div>

<style type="text/css">
    div.ccm-composer-url-slug {
        position: relative;
    }

    div.ccm-composer-url-slug i.ccm-composer-url-slug-loading {
        position: absolute; top: 35px; right: 10px; display: none;
    }
</style>