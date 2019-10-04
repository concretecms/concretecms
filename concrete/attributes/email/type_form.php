<fieldset>
<legend><?php echo t('Email Options'); ?></legend>

    <div class="form-group">

        <?php echo $form->label('akEmailPlaceholder', t('Placeholder Email')); ?>

        <?php echo $form->text('akEmailPlaceholder', isset($akEmailPlaceholder) ? $akEmailPlaceholder : ''); ?>
    </div>

</fieldset>