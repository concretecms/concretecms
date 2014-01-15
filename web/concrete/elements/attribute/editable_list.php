<? foreach($attributes as $ak) { 

	if (isset($objects)) {
		foreach($objects as $object) {
			$display = $object->getAttribute($ak->getAttributeKeyHandle(), 'displaySanitized', 'display');
			if (isset($lastDisplay) && $display != $lastDisplay) {
				$display = t('Multiple Values');
			}
			$lastDisplay = $display;
		}
	} else {
		$display = $object->getAttribute($ak->getAttributeKeyHandle(), 'displaySanitized', 'display');
	}

	$canEdit = $permissionsCallback($ak, $permissionsArguments); ?>

	<div class="row">
		<div class="col-md-3"><p><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></p></div>
		<div class="col-md-9" <? if ($canEdit) { ?>data-editable-field-inline-commands="true"<? } ?>>
		<? if ($canEdit) { ?>
		<ul class="ccm-edit-mode-inline-commands">
			<li><a href="#" data-key-id="<?=$ak->getAttributeKeyID()?>" data-url="<?=$clearAction?>" data-editable-field-command="clear_attribute"><i class="glyphicon glyphicon-trash"></i></a></li>
		</ul>
		<? } ?><p><span <? if ($canEdit) { ?>data-title="<?=$ak->getAttributeKeyName()?>" data-key-id="<?=$ak->getAttributeKeyID()?>" data-name="<?=$ak->getAttributeKeyID()?>" data-editable-field-type="xeditableAttribute" data-url="<?=$saveAction?>" data-type="concreteattribute"<? } ?>><?=$display?></span></p>
	</div>
	</div>

	<? if ($canEdit) { ?>

		<div style="display: none">
		<div data-editable-attribute-key-id="<?=$ak->getAttributeKeyID()?>">
			<?
			$value = $object->getAttributeValueObject($ak);
			$ak->render('form', $value);
			?>
		</div>
		</div>

	<? } ?>



	<? unset($lastDisplay); ?>

<? } ?>