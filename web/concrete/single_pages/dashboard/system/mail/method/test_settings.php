<?php defined('C5_EXECUTE') or die('Access Denied.');

/* @var $cdh ConcreteDashboardHelper */
$cdh = Loader::helper('concrete/dashboard');
/* @var $cih ConcreteDashboardHelper */
$cih = Loader::helper('concrete/interface');
/* @var $fh FormHelper */
$fh = Loader::helper('form');

echo $cdh->getDashboardPaneHeaderWrapper(t('Test Mail Settings'), false, 'span8 offset2', false);

?><form method="post" action="<?=$this->url('/dashboard/system/mail/method/test_settings', 'test')?>" class="form-horizontal" id="mail-settings-test-form">
	<div class="ccm-pane-body"><?php
		if(!ENABLE_EMAILS) {
			?><div class="alert alert-info"><?php echo t(/*i18n: %1$s is a configuration name, %2$s is a configuration value*/'It\'s not possible to test the settings since the mail system is disabled (the setting %1$s is set to %2$s in the configuration).', '<b>ENABLE_EMAILS</b>', '<b>false</b>'); ?></div><?php
		}
		else {
			echo Loader::helper('validation/token')->output('test'); ?>
			<div class="control-group">
				<?php echo $fh->label('mailRecipient', t('Recipient email address')); ?>
				<div class="controls">
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
			</div>
			<?php
		}
	?></div>
	<div class="ccm-pane-footer">
		<a href="<?=$this->url('/dashboard/system/mail/method')?>" class="btn"><?=t('Change Settings')?></a>
		<?php
		if (ENABLE_EMAILS) {
			echo $cih->submit(t('Send'), 'mail-settings-test-form', 'right', 'primary');
		}
		?>
	</div>
</form><?php
echo $cdh->getDashboardPaneFooterWrapper(false);
