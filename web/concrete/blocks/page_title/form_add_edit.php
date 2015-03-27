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

<div class="form-group">
    <?php echo $form->label('formatting', t('Formatting Style'))?>
    <select class="form-control" name="formatting" id="formatting">
        <option value="h1" <?php echo ($this->controller->formatting=="h1"?"selected":"")?>><?php echo t('H1')?></option>
        <option value="h2" <?php echo ($this->controller->formatting=="h2"?"selected":"")?>><?php echo t('H2')?></option>
        <option value="h3" <?php echo ($this->controller->formatting=="h3"?"selected":"")?>><?php echo t('H3')?></option>
        <option value="h4" <?php echo ($this->controller->formatting=="h4"?"selected":"")?>><?php echo t('H4')?></option>
        <option value="h5" <?php echo ($this->controller->formatting=="h5"?"selected":"")?>><?php echo t('H5')?></option>
        <option value="h6" <?php echo ($this->controller->formatting=="h6"?"selected":"")?>><?php echo t('H6')?></option>
    </select>
</div>

