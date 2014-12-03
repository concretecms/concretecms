<?php defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper("interface/flag", 'multilingual');
?>

<div class="ccm-multilingual-language-list-wrapper">
	<strong><?php echo $label?></strong>
	
	<form method="post" action="<?php echo $action?>" id="ccm-multilingual-language-list">
	<?php if (Loader::helper('validation/numbers')->integer($_REQUEST['rcID'])) { ?>
		<input type="hidden" name="ccmMultilingualCurrentPageID" value="<?php echo Loader::helper('text')->entities($_REQUEST['rcID'])?>" />
	<?php } ?>

<?php foreach($languageSections as $ml) {  ?>
	<div class="ccm-multilingual-language-list-item">
	
	<input type="radio" name="ccmMultilingualSiteDefaultLanguage" value="<?php echo $ml->getLocale()?>"  <?php if ($defaultLanguage == $ml->getLocale()) { ?> checked="checked" <?php } ?> /><?
		print $ih->getSectionFlagIcon($ml);	
		print $ml->getLanguageText($ml->getLocale());
		print ' ' . (strlen($ml->msIcon)?'('.$ml->msIcon.')':'');
	?></div>
	
<?php } ?>

	<div class="ccm-multilingual-site-default-remember">
		<?php echo $form->checkbox('ccmMultilingualSiteRememberDefault', 1, 1)?> <?php echo $form->label('ccmMultilingualSiteRememberDefault', t('Remember my choice on this computer.'))?>
	</div>
	
	<?php echo $form->submit('submit', t('Save'))?>
	</form>
	
</div>