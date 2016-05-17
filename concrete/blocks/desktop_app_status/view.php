<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

    <?php if (version_compare($latest_version, APP_VERSION, '>')) {
        ?>
    <div class="alert alert-info">
        <?=t('The latest version of concrete5 is <strong>%s</strong>. You are currently running concrete5 version <strong>%s</strong>.', $latest_version, APP_VERSION)?>
        <a class="pull-right btn btn-info btn-xs" href="<?=$view->url('/dashboard/system/backup/update')?>"><?=t('Update')?></a>
        </div>

    <?php
    } elseif (version_compare(APP_VERSION, Config::get('concrete.version'), '>')) {
        ?>
    <div class="alert alert-warning">

    <?=t('You have downloaded a new version of concrete5 but have not upgraded to it yet.');
        ?> <a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade" class="pull-right btn btn-warning btn-xs"><?=t('Update')?></a></div>
    <?php
    } ?>

    <?php if ($updates > 0) { ?>

        <div class="alert alert-info">
            <?=t2('There is currently %s add-on update available.', 'There are currently %s add-on updates available.', $updates)?>
            <a class="btn btn-info btn-xs pull-right" href="<?=$view->url('/dashboard/extend/update')?>"><?=t('Update')?></a></p>
        </div>

    <?php } ?>