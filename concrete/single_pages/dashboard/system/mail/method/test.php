<?php defined('C5_EXECUTE') or die('Access Denied.');
/* @var $cih ConcreteDashboardHelper */
$cih = Loader::helper('concrete/ui');
/* @var $fh FormHelper */
$fh = Loader::helper('form');

?><form method="post" action="<?=$controller->action('do_test')?>" id="mail-settings-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php
            if(!Config::get('concrete.email.enabled')) {
                ?><div class="alert alert-info"><?php echo t(/*i18n: %1$s is a configuration name, %2$s is a configuration value*/'It\'s not possible to test the settings since the mail system is disabled (the setting %1$s is set to %2$s in the configuration).', '<b>concrete.email.enabled</b>', '<b>false</b>'); ?></div><?php
            }
            else {
                echo Loader::helper('validation/token')->output('test'); ?>
                <div class="form-group">
                    <?php echo $fh->label('mailRecipient', t('Recipient email address')); ?>
                        <?php
                        if(!isset($mailRecipient)) {
                            $mailRecipient = '';
                            if(User::isLoggedIn()) {
                                $me = new User();
                                $myInfo = UserInfo::getByID($me->getUserID());
                                $mailRecipient = $myInfo->getUserEmail();
                            }
                        }
                        echo $fh->email('mailRecipient', $mailRecipient, array('required' => 'required')); ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/mail/method')?>" class="btn btn-default pull-left"><?=t('Change Settings')?></a>
            <?php
            if (Config::get('concrete.email.enabled')) {
                echo $cih->submit(t('Send'), 'mail-settings-test-form', 'right', 'btn-primary');
            }
            ?>
        </div>
	</div>
</form>
