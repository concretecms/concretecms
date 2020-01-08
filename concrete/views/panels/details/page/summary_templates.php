<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
    <header><?= t('Summary Templates') ?></header>
    <form method="post" action="<?= $controller->action('submit') ?>" data-dialog-form="summary-templates"
          data-panel-detail-form="summary-templates">

        <div class="form-group form-check">
            <label class="form-check-label">
                <input type="checkbox" <?php if ($c->hasCustomSummaryTemplates()) { ?>checked<?php } ?>
                       name="hasCustomSummaryTemplates" class="form-check-input">
                <?= t('Enable custom summary templates.') ?>
            </label>
        </div>

        <hr>

        <h3 class="font-weight-light"><?=t("All Available Templates")?></h3>
        <p><?=t("Available summary templates are determined by the attributes and content assigned to this page.")?></p>

        <div class="form-group" data-list="summary-templates">
        <?php foreach($templates as $template) { ?>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox"
                           name="templateIDs[]" <?php if (!$c->hasCustomSummaryTemplates() || in_array($template->getID(), $selectedTemplateIDs)) { ?>checked<?php } ?> value="<?=$template->getID()?>" class="form-check-input">
                    <?=$template->getName()?>
                </label>
            </div>
        <?php } ?>
        </div>
        
    </form>
    <div class="ccm-panel-detail-form-actions dialog-buttons">
        <button class="float-left btn btn-secondary" type="button" data-dialog-action="cancel"
                data-panel-detail-action="cancel"><?= t('Cancel') ?></button>
        <button class="float-right btn btn-success" type="button" data-dialog-action="submit"
                data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
    </div>

</section>

<script type="text/javascript">
    $(function() {
        $('input[name=hasCustomSummaryTemplates]').on('change', function() {
            if ($(this).is(':checked')) {
                $('div[data-list=summary-templates] input[type=checkbox]').prop('disabled', false);
            } else {
                $('div[data-list=summary-templates] input[type=checkbox]').prop('disabled', true).prop('checked', true);
            }
        }).trigger('change');
    });
</script>
