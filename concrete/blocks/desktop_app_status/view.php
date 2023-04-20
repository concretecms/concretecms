<?php

use Concrete\Core\Production\Modes;

defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Core\Block\View\BlockView $view */
/** @var string $current_version the currently installed Concrete version */
/** @var string $latest_version the last available Concrete version */
/** @var int $updates number of packages with new versions available */

if (version_compare($latest_version, $current_version, '>')) {
    ?>
    <div class="alert alert-info clearfix">
        <?php
        echo t(
            'The latest version of Concrete is <strong>%s</strong>. You are currently running Concrete version <strong>%s</strong>.',
            $latest_version,
            $current_version
        ) ?>
        <a class="btn btn-info btn-sm float-end" href="<?php
        echo $view->url('/dashboard/system/update/update') ?>"><?php
            echo t('Update') ?></a>
    </div>
    <?php
}

if ($updates > 0) {
    ?>
    <div class="alert alert-info clearfix">
        <?php
        echo t2(
            'There is currently %d add-on update available.',
            'There are currently %d add-on updates available.',
            $updates
        ) ?>
        <a class="btn btn-info btn-sm float-end" href="<?php
        echo $view->url('/dashboard/extend/update') ?>"><?php
            echo t('Update') ?></a>
    </div>
    <?php
}

if (isset($productionMode)) {
    if ($productionMode === Modes::MODE_DEVELOPMENT) { ?>

        <div class="alert alert-secondary d-flex align-items-center">
            <div><?= t(
                    'Your site is in development mode. Only use this mode if you are working in a protected or local development environment.'
                ) ?></div>
            <a class="btn btn-info btn-sm ms-auto" href="<?php
            echo $view->url('/dashboard/system/basics/production_mode') ?>"><?php
                echo t('Change') ?></a>
        </div>

        <?php
    } else {
        if ($productionMode === Modes::MODE_STAGING) { ?>

            <div class="alert alert-warning d-flex align-items-center">
                <div><?= t(
                        'Your site is in staging mode. Only use this mode if your site is a copy of a live site but is not actively being visited.'
                    ) ?></div>
                <a class="btn btn-warning btn-sm ms-auto" href="<?php
                echo $view->url('/dashboard/system/basics/production_mode') ?>"><?php
                    echo t('Change') ?></a>
            </div>

            <?php
        }
    }
} ?>
