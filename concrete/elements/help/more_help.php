<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Core\Config\Repository\Repository $config
*/
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= t('More help.') ?></h2>
            <div class="spacer-row-2"></div>
            <div class="ccm-dialog-help-item">
                <ol class="ccm-dialog-help-item-icon-row">
                    <li><i class="fa fa-cog"></i></li>
                    <li><i class="fa fa-question-circle"></i></li>
                    <li><i class="fa fa-file-text"></i></li>
                </ol>
                <p><?= t('Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with concrete5.', $config->get('concrete.urls.help.user')) ?></p>
            </div>
            <div class="ccm-dialog-help-item">
                <ol class="ccm-dialog-help-item-icon-row">
                    <li><i class="fa fa-wrench"></i></li>
                    <li><i class="fa fa-code"></i></li>
                    <li><i class="fa fa-flash"></i></li>
                </ol>
                <p><?= t('The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom concrete5 development.', $config->get('concrete.urls.help.developer')) ?></p>
            </div>
            <div class="ccm-dialog-help-item">
                <ol class="ccm-dialog-help-item-icon-row">
                    <li><i class="fa fa-slack"></i></li>
                    <li><i class="fa fa-commenting-o"></i></li>
                    <li><i class="fa fa-external-link-square"></i></li>
                </ol>
                <p><?= t('In the <a href="%s" target="_blank">concrete5 Slack channels</a> you can get in touch with a lot of concrete5 lovers and developers.', $config->get('concrete.urls.help.slack')) ?></p>
            </div>
            <div class="ccm-dialog-help-item">
                <ol class="ccm-dialog-help-item-icon-row">
                    <li><i class="fa fa-smile-o"></i></li>
                    <li><i class="fa fa-comment"></i></li>
                    <li><i class="fa fa-external-link"></i></li>
                </ol>
                <p><?= t('Finally, <a href="%s" target="_blank">the forum</a> is full of helpful community members that make concrete5 so great.', $config->get('concrete.urls.help.forum')) ?></p>
            </div>
        </div>
    </div>
</div>
