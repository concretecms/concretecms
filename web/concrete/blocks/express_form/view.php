<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-express-form">
    <div class="ccm-form">
        <a name="form<?=$bID?>"></a>

        <?php if (isset($success)) { ?>
            <div class="alert alert-success">
                <?=$success?>
            </div>
        <?php } ?>

        <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('submit')?>">
        <?php
        if (is_object($renderer) && is_object($expressForm)) {
            print $renderer->render($expressForm);
        }
        ?>

        <div class="form-actions">
            <button type="submit" name="Submit" class="btn btn-primary"><?=t($submitLabel)?></button>
        </div>

        </form>

    </div>
</div>