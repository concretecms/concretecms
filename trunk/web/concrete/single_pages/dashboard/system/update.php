<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<h1><span><?=t('Update concrete5')?></span></h1>
<div class="ccm-dashboard-inner">
<?
$ih = Loader::helper('concrete/interface');

switch(count($updates)) {
	case '1': ?>
	
	<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
	<input type="hidden" name="updateVersion" value="<?=$updates[0]->getUpdateVersion()?>" />
	
	<p><?=t('An update is available. Click below to update to <strong>%s</strong>', $updates[0]->getUpdateVersion())?>
	</p>

	<?=$ih->submit(t('Update'), 'ccm-update-form', 'left')?>
	
			<div class="ccm-spacer">&nbsp;</div>

	</form>	
	
	<?	
		break;
	case '0':
		print t('There are no downloaded updates available.');	
		break;
	default: ?>
	
	<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
	<p><?=t('Several updates are available. Please choose the desired update from the list below.')?></p>
	<? 
		$checked = true;
		foreach($updates as $upd) { 
	
		?>

		<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?=$upd->getUpdateVersion()?>" <? if ($checked) { ?> checked <? } ?> />
			<?=$upd->getUpdateVersion()?>
		</div>
		
		<?
		$checked = false;
		
		}
		
		?>

		<?=$ih->submit(t('Update'), 'ccm-update-form', 'left')?>
		
		<div class="ccm-spacer">&nbsp;</div>
		
	
	</form>	

	
	<? 
	
		break;
}
?>
</div>