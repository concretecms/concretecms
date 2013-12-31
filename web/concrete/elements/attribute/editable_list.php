<? foreach($attributes as $ak) { ?>

	<div class="row">
		<div class="col-md-3"><p><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></p></div>
		<div class="col-md-9"><p data-title="<?=$ak->getAttributeKeyName()?>" data-key-id="<?=$ak->getAttributeKeyID()?>" data-name="<?=$ak->getAttributeKeyID()?>" data-editable-field-type="xeditableAttribute" data-url="<?=$action?>" data-type="concreteattribute"><?=$object->getAttribute($ak->getAttributeKeyHandle(), 'displaySanitized', 'display')?></p></div>
	</div>

	<div style="display: none">
	<div data-editable-attribute-key-id="<?=$ak->getAttributeKeyID()?>">
		<?
		$value = $object->getAttributeValueObject($ak);
		$ak->render('form', $value);
		?>
	</div>
	</div>

<? } ?>