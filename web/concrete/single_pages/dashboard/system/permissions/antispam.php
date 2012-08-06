<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Spam Control'), false, 'span10 offset1', (!is_object($activeLibrary) || (!$activeLibrary->hasOptionsForm())))?>
<form method="post" id="site-form" action="<?=$this->action('update_library')?>">
<? if (is_object($activeLibrary) && $activeLibrary->hasOptionsForm()) { ?>
	<div class="ccm-pane-body">
<? } ?>

	<?=$this->controller->token->output('update_library')?>
	<? if (count($libraries) > 0) { ?>

		<div class="clearfix">
		<?=$form->label('activeLibrary', t('Active Library'))?>
		<div class="input">
		<? 
		$activeHandle = '';
		if (is_object($activeLibrary)) {
			$activeHandle = $activeLibrary->getSystemAntispamLibraryHandle();
		}
		?>
		
		<?=$form->select('activeLibrary', $libraries, $activeHandle, array('class' => 'span4'))?>
		</div>
		</div>
		
		<? if (is_object($activeLibrary)) {
			if ($activeLibrary->hasOptionsForm()) {
				if ($activeLibrary->getPackageID() > 0) { 
					Loader::packageElement('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form', $activeLibrary->getPackageHandle());
				} else {
					Loader::element('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form');
				}
				
				?>
				
				<div class="clearfix">
				<?=$form->label('ANTISPAM_LOG_SPAM', t('Log settings'))?>
				<div class="input">
				<ul class="inputs-list">
					<li><label><?=$form->checkbox('ANTISPAM_LOG_SPAM', 1, Config::get('ANTISPAM_LOG_SPAM'))?> <span><?=t('Log entries marked as spam.')?></span></label>
						<span class="help-block"><?=t('Logged entries can be found in <a href="%s" style="color: #bfbfbf; text-decoration: underline">Dashboard > Reports > Logs</a>', $this->url('/dashboard/reports/logs'))?></span>
					</li>
				</ul>
				</div>
				</div>

				<div class="clearfix">
				<?=$form->label('ANTISPAM_NOTIFY_EMAIL', t('Email Notification'))?>
				<div class="input">
					<?=$form->text('ANTISPAM_NOTIFY_EMAIL', Config::get('ANTISPAM_NOTIFY_EMAIL'))?>
				<span class="help-block"><?=t('Any email address in this box will be notified when spam is detected.')?></span>
				</div>

				</div>
				
				
				<?
			}
		} ?>


	<? } else { ?>
		<p><?=t('You have no anti-spam libraries installed.')?></p>
	<? } ?>

<? if (is_object($activeLibrary) && $activeLibrary->hasOptionsForm()) { ?>
	</div>
	<div class="ccm-pane-footer">
		<?=Loader::helper('concrete/interface')->submit(t('Save Additional Settings'), 'submit', 'right', 'primary')?>
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

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper( (!is_object($activeLibrary) || (!$activeLibrary->hasOptionsForm())));?>
