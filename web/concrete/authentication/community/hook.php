<div class="form-group">
        <span>
            <?= t('Attach a community account') ?>
        </span>
    <hr>
</div>
<div class="form-group">
    <?php
    if ($attached) {
        ?>
        <div class="btn-group">
            <a href="<?= \URL::to('/system/authentication/community/attempt_attach'); ?>"
               class="btn btn-primary btn-community" target="_blank">
                <img src="<?= BASE_URL . DIR_REL ?>/concrete/images/logo.png" class="concrete5-icon"></i>
                <?= t('Attach a concrete5.org account') ?>
            </a>
            <a href="<?= \URL::to(
                '/login/callback/community/handle_detach',
                id(new \Concrete\Core\Validation\CSRF\Token)->generate('oauth_detach')); ?>"
               class="btn btn-danger" target="_blank">
                <?= t('Detach') ?>
            </a>
        </div>
    <?php
    } else {
        ?>
        <a href="<?= \URL::to('/system/authentication/community/attempt_attach'); ?>"
           class="btn btn-primary btn-community" target="_blank">
            <img src="<?= BASE_URL . DIR_REL ?>/concrete/images/logo.png" class="concrete5-icon"></i>
            <?= t('Attach a concrete5.org account') ?>
        </a>
    <?php
    }
    ?>
</div>

<style>
    .ccm-ui .btn-community {
        border-color: transparent;
        background: rgb(31, 186, 232);
    }

    .ccm-ui .btn-community:hover {
        background: rgb(28, 163, 205);
        border-color: transparent;
    }

    img.concrete5-icon {
        height: 18px;
        margin-right: 5px;
    }
</style>
