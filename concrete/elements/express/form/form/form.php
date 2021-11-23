<?php

use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var Concrete\Core\Express\Form\Context\FrontendFormContext $context
 * @var Concrete\Core\Entity\Express\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 */

$page = Page::getCurrentPage();
if (!$page || $page->isError() || !$page->isEditMode()) {
    $token->output('express_form');
}
?>
<input type="hidden" name="express_form_id" value="<?=$form->getID()?>">
<div class="ccm-dashboard-express-form">
    <?php
    foreach ($form->getFieldSets() as $fieldSet) { ?>

        <fieldset>
            <?php if ($fieldSet->getTitle()) { ?>
                <legend><?= h(t($fieldSet->getTitle())) ?></legend>
            <?php } ?>

            <?php

            foreach($fieldSet->getControls() as $setControl) {
                $controlView = $setControl->getControlView($context);

                if (is_object($controlView)) {
                    $renderer = $controlView->getControlRenderer();
                    print $renderer->render();
                }
            }

            ?>
        </fieldset>
    <?php } ?>
</div>
