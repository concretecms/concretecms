<?php
defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/ui');
$form = Loader::helper('form');
$view = View::getInstance();
if ($canUpgrade) { ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?= $view->action('check_for_updates') ?>" class="btn btn-primary">
            <?= t('Check For Updates') ?>
        </a>
    </div>


   <? if (is_object($update)) { ?>

        <div class="ccm-dashboard-update-details-wrapper">

            <div class="ccm-dashboard-update-details">
                <div class="ccm-dashboard-update-thumbnail"><?
                    $thumb = $update->getThumbnailURL();
                    printf('<img src="%s">', $thumb);
                    ?>
                </div>
                <h2><?=t('Version %s', $update->getVersion())?></h2>
                <div><i class="fa fa-cog"></i> <span class="ccm-dashboard-update-details-testing-text"><?=t('Testing System...')?></span></div>
            </div>

            <div class="ccm-dashboard-update-nav">
                <form method="post" action="<?=$view->action('do_update')?>">
                    <?=$token->output('do_update')?>
                    <input type="hidden" name="version" value="<?=$update->getVersion()?>" />
                    <div class="ccm-dashboard-update-apply">
                        <button class="btn btn-primary" disabled="disabled" type="submit" name="update" value="1"><?=t('Checking...')?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="ccm-dashboard-update-detail-columns">
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-group">
                        <li class="list-group-item"><a href="#notes"><?=t('Release Notes')?></a></li>
                        <li class="list-group-item"><span data-href="#addons" class="text-muted"><?=t('Add-On Compatibility')?></span></li>
                        <li class="list-group-item"><span data-href="#notices" class="text-muted"><?=t('Important Notices')?></span></li>
                    </ul>
                </div>
                <div class="col-md-7 col-md-offset-1 ccm-dashboard-update-detail-main">
                    <a name="notes"></a>
                    <a href="<?=$update->getInfoURL()?>" target="_blank" class="btn btn-default pull-right btn-xs "><?=t('View Full Release Notes')?></a>
                    <h3><?=t('Release Notes')?></h3>
                    <div class="ccm-dashboard-update-detail-release-notes"><?=t('Retrieving Release Notes...')?></div>

                    <div class="spacer-row-5"></div>

                    <a name="addons"></a>
                    <h3><?=t('Add-On Compatibility')?></h3>
                    <? $list = \Package::getInstalledList();
                    $ci = Core::make('helper/concrete/urls');
                    foreach($list as $pkg) { ?>

                        <div class="media" data-addon="<?=$pkg->getPackageHandle()?>">
                            <div class="pull-left"><img style="width: 49px" src="<?= $ci->getPackageIconURL($pkg); ?>" class"media-object" /></div>
                            <div class="media-body">
                                <i class="fa fa-question-circle text-muted pull-right"></i>
                                <h4 class="media-heading"><?= $pkg->getPackageName(); ?> <span class="badge badge-info" style="margin-right: 10px"><?= tc('AddonVersion', 'v.%s', $pkg->getPackageVersion()); ?></span></h4>
                                <div class="ccm-dashboard-update-detail-status-text"></div>
                            </div>
                        </div>

                    <? } ?>

                    <div class="spacer-row-5"></div>

                    <h3><?=t('Upgrade Notices')?></h3>
                    <a name="notices"></a>
                    <div class="ccm-dashboard-update-detail-notices"><?=t('Loading...')?></div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        $(function() {
            $.ajax({
                dataType: 'json',
                type: 'post',
                data: {
                    'version': '<?=$update->getVersion()?>'
                },
                complete: function() {
                    $('.ccm-dashboard-update-apply button').prop('disabled', false).text('<?=t('Install Update')?>');
                },
                url: '<?=$view->action('get_update_diagnostic_information')?>',
                success: function(r) {
                    $('.ccm-dashboard-update-detail-release-notes').html(r.releaseNotes);
                    $('span[data-href]').each(function() {
                        var $tag = $('<a />', {'href': $(this).attr('data-href'), text: $(this).text()});
                        $(this).replaceWith($tag);
                    });
                    var $wrapper = $('.ccm-dashboard-update-detail-notices');
                    $wrapper.html('');
                    if (r.notices && r.notices.length) {
                        $.each(r.notices, function(i, notice) {
                            var className = '';
                            switch(notice.safety) {
                                case 'info':
                                    className = 'fa fa-question-circle text-info';
                                    textClassName = '';
                                    break;
                                case 'warning':
                                    className = 'fa fa-warning text-warning';
                                    textClassName = 'text-warning';
                                    break;
                                case 'danger':
                                    className = 'fa fa-exclamation-circle text-danger';
                                    textClassName = 'text-danger';
                                    break;
                            }
                            $wrapper.append('<div class="media"><div class="pull-left"><i class="' + className + '"></i></div><div class="media-body ' + textClassName + '">' + notice.status + '</div></div>');
                        });
                    } else {
                        $wrapper.append('<?=t('No upgrade notices found.')?>');
                    }
                    var $statusIcon = $('.ccm-dashboard-update-details i'),
                        $statusText = $('.ccm-dashboard-update-details-testing-text');
                    if (r.status) {
                        var className = '';
                        switch(r.status.safety) {
                            case 'success':
                                className = 'fa fa-check text-success';
                                textClassName = 'text-success';
                                break;
                            case 'warning':
                                className = 'fa fa-warning text-warning';
                                textClassName = 'text-warning';
                                break;
                            case 'danger':
                                className = 'fa fa-exclamation-circle text-danger';
                                textClassName = 'text-danger';
                                break;
                            default:
                                className = 'fa fa-arrow-circle-right';
                                textClassName = '';
                        }
                        $statusIcon.removeClass().addClass(className);
                        $statusText.removeClass().addClass(textClassName).text(r.status.status);
                    } else {
                        $statusIcon.removeClass().addClass('fa fa-arrow-circle-right');
                        $statusText.removeClass().addClass(textClassName).text('<?=t('Update Ready')?>');
                    }
                    $('[data-addon]').each(function() {
                        var $addon = $(this);
                        var item = false;
                        var textClassName = '';
                        var mpHandle = $addon.attr('data-addon');
                        if (r.marketplaceItemStatuses) {
                            var item = _.find(r.marketplaceItemStatuses, function(item) {
                                return item.mpHandle == mpHandle;
                            });
                            if (item) {
                                var className = '';
                                switch(item.safety) {
                                    case 'success':
                                        className = 'fa fa-check text-success pull-right';
                                        textClassName = 'text-success';
                                        break;
                                    case 'warning':
                                        className = 'fa fa-warning text-warning pull-right';
                                        textClassName = 'text-warning';
                                        break;
                                    case 'danger':
                                        className = 'fa fa-exclamation-circle text-danger pull-right';
                                        textClassName = 'text-danger';
                                        break;
                                }

                                if (className) {
                                    $addon.find('i').removeClass().addClass(className);
                                }
                            }
                        }
                        if (item) {
                            $addon.find('.ccm-dashboard-update-detail-status-text').addClass(textClassName).html(item.status);
                        } else {
                            $addon.find('.ccm-dashboard-update-detail-status-text').html('<?=t('No information about this add-on available.')?>');
                        }
                    });
                }
            });
        });
        </script>


    <? } else { ?>


        <? if ($downloadableUpgradeAvailable) { ?>

            <h2><?= t('Available Update') ?></h2>
            <form method="post" action="<?= $view->action('download_update') ?>" id="ccm-download-update-form">

                <?= Loader::helper('validation/token')->output('download_update') ?>

                <legend style="line-height:40px">
                    <?= t('Version: %s', $update->version) ?>.
                    <?= t('Release Date: %s', date(t('F d, Y'), strtotime($update->date))) ?>
                    <?= Loader::helper('concrete/ui')->submit(
                        t('Download'),
                        'ccm-download-update-form',
                        'right',
                        'btn-success') ?>
                </legend>
                <div id="ccm-release-notes">
                    <?= $update->notes ?>
                </div>
                <hr/>
                <span class="help-block"><?= t('Note: Downloading an update will NOT automatically install it.') ?></span>

            </form>
            <script>
                $('header.ccm-dashboard-page-header').children().text('<?= t('Currently Running %s', config::get('concrete.version')) ?>');
            </script>

        <?

            }

        if (count($updates)) {
            ?>
            <div class="alert alert-warning">
                <i class="fa fa-warning"></i> <?= t(
                    'Make sure you <a href="%s">backup your database</a> before updating.',
                    $view->url('/dashboard/system/backup/backup')) ?>
            </div>
            <?php
            $ih = Loader::helper('concrete/ui');
            ?>

            <p><?= t('Several updates are available. Please choose the desired update from the list below.') ?></p>
            <span class="label"><?= t('Current Version') ?> <?= config::get('concrete.version') ?></span>
            <form method="post" class="form" action="<?= $view->action('start') ?>" id="ccm-update-form">
                <?php
                $checked = true;
                foreach ($updates as $upd) {
                    ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="updateVersion"
                                   value="<?= $upd->getUpdateVersion() ?>" <?= (!$checked ? '' : "checked") ?> />
                            <?= $upd->getUpdateVersion() ?>
                        </label>
                    </div>
                    <?php
                    $checked = false;
                }
                ?>
                <div class="ccm-dashboard-form-actions-wrapper">
                    <div class="ccm-dashboard-form-actions">
                        <?= $ih->submit(t('Update'), false, 'right', 'btn-primary') ?>
                    </div>
                </div>
            </form>
            </div>
            <div class="clearfix">&nbsp;</div>
        <?
        }?>

    <? } ?>

<? } else { ?>
    <p><?=t('You do not have permission to upgrade this installation of concrete5.')?></p>
<? } ?>
