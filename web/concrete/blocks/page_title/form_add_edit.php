<?php defined('C5_EXECUTE') or die("Access Denied."); 
if(!strlen($titleText)) { $titleText = $controller->getTitleText();}
?>

<div class="form-group" class="ccm-ui">
    <label><?=t("Custom Title")?></label>
	<div class="checkbox"><label>
        <?php echo $form->checkbox('useCustomTitle', 1, $useCustomTitle);?>
        <?=t('Override page name with custom title?')?>
    </label></div>
</div>

<div class="form-group">
	<?=$form->label('titleText', t('Custom Title Text'))?>
    <?php echo $form->text('titleText', $titleText); ?>
</div>
