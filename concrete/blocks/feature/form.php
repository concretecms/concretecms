<?php
defined('C5_EXECUTE') or die("Access Denied.");
/** @var \Concrete\Block\Feature\Controller $controller */
/** @var \Concrete\Core\Form\Service\Form $form */
$bID = $bID ?? 0;
$icon = $icon ?? '';
$title = $title ?? '';
$titleFormat = $titleFormat ?? '';
$internalLinkCID = $internalLinkCID ?? 0;
$externalLink = $externalLink ?? '';
?>

<fieldset>
    <legend><?=t('Display')?></legend>
    <div class="form-group ccm-block-select-icon">
        <?php echo $form->label('icon', t('Icon'))?>
        <div id="ccm-icon-selector-<?= h($bID) ?>">
            <icon-selector name="icon" selected="<?= h($icon) ?>" title="<?= t('Choose Icon') ?>" empty-option-label="<?= h(tc('Icon', '** None Selected')) ?>" />
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("title", t('Title')); ?>
	    <div class="input-group">
		    <?php echo $form->text('title', $title); ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
		</div>
	</div>

    <div class="form-group">
        <?php echo $form->label('paragraph', t('Paragraph:'));?>
        <?php
            $editor = Core::make('editor');
            echo $editor->outputBlockEditModeEditor('paragraph', $controller->getParagraphEditMode());
        ?>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Link')?></legend>

    <div class="form-group">
        <select name="linkType" data-select="feature-link-type" class="form-select">
            <option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('None')?></option>
            <option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
            <option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
        </select>
    </div>

    <div data-select-contents="feature-link-type-internal" style="display: none;" class="form-group">
        <?=$form->label('internalLinkCID', t('Choose Page:'))?>
        <?= Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
    </div>

    <div data-select-contents="feature-link-type-external" style="display: none;" class="form-group">
        <?=$form->label('externalLink', t('URL'))?>
        <?= $form->text('externalLink', $externalLink); ?>
    </div>

</fieldset>

<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function(Vue, config) {
        new Vue({
            el: '#ccm-icon-selector-<?= h($bID) ?>',
            components: config.components
        })
    })
    $('select[data-select=feature-link-type]').on('change', function() {
       if ($(this).val() == '0') {
           $('div[data-select-contents=feature-link-type-internal]').hide();
           $('div[data-select-contents=feature-link-type-external]').hide();
       }
       if ($(this).val() == '1') {
           $('div[data-select-contents=feature-link-type-internal]').show();
           $('div[data-select-contents=feature-link-type-external]').hide();
       }
       if ($(this).val() == '2') {
           $('div[data-select-contents=feature-link-type-internal]').hide();
           $('div[data-select-contents=feature-link-type-external]').show();
       }
    }).trigger('change');
});
</script>

<style type="text/css">
    div.ccm-block-select-icon .input-group-addon {
        min-width:70px;
    }
    div.ccm-block-select-icon i {
        font-size: 22px;
    }
</style>
