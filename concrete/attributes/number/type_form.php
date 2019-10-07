<fieldset>
<legend><?php echo t('Number Options'); ?></legend>

    <div class="form-group">

        <?php echo $form->label('akNumberPlaceholder', t('Placeholder Number')); ?>

        <?php echo $form->text('akNumberPlaceholder', isset($akNumberPlaceholder) ? $akNumberPlaceholder : ''); ?>
    </div>

</fieldset>