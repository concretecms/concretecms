<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <label class="control-label" for="<?= $key->getController()->getLabelID() ?>"><?=$label?>
        <?php

        if ($control->isRequired()) {
            print $renderer->getRequiredHtmlElement();
        }
        ?>
    </label>

    <?=$key->render(new \Concrete\Core\Attribute\Context\StandardFormContext(), $value)?>
</div>
