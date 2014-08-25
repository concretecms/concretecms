<? defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" id="site-form" action="<?=$view->action('update_library')?>">
    <div class="form-group">
		<label for='group_id'><?=t('Spam Whitelist Group')?></label>
		<?=$form->select('group_id', (array)$groups, $whitelistGroup);?>
    </div>
	<?=$this->controller->token->output('update_library')?>
	<? if (count($libraries) > 0) { ?>

		<div class="form-group">
		<?=$form->label('activeLibrary', t('Active Library'))?>
		<?
		$activeHandle = '';
		if (is_object($activeLibrary)) {
			$activeHandle = $activeLibrary->getSystemAntispamLibraryHandle();
		}
		?>
		
		<?=$form->select('activeLibrary', $libraries, $activeHandle, array('class' => 'form-control'))?>
		</div>
		
		<? if (is_object($activeLibrary)) {
			if ($activeLibrary->hasOptionsForm()) {
				if ($activeLibrary->getPackageID() > 0) { 
					Loader::packageElement('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form', $activeLibrary->getPackageHandle());
				} else {
					Loader::element('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form');
				}
				
				?>
				
				<div class="form-group">
                <label><?= t('Log settings') ?></label>
                    <div class="checkbox">
                        <?=t('Log entries marked as spam.')?><label><?=$form->checkbox('ANTISPAM_LOG_SPAM', 1, Config::get('ANTISPAM_LOG_SPAM'))?></label>
                    </div>
					<span class="help-block"><?=t('Logged entries can be found in <a href="%s" style="color: #bfbfbf; text-decoration: underline">Dashboard > Reports > Logs</a>', $view->url('/dashboard/reports/logs'))?></span>
				</div>

				<div class="form-group">
                    <label><?=t('Email Notification')?> </label>
					<?=$form->text('ANTISPAM_NOTIFY_EMAIL', Config::get('ANTISPAM_NOTIFY_EMAIL'))?>
				    <span class="help-block"><?=t('Any email address in this box will be notified when spam is detected.')?></span>
				</div>
				
				
				<?
			}
		} ?>


	<? } else { ?>
		<p><?=t('You have no anti-spam libraries installed.')?></p>
	<? } ?>

<? if (is_object($activeLibrary) && $activeLibrary->hasOptionsForm()) { ?>
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
		    <?=Loader::helper('concrete/ui')->submit(t('Save Additional Settings'), 'submit', 'right', 'btn-primary')?>
        </div>
	</div>
<? } ?>
</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeLibrary]').change(function() {
		$('#site-form').submit();
	});
});
</script>
