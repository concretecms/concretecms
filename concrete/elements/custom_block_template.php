<?php if ($style instanceof \Concrete\Core\Block\CustomStyle) { ?>

<form method="post" action="<?=$saveAction?>" id="ccm-inline-design-form">
    <ul class="ccm-inline-toolbar ccm-ui">
        <li>
            <div class="ccm-inline-select-container">
                <select id="bFilename" name="bFilename" class="form-control" style="border: none; height: 27px; padding: 2px 28px 2px 2px; background-position: right 5px top 5px;">
                    <option value="">(<?=t('None selected')?>)</option>
                    <?php
                    foreach ($templates as $tpl) { ?>
                        <option value="<?=$tpl->getTemplateFileFilename()?>" <?php if ($bFilename == $tpl->getTemplateFileFilename()) { ?> selected <?php } ?>><?=$tpl->getTemplateFileDisplayName()?></option>
                    <?php } ?>
                </select>
            </div>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
            <button data-action="cancel-design" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <button data-action="save-design" class="btn btn-primary" type="button"><?=t('Save')?></button>
        </li>
    </ul>
</form>
<script>
  $('#ccm-inline-design-form').concreteBlockInlineStyleCustomizer();
</script>
<?php } ?>

