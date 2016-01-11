<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>
</div>

<?php

if (is_object($expressForm)) {
    ?>

    <form method="post" action="<?=$view->action('submit', $entity->getId())?>">


    <?php
        echo $renderer->render($expressForm);
    ?>

        <div class="form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?=t('Add %s', $entity->getName())?></button>
        </div>


<?php

} else {
    ?>
    <p><?=t('You have not created any forms for this data type.')?></p>
<?php 
} ?>
