<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
    <header><h3><?= t('Summary Templates') ?></h3></header>
    <form method="post" action="<?= $controller->action('submit') ?>" data-dialog-form="summary-templates"
          data-panel-detail-form="summary-templates">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-8 pl-0">
                    <p class="text-muted"><small><?=t('Review your summary templates to make sure they look good at different breakpoints.')?></small></p>
                </div>
                <div class="col-4 text-right">
                    <toggle-switch class="pr-2 mb-0" name="hasCustomSummaryTemplates"></toggle-switch>
                    <span class="text-muted"><small><?= t('Use only specific templates.') ?></small></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill border-bottom">
                        <li class="nav-item"><a class="nav-link active" href="#"><?=t('Extra Large')?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><?=t('Large')?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><?=t('Medium')?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><?=t('Small')?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><?=t('Extra Small')?></a></li>
                    </ul>
                </div>
            </div>

            <?php
            foreach ($templates as $instanceTemplate) {
                $template = $instanceTemplate->getTemplate();
                ?>

                <?= URL::to('/ccm/system/summary_template/render', $categoryHandle, $memberIdentifier, $instanceTemplate->getId())?>

                <br>
            <?php }

            ?>
        </div>




        <?php
        /*
        ?>


        <br>
        <br><br><br><br><br>


        <div class="form-group form-check">
            <label class="form-check-label">
                <input type="checkbox" <?php if ($object->hasCustomSummaryTemplates()) { ?>checked<?php } ?>
                       name="hasCustomSummaryTemplates" class="form-check-input">
            </label>
        </div>

        <hr>

        <h3 class="font-weight-light"><?=t("All Available Templates")?></h3>
        <p><?=t("Available summary templates are determined by the attributes and content.")?></p>

        <div class="form-group" data-list="summary-templates">
        <?php foreach($templates as $template) { ?>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox"
                           name="templateIDs[]" <?php if (!$object->hasCustomSummaryTemplates() || in_array($template->getID(), $selectedTemplateIDs)) { ?>checked<?php } ?> value="<?=$template->getID()?>" class="form-check-input">
                    <?=$template->getName()?>
                </label>
            </div>
        <?php } ?>
        </div>

        */ ?>

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
