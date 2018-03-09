<fieldset>
    <legend><?php echo t('Text Options'); ?></legend>

    <div class="form-group" data-group="single-value">
        <label><?=t("Required"); ?></label>
        <div class="checkbox">
            <label>
                <?=$form->checkbox('akIsRequired', 1, $akIsRequired); ?> <span><?=t('Ensure a value is inputted.'); ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">

        <?php echo $form->label('akTextPlaceholder', t('Placeholder Text')); ?>

        <?php echo $form->text('akTextPlaceholder', $akTextPlaceholder);?>
    </div>

</fieldset>