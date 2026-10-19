<?php
defined('C5_EXECUTE') or die('Access Denied.');

// @var \Concrete\Core\Form\Service\Widget\DateTime $dt
// @var \Concrete\Core\Validation\CSRF\Token $token
// @var \Concrete\Core\Form\Service\Widget\UserSelector $userSelector
?>

<form role="form" method="post" action="<?= $controller->action('csv_export') ?>">
    <?php
    $token->output('export_page_changes');
    ?>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <label for="startDate" class="control-label form-label">
                    <?= tc('Start date', 'From') ?>
                </label>
                <div>
                    <?= $dt->datetime('startDate', null, true);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <label for="endDate" class="control-label form-label">
                    <?= tc('End date', 'To') ?>
                </label>
                <div>
                    <?= $dt->datetime('endDate', null, true);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <label class="control-label form-label">
                    <?= t('Author') ?>
                </label>
                <div>
                    <?= $userSelector->selectUser('versionAuthorID') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-search-fields-submit clearfix">
        <button type="submit" class="btn btn-primary float-end">
            <?= t('Export to CSV') ?>
        </button>
    </div>
</form>
