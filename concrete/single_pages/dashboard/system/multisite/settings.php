<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<form action="<?php echo $view->action('enable_multisite')?>" method="post">
    <?=$token->output('enable_multisite')?>
    <?php if ($service->isMultisiteEnabled()) { ?>
        <p><?=t('Multiple sites are enabled.')?></p>
        <?php
    } else { ?>

        <p><?=t('Multiple site hosting requires concrete5 version 9 or above.')?></p>

    <?php } ?>

</form>