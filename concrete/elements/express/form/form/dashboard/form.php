<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<input type="hidden" name="express_form_id" value="<?=$form->getID()?>">
<?=$token->output('express_form')?>

<div class="ccm-dashboard-express-form">
    <?php
    foreach ($form->getFieldSets() as $fieldSet) { ?>

        <fieldset>
            <?php if ($fieldSet->getTitle()) { ?>
                <legend><?= $fieldSet->getTitle() ?></legend>
            <?php } ?>

            <?php

            foreach($fieldSet->getControls() as $setControl) {
                $controlView = $setControl->getControlView(
                    new \Concrete\Core\Express\Form\Context\DashboardFormContext()
                );

                if (is_object($controlView)) {
                    $renderer = $controlView->getControlRenderer();
                    print $renderer->render();
                }
            }

            ?>
        </fieldset>
    <?php } ?>
</div>
