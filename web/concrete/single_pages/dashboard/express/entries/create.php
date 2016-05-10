<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

if (is_object($expressForm)) {
    ?>

    <form method="post" action="<?=$view->action('submit', $entity->getId())?>">


    <?php
        echo $renderer->render($expressForm);
    ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php if ($backURL) { ?>
                    <a class="pull-left btn btn-default" href="<?=$backURL?>"><?=t('Back')?></a>
                <?php } ?>
                <button class="pull-right btn btn-primary" type="submit"><?=t('Add %s', $entity->getName())?></button>
            </div>
        </div>


<?php

} else {
    ?>
    <p><?=t('You have not created any forms for this data type.')?></p>
<?php 
} ?>
