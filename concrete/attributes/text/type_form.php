<fieldset>
    <legend><?php echo t('Text Options')?></legend>

    <div class="form-group">

        <?php echo $form->label( 'akTextPlaceholder', t('Placeholder Text'), ['class' => 'form-label'])?>

        <?php echo $form->text( 'akTextPlaceholder' , isset($akTextPlaceholder) ? $akTextPlaceholder : '')?>
    </div>

</fieldset>
