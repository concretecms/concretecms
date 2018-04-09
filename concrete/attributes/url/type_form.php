<fieldset class="ccm-attribute ccm-attribute-date-time">

    <legend><?=t('URL Options'); ?></legend>

    <div class="form-group" data-group="single-value">
        <label><?=t("Required"); ?></label>
        <div class="checkbox">
            <label>
                <?=$form->checkbox('akIsRequired', 1, $akIsRequired); ?> <span><?=t('Ensure a value is selected.');?></span>
            </label>
        </div>
    </div>

</fieldset>
