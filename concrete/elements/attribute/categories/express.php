<?php
use Concrete\Core\Attribute\FilterableByValueInterface;

if (isset($key) && is_object($key)) {
    $eakUnique = $key->isAttributeKeyUnique();
}

$checkboxAttributes = [];
$controller = $type->getController();
if (!($controller instanceof FilterableByValueInterface)) {
    $checkboxAttributes = ['disabled' => 'disabled'];
}
?>
<?php $form = Loader::helper('form'); ?>
<fieldset>
    <legend><?=t('Express Options')?></legend>
    <div class="form-group">
        <div class="form-check">
            <?=$form->checkbox('eakUnique', 1, !empty($eakUnique), $checkboxAttributes)?>
            <?=$form->label('eakUnique',t('This attribute contains unique-only values.'), ['class'=>'form-check-label']);?>
        </div>
        <div class="help-block"><?=t('Checking this will disallow multiple objects with the same values to be created or updated. Useful for SKU-type data.')?></div>
        <?php
        if (!($controller instanceof FilterableByValueInterface)) { ?>
            <div class="alert alert-warning"><?=t('This attribute type does not support unique-only values.')?></div>
        <?php } ?>
    </div>

</fieldset>