<fieldset>
    <legend><?php echo t('Telephone Options'); ?></legend>

    <div class="form-group">

        <?php echo $form->label('akTelephonePlaceholder', t('Placeholder Telephone')); ?>

        <?php echo $form->text('akTelephonePlaceholder', isset($akTelephonePlaceholder) ? $akTelephonePlaceholder : ''); ?>
    </div>

</fieldset>