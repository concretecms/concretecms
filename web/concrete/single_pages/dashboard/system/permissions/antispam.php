<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Spam Control'), false, 'span12 offset2', (!is_object($activeLibrary) || (!$activeLibrary->hasOptionsForm())))?>
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
