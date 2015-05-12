<?php defined('C5_EXECUTE') or die("Access Denied.");

$fp = FilePermissions::getGlobal();
$tp = new TaskPermission();
?>

<fieldset>
    <legend><?=t('Icon')?></legend>
        <div class="form-group ccm-block-feature-select-icon" style="margin-right: 35px;">
            <?=$form->select('icon', $icons, $icon);?>
            <i data-preview="icon" <?php if ($icon) { ?>class="fa fa-<?=$icon?>"<?php } ?>></i>
        </div>
</fieldset>

<fieldset>
    <legend><?=t('Text')?></legend>

    <div class="form-group">
        <?=$form->label('title', t('Title'))?>
        <?php echo $form->text('title', $title); ?>
    </div>

    <div class="form-group">
       <?php echo $form->label('redactor-content', t('Paragraph:'));?>
        <textarea style="display: none" id="redactor-content" name="content"><?php echo $content; ?></textarea>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Link')?></legend>

    <div class="form-group">
        <select name="linkType" data-select="feature-link-type" class="form-control">
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

<script>
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
    $('#redactor-content').redactor({
            minHeight: 200,
            'concrete5': {
                filemanager: <?php echo $fp->canAccessFileManager()?>,
                sitemap: <?php echo $tp->canAccessSitemap()?>,
                lightbox: true
            }
        });
</script>

<script type="text/javascript">
$(function() {
    $('div.ccm-block-feature-select-icon').on('change', 'select', function() {
        $('i[data-preview="icon"]').removeClass();
        if($(this).val()) {
            $('i[data-preview="icon"]').addClass('fa fa-' + $(this).val());
        }
    });
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
    div.ccm-block-feature-select-icon {
        position: relative;
    }
    div.ccm-block-feature-select-icon i {
        position: absolute;
        right: -25px;
        top: 10px;
    }
</style>
