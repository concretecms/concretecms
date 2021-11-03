<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-page-user-directory">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-1"><?= $c->getCollectionName() ?></h1>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form method="get" action="<?= $view->action('search_members') ?>">
                    <div class="hstack gap-3">
                        <input class="form-control me-auto" name="keywords" value="<?= $keywords ?>" type="text"
                               placeholder="<?= t('Name or keywords') ?>">
                        <button type="submit" name="submit" class="btn btn-outline-primary"><i
                                    class="fas fa-search"></i></button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">

                <?php
                if ($total == 0) {
                    ?>

                    <p class="card-text lead text-secondary"><?= t('No users found.') ?></p>

                    <?php
                } else {
                    foreach ($users as $user) { ?>

                        <a href="<?=$user->getUserPublicProfileURL()?>" class="card mb-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ccm-page-user-directory-entry">
                                        <img class="ccm-page-user-directory-avatar" src="<?=$user->getUserAvatar()->getPath()?>">
                                        <div>
                                            <?=ucfirst($user->getUserName())?>
                                            <?php
                                            foreach ($attribs as $ak) { ?>
                                                <div class="text-secondary small"><?=$user->getAttribute($ak, 'displaySanitized', 'display'); ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                </div>
                             </div>
                        </a>
                    <?php
                    }
                } ?>

            </div>
        </div>
    </div>
</div>

