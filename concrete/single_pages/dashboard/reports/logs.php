<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string|null $settingsPage
 */

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();
$valt = $app->make('helper/validation/token');
$th = $app->make('helper/text');
if ($isReportEnabled) {
    ?>

<div class="ccm-dashboard-header-buttons">
    <?php
    if ($settingsPage !== null) {
        ?>
        <a href="<?= h($settingsPage) ?>" class="btn btn-default"><?= t('Settings') ?></a>
        <?php
    }
    if (!isset($selectedChannel)) {
        ?>
        <a href="javascript:void(0)" class="btn btn-danger" onclick="clearAllChannelLogs()" ><?=t('Delete all'); ?></a>
        <script>
            clearAllChannelLogs = function() {
                ConcreteAlert.confirm(
                    <?= json_encode(t('Are you sure you want to clear all channel logs?')); ?>,
                    function() {
                        location.href = "<?= $controller->action('clear', $valt->generate()); ?>";
                    },
                    'btn-danger',
                    <?= json_encode(t('Delete')); ?>
                );
            };
        </script>
    <?php
    } ?>
    <a id="ccm-export-results" class="btn btn-success" href="<?= $view->action('csv', $valt->generate()); ?>?<?=$query; ?>">
        <i class='fa fa-download'></i> <?= t('Export to CSV'); ?>
    </a>
</div>

<form role="form" action="<?=$controller->action('view'); ?>" class="ccm-search-fields">
    <div class="form-group">
        <?=$form->label('keywords', t('Search')); ?>
        <div class="ccm-search-field-content">
            <div class="ccm-search-main-lookup-field">
                <i class="fa fa-search"></i>
                <?=$form->search('keywords', ['placeholder' => t('Keywords')]); ?>
                <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search'); ?></button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <?=$form->label('channel', t('Channel')); ?>
                <div class="ccm-search-field-content">
                    <?=$form->select('channel', $channels); ?>
                    <?php if (isset($selectedChannel)) {
        ?>
                        <a href="javascript:void(0)" class="btn btn-default btn-danger pull-right" onclick="clearSelectedChannelLogs()" style="margin-top: 30px;"><?=tc('%s is a channel', 'Clear all in %s', \Concrete\Core\Logging\Channels::getChannelDisplayName($selectedChannel)); ?></a>
                        <script>
                            clearSelectedChannelLogs = function() {
                                ConcreteAlert.confirm(
                                    <?= json_encode(t('Are you sure you want to clear the %s channel logs?', \Concrete\Core\Logging\Channels::getChannelDisplayName($selectedChannel))); ?>,
                                    function() {
                                        location.href = "<?= $controller->action('clear', $valt->generate(), $selectedChannel); ?>";
                                    },
                                    'btn-danger',
                                    <?= json_encode(t('Delete')); ?>
                                );
                            };
                        </script>
                    <?php
    } ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?=$form->label('date_from', t('Date From')); ?>
            <div class="ccm-search-field-content">
                <?= $wdt->date('date_from', $date_from); ?>
            </div>
        </div>
        <div class="col-md-4">
            <?=$form->label('date_to', t('Date To')); ?>
            <div class="ccm-search-field-content">
                <?= $wdt->date('date_to', $date_to); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?=$form->label('level', t('Level')); ?>
        <div class="ccm-search-field-content">
            <?=$form->selectMultiple('level', $levels, array_keys($levels)); ?>
        </div>
    </div>

    <div class="ccm-search-fields-submit">
        <button type="submit" class="btn btn-primary pull-right"><?=t('Search'); ?></button>
    </div>
</form>

<div class="ccm-dashboard-content-full">
    <div class="table-responsive">
        <table class="ccm-search-results-table selectable">
            <thead>
                <tr>
                    <th class="<?=$list->getSearchResultsClass('logID'); ?>"><a href="<?=$list->getSortByURL('logID', 'desc'); ?>"><?=t('Date/Time'); ?></a></th>
                    <th class="<?=$list->getSearchResultsClass('level'); ?>"><a href="<?=$list->getSortByURL('level', 'desc'); ?>"><?=t('Level'); ?></a></th>
                    <th><span><?=t('Channel'); ?></span></th>
                    <th><span><?=t('User'); ?></span></th>
                    <th><span><?=t('Message'); ?></span></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $ent) {
        ?>
                <tr>
                    <td valign="top" style="white-space: nowrap" class="active"><?php echo $ent->getDisplayTimestamp(); ?></td>
                    <td valign="top" style="text-align: center"><?=$ent->getLevelIcon(); ?></td>
                    <td valign="top" style="white-space: nowrap"><?=$ent->getChannelDisplayName(); ?></td>
                    <td valign="top"><strong><?php
                    $uID = $ent->getUserID();
        if (empty($uID)) {
            echo t('Guest');
        } else {
            $u = User::getByUserID($uID);
            if (is_object($u)) {
                echo $u->getUserName();
            } else {
                echo tc('Deleted user', 'Deleted (id: %s)', $uID);
            }
        } ?></strong></td>
                    <td style="width: 100%"><?=$th->makenice($ent->getMessage()); ?></td>
                    <td valign="top" style="text-align: center; padding: 15px;"><a href="javascript:void(0)" class="btn btn-default btn-xs btn-danger" onclick="deleteLog(<?=$ent->getID(); ?>)"><?=t('Delete'); ?></a></td>
                </tr>
            <?php
    } ?>
            </tbody>
        </table>
    </div>

    <!-- END Body Pane -->
    <?=$list->displayPagingV2(); ?>
</div>

<script>
    $(function() {
        $('#level').selectize({
            plugins: ['remove_button']
        });
    });

    deleteLog = function(logID) {
        ConcreteAlert.confirm(
            <?= json_encode(t('Are you sure you want to delete this log?')); ?>,
            function() {
                location.href = "<?= $controller->action('deleteLog'); ?>/" + logID + "/<?= $valt->generate(); ?>";
            },
            'btn-danger',
            <?= json_encode(t('Delete')); ?>
        );
    };
</script>


<?php
} else {
    ?>
    <p><?=t('The dashboard log report has been disabled in your logging configuration.'); ?></p>
    <?php
    if ($settingsPage !== null) {
        ?>
        <a href="<?= h($settingsPage) ?>" class="btn btn-primary"><?= t('Settings') ?></a>
        <?php
    }
}
