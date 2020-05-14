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
                <div class="col-4 text-right" vue-enabled>
                    <?php
                    $checked = "false";
                    if ($object->hasCustomSummaryTemplates()) {
                        $checked = "true";
                    }
                    ?>
                    <toggle-switch class="pr-2 mb-0" :value="<?=$checked?>" name="hasCustomSummaryTemplates"></toggle-switch>
                    <span class="text-muted"><small><?= t('Use only specific templates.') ?></small></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 pl-0">
                    <ul class="nav nav-tabs nav-fill border-bottom mb-3" data-nav="summary-template-form-factors">
                        <li class="nav-item"><a data-width="1140" class="nav-link active" href="#"><?=t('Extra Large')?></a></li>
                        <li class="nav-item"><a data-width="992" class="nav-link" href="#"><?=t('Large')?></a></li>
                        <li class="nav-item"><a data-width="768" class="nav-link" href="#"><?=t('Medium')?></a></li>
                        <li class="nav-item"><a data-width="576" class="nav-link" href="#"><?=t('Small')?></a></li>
                        <li class="nav-item"><a data-width="480" class="nav-link" href="#"><?=t('Extra Small')?></a></li>
                    </ul>
                </div>
            </div>

            <div data-list="summary-templates">
                <?php
                foreach ($templates as $instanceTemplate) {
                    $template = $instanceTemplate->getTemplate();
                    $checked = "false";
                    if (!$object->hasCustomSummaryTemplates() || in_array($template->getID(), $selectedTemplateIDs)) {
                        $checked = "true";
                    }

                    ?>
                    <div class="row">
                        <div class="col-6 pl-0">
                            <p class="text-muted"><?=$template->getName()?></p>
                        </div>
                        <div class="col-6 text-right" data-wrapper="toggle-switch" vue-enabled>
                            <toggle-switch name="template_<?=$template->getID()?>" :value="<?=$checked?>"></toggle-switch>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 pl-0 text-center">

                            <div data-text="loading" class="text-left"><?=t('Loading...')?></div>

                            <iframe width="1140" class="ccm-summary-templates-preview" src="<?=
                            URL::to('/ccm/system/summary_template/render',
                                $categoryHandle,
                                $memberIdentifier,
                                $instanceTemplate->getId()
                            )?>"></iframe>

                            <hr>




                        </div>
                    </div>
                <?php } ?>
            </div>
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
                $('div[data-list=summary-templates] [data-wrapper=toggle-switch]').show();
            } else {
                $('div[data-list=summary-templates] [data-wrapper=toggle-switch]').hide();
            }
        }).trigger('change');

        $('[data-list=summary-templates] iframe').on('load', function() {
            var offsetHeight = this.contentWindow.document.body.offsetHeight,
                frameHeight = offsetHeight > 480 ? 480 : offsetHeight;

            $(this).css('width', $('[data-nav=summary-template-form-factors] a.active').attr('data-width'));
            $(this).css('height', frameHeight);

            $(this).parent().find('div[data-text=loading]').remove();
        });
        $('[data-nav=summary-template-form-factors]').on('click', 'a', function() {
            $(this).closest('ul').find('a').removeClass('active');
            $(this).addClass('active');
            $('[data-list=summary-templates] iframe').css('width', $(this).attr('data-width'));
        });
    });
</script>
