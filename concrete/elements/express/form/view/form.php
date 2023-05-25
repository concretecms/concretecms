<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php
foreach ($form->getFieldSets() as $fieldSet) { ?>

    <div class="card mb-5">
        <?php if ($fieldSet->getTitle()) { ?>
            <div class="card-header"><h5 class="display-5 mb-0 text-secondary"><?= h($fieldSet->getTitle()) ?></h5></div>
        <?php } ?>

        <div class="card-body">
            <div class="list-group">

                <?php

                foreach($fieldSet->getControls() as $setControl) {
                    $controlView = $setControl->getControlView($context);

                    if (is_object($controlView)) {
                        $renderer = $controlView->getControlRenderer();
                        print $renderer->render();
                    }
                }

                ?>
            </div>
        </div>
    </div>
<?php } ?>
