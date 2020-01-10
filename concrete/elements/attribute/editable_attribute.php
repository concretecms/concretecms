<?php

use Concrete\Core\Attribute\CustomNoValueTextAttributeInterface;
use Concrete\Core\Attribute\XEditableConfigurableAttributeInterface;

/**
 * @var Concrete\Core\Entity\Attribute\Key\Key $ak
 * @var Traversable|array|null $objects
 * @var object|null $object
 * @var callback $permissionsCallback
 * @var array $permissionsArguments
 * @var string $clearAction
 * @var string $saveAction
 * @var string $display
 */
$xeditableOptions = null;
$display = null;
$noValueDisplayHtml = '';
if (isset($objects)) {
    $previousDisplay = null;
    foreach ($objects as $object) {
        $value = $object->getAttributeValueObject($ak);
        if (is_object($value)) {
            $display = (string) $value->getDisplayValue();
        }
        if ($previousDisplay !== null && $display !== $previousDisplay) {
            $display = t('Multiple Values');
            break;
        }
        $previousDisplay = $display;
    }
} else {
    if (method_exists($ak, 'getController')) {
        $attributeController = $ak->getController();
        if ($attributeController instanceof CustomNoValueTextAttributeInterface) {
            $noValueDisplayHtml = (string) $attributeController->getNoneTextDisplayValue();
        }
        if ($attributeController instanceof XEditableConfigurableAttributeInterface) {
            $xeditableOptions = $attributeController->getXEditableOptions();
        }
    }
    $value = $object->getAttributeValueObject($ak);
    if (is_object($value)) {
        $display = (string) $value->getDisplayValue();
    }
}

$canEdit = $permissionsCallback($ak, isset($permissionsArguments) ? $permissionsArguments : null);
?>
<div class="row">
    <div class="editable-attribute-wrapper">
        <div class="col-md-3">
            <p class="editable-attribute-display-name"><?= $ak->getAttributeKeyDisplayName() ?></p>
        </div>
        <div class="col-md-9"<?php if ($canEdit) { ?> data-editable-field-inline-commands="true"<?php } ?>>
            <div class="editable-attribute-field-inline">
                <?php if ($canEdit) { ?>
                    <ul class="ccm-edit-mode-inline-commands">
                        <li>
                            <a href="#" data-key-id="<?= $ak->getAttributeKeyID() ?>" data-url="<?= $clearAction ?>" data-editable-field-command="clear_attribute">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
                <span
                    <?php
                    if ($canEdit) {
                        ?>
                        data-title="<?= $ak->getAttributeKeyDisplayName() ?>"
                        data-key-id="<?= $ak->getAttributeKeyID() ?>"
                        data-name="<?= $ak->getAttributeKeyID() ?>"
                        data-editable-field-type="xeditableAttribute"
                        data-url="<?= $saveAction ?>"
                        data-type="concreteattribute"
                        data-placement="bottom"
                        <?php
                        if ($xeditableOptions !== null) {
                            foreach ($xeditableOptions as $xeditableOptionName => $xeditableOptionValue) {
                                echo ' ', h("data-{$xeditableOptionName}"), '="', h($xeditableOptionValue), '"';
                            }
                        } elseif ($ak->getAttributeTypeHandle() === 'textarea') {
                            echo ' data-editableMode="inline" ';
                        }
                        if ($noValueDisplayHtml !== '') {
                            echo ' data-no-value-html="' . h($noValueDisplayHtml) . '" ';
                        }
                    }
                    ?>
                    ><?= $canEdit || $display !== null ? $display : $noValueDisplayHtml ?></span>
            </div>
        </div>
    </div>
</div>
<?php
if ($canEdit) {
    ?>
    <div style="display: none">
        <div data-editable-attribute-key-id="<?= $ak->getAttributeKeyID() ?>">
            <?php
            $value = $object->getAttributeValueObject($ak);
            $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), $value);
            ?>
        </div>
    </div>
    <?php
}
