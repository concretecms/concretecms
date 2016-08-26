<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<form action="<?= $view->action('update_status', $user->getUserID()) ?>" method="post">
    <?= $token_validator->output() ?>
    <div class="btn-group">


        <?php if (Config::get('concrete.user.registration.validate_email') == true && $canActivateUser) {
            ?>
            <?php if ($user->isValidated() < 1) {
                ?>
                <button type="submit" name="task" value="validate"
                        class="btn btn-default"><?= t('Mark Email as Valid') ?></button>
                <?php
            }
            ?>
            <?php
        }
        ?>

        <?php
        if ($user->getAttribute('profile_private_messages_enabled')) {
            $u = new User();
            if ($u->getUserID() != $user->getUserID()) { ?>
                <a href="<?php echo View::url('/account/messages', 'write', $user->getUserID())?>" class="btn btn-default"><?php echo t("Send Private Message")?></a>
            <?php } ?>
        <?php } ?>

        <?php if ($canActivateUser) { ?>
            <?php if ($user->isActive()) { ?>
                <?php if (!in_array("deactivate", $workflowRequestActions)) { ?>
                    <button type="submit" name="task" value="deactivate"
                            class="btn btn-default"><?= t('Deactivate User') ?></button>
                <?php } ?>
            <?php } else { ?>
                <?php if ((!in_array("activate", $workflowRequestActions) && !in_array("register_activate", $workflowRequestActions))) { ?>
                    <button type="submit" name="task" value="activate"
                            class="btn btn-default"><?= t('Activate User') ?></button>
                <?php } ?>
            <?php } ?>
        <?php } ?>

        <?php if ($canSignInAsUser) {
            ?>
            <button type="submit" name="task" value="sudo"
                    class="btn btn-default"><?= t('Sign in As User') ?></button>
            <?php
        }
        ?>
        <?php if ($canDeleteUser) {
            ?>
            <button type="submit" name="task" value="delete" class="btn btn-danger"><?= t('Delete') ?></button>
            <?php
        }
        ?>
    </div>
</form>
