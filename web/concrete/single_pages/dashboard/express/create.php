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


    <?php
        print $renderer->render($expressForm);
    ?>


<?php

} else { ?>
    <p><?=t('You have not created any forms for this data type.')?></p>
<?php } ?>
