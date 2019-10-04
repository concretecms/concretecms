<fieldset>
    <legend><?php echo t('Url Options'); ?></legend>

    <div class="form-group">

        <?php echo $form->label('akUrlPlaceholder', t('Placeholder URL')); ?>

        <?php echo $form->text('akUrlPlaceholder', isset($akUrlPlaceholder) ? $akUrlPlaceholder : ''); ?>
    </div>

</fieldset>