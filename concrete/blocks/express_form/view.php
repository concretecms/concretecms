<?php defined('C5_EXECUTE') or die("Access Denied.");

    $renderer = Core::make('Concrete\Core\Express\Form\StandardFormRenderer', ['form' => $expressForm]);

?>
<div class="ccm-block-express-form">
    <div class="ccm-form">
        <a name="form<?=$bID?>"></a>

        <?php if (isset($success)) { ?>
            <div class="alert alert-success">
                <?=$success?>
            </div>
        <?php } ?>

        <?php if (isset($error) && is_object($error)) { ?>
            <div class="alert alert-danger">
                <?=$error->output()?>
            </div>
        <?php } ?>


        <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('submit')?>#form<?=$bID?>">
        <?php
        if (is_object($renderer)) {
            $renderer->setRequiredHtmlElement('<span class="text-muted small">' . t('Required') . '</span>');
            print $renderer->render();
        }

        if ($displayCaptcha) {
            $captcha = \Core::make('helper/validation/captcha');
            ?>
            <div class="form-group captcha">
                <?php
                $captchaLabel = $captcha->label();
                if (!empty($captchaLabel)) {
                    ?>
                    <label class="control-label"><?php echo $captchaLabel;
                        ?></label>
                    <?php

                }
                ?>
                <div><?php  $captcha->display(); ?></div>
                <div><?php  $captcha->showInput(); ?></div>
            </div>
        <?php } ?>

        <div class="form-actions">
            <button type="submit" name="Submit" class="btn btn-primary"><?=t($submitLabel)?></button>
        </div>

        </form>

    </div>
</div>