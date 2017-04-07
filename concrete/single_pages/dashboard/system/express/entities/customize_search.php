<?php defined('C5_EXECUTE') or die("Access Denied.");?>

    <div class="ccm-dashboard-header-buttons">

        <?php
        $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
        $manage->render();
        ?>

    </div>

<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <form method="post" action="<?=$view->action('save', $entity->getID())?>">
            <?=$token->output('save')?>

            <fieldset>
                <legend><?=t('Attribute to use as Name')?></legend>
                <select class="form-control" name="displayAttributeKey">
                    <option value="0"><?= t('Select One') ?></option>
                    <?php
                    /** @var \Concrete\Core\Entity\Express\Entity $entity */
                    $attributes = $entity->getAttributes();
                    $selected = (int) $entity->getDisplayAttributeKey();

                    /** @var \Concrete\Core\Entity\Attribute\Key\ExpressKey $attribute */
                    foreach ($attributes as $attribute) {
                        $key = $attribute->getAttributeKeyID();
                        ?>
                        <option value="<?= $key ?>" <?= $key === $selected ? 'selected' : '' ?>>
                            <?= $attribute->getAttributeKeyDisplayName() ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </fieldset>

        <?php
        print $customizeElement->render();
        ?>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
                </div>
            </div>

        </form>
    </div>
</div>
