<? defined('C5_EXECUTE') or die("Access Denied.");?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Multilingual Setup'), false, 'span8 offset2', false)?>
<? 

if (count($languages) == 0) { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<?=t("You don't have any interface languages installed. You must run concrete5 in English.");?>
</div>
<? } else { ?>

<form method="post" class="form-horizontal" action="<?=$this->action('save_interface_language')?>">
<div class="ccm-pane-body">
	
	<div class="control-group">
	<?=$form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Login'))?>
	<div class="controls">
		<label class="checkbox"><?=$form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?> <span><?=t('Offer choice of language on login.')?></span></label>
	</div>
	</div>
	
	<?
	$args = array();
	if (defined("LOCALE")) {
		$args['disabled'] = 'disabled';
	}
	?>
	
	<div class="control-group">
	<?=$form->label('SITE_LOCALE', t('Default Language'))?>
	<div class="controls">
	<?=$form->select('SITE_LOCALE', $interfacelocales, SITE_LOCALE, $args);?>
	</div>
	</div>
	
	<br/>
	<?=Loader::helper('validation/token')->output('save_interface_language')?>
</div>
<div class="ccm-pane-footer">
	<?= Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left', 'primary')?>
</div>
</form>
	
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>