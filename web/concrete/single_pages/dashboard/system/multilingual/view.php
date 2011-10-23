<? defined('C5_EXECUTE') or die("Access Denied.");?>

<h1><span><?=t('Multilingual Setup')?></span></h1>
<div class="ccm-dashboard-inner">
<h2><?=t('Interface')?></h2>
<? 

if (count($languages) == 0) { ?>
	<?=t("You don't have any interface languages installed. You must run concrete5 in English.");?>
<? } else { ?>
	
	<form method="post" action="<?=$this->action('save_interface_language')?>">
	<div><?=$form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?> <?=$form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Offer choice of language on login.'))?></div>
	<? if (defined('LOCALE')) { ?>
	<br/>
		<strong><?=t('Default Language: ')?></strong>
		<div><? foreach($interfacelocales as $sl => $v) {
			if ($sl == LOCALE) {
				print $v;
			}
		} ?> <?=t('This has been set in config/site.php')?></div>
	<? } else { ?>
		<div><?=$form->label('SITE_LOCALE', t('Default Language'))?> <?=$form->select('SITE_LOCALE', $interfacelocales, SITE_LOCALE);?></div>
	<? } ?>
	
	<br/>
	<?=Loader::helper('validation/token')->output('save_interface_language')?>
	<?= Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left')?>
	</form>
	
<? } ?>

</div>