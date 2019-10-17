<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Core\Block\View\BlockView $view */
/** @var string $current_version the currently installed concrete5 version */
/** @var string $latest_version the last available concrete5 version */
/** @var int $updates number of packages with new versions available */

if (version_compare($latest_version, $current_version, '>')) {
    ?>
    <div class="alert alert-info">
        <?php echo t('The latest version of concrete5 is <strong>%s</strong>. You are currently running concrete5 version <strong>%s</strong>.', $latest_version, $current_version) ?>
        <a class="pull-right btn btn-info btn-xs" href="<?php echo $view->url('/dashboard/system/update/update')?>"><?php echo t('Update')?></a>
    </div>
    <?php
}

if ($updates > 0) {
    ?>
    <div class="alert alert-info clearfix">
        <?php echo t2('There is currently %d add-on update available.', 'There are currently %d add-on updates available.', $updates) ?>
        <a class="btn btn-info btn-xs pull-right" href="<?php echo $view->url('/dashboard/extend/update') ?>"><?php echo t('Update') ?></a>
    </div>
    <?php
}
