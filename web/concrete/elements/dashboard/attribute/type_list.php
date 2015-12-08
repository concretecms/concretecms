<?php

/**
 * @var $attributes \Concrete\Core\Attribute\AttributeKeyInterface[]
 */
?>

<?php $form = Core::make('helper/form'); ?>
<h4><?=t('Add Attribute Type')?></h4>

<?php if (isset($types) && is_array($types) && count($types) > 0) { ?>

    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <?=t('Choose')?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <? foreach($types as $type) {
                $controller = $type->getController();
                /**
                 * @var $formatter \Concrete\Core\Attribute\IconFormatterInterface
                 */
                $formatter = $controller->getIconFormatter(); ?>
                <li><a href="<?=$view->controller->getSelectTypeURL($type)?>"><?=$formatter->getListIconElement()?> <?=$type->getAttributeTypeDisplayName()?></a></li>
            <? } ?>
        </ul>
    </div>

<?php } ?>

