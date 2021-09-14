<fieldset>
    <legend><?= t('Checkbox Options') ?></legend>

    <div class="form-group">
        <label><?= t("Default Value") ?></label>
        <div class="form-check">
            <?= $form->checkbox('akCheckedByDefault', 1, $akCheckedByDefault ?? false) ?>
            <label class="form-check-label" for="akCheckedByDefault"><?= t('The checkbox will be checked by default.') ?></label>
        </div>
    </div>

    <div class="form-group">
        <label><?= t("Label") ?></label>
        <?= $form->text('akCheckboxLabel', $akCheckboxLabel ?? '') ?>
        <p class="help-block"><?=t('This will be displayed next to the checkbox. If it is blank, the name of the attribute will be displayed.')?></p>
    </div>

</fieldset>