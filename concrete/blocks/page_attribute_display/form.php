<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Core::make('helper/form');

echo Core::make('helper/concrete/ui')->tabs(array(
    array('add', t('Add'), true),
    array('options', t('Options')),
));
?>
<div class="ccm-tab-content" id="ccm-tab-content-options">
    <div class="form-group">
        <label class="control-label"><?php  echo t('Display Property with Formatting')?></label>
        <select name="displayTag" class="form-control">
            <option value=""><?=t('- none -')?></option>
            <option value="h1" <?php echo($this->controller->displayTag == "h1" ? "selected" : "")?>><?=t('H1 (Heading 1)')?></option>
            <option value="h2" <?php echo($this->controller->displayTag == "h2" ? "selected" : "")?>><?=t('H2 (Heading 2)')?></option>
            <option value="h3" <?php echo($this->controller->displayTag == "h3" ? "selected" : "")?>><?=t('H3 (Heading 3)')?></option>
            <option value="p" <?php echo($this->controller->displayTag == "p" ? "selected" : "")?>><?=t('p (paragraph)')?></option>
            <option value="b" <?php echo($this->controller->displayTag == "b" ? "selected" : "")?>><?=t('b (bold)')?></option>
            <option value="address" <?php echo($this->controller->displayTag == "address" ? "selected" : "")?>><?=t('address')?></option>
            <option value="pre" <?php echo($this->controller->displayTag == "pre" ? "selected" : "")?>><?=t('pre (preformatted)')?></option>
            <option value="blockquote" <?php echo($this->controller->displayTag == "blockquote" ? "selected" : "")?>><?=t('blockquote')?></option>
            <option value="div" <?php echo($this->controller->displayTag == "div" ? "selected" : "")?>><?=t('div')?></option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label"><?php  echo t('Format of Date Properties')?></label>
        <input type="text" class="form-control" name="dateFormat" value="<?php  echo $this->controller->dateFormat ?>"/>
        <div class="help-block"><?php echo sprintf(t('See the formatting options for date at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?></div>
    </div>
    <div class="form-group">
        <label class="control-label"><?php  echo t('Delimiter for Multiple Items')?></label>
        <select name="delimiter" class="form-control">
            <option value=""><?=t('- none -')?></option>
            <option value="comma" <?php echo($this->controller->delimiter == "comma" ? "selected" : "")?>><?=t('Comma (",")')?></option>
            <option value="commaSpace" <?php echo($this->controller->delimiter == "commaSpace" ? "selected" : "")?>><?=t('Comma With Space After (", ")')?></option>
            <option value="pipe" <?php echo($this->controller->delimiter == "pipe" ? "selected" : "")?>><?=t('Pipe ("|")')?></option>
            <option value="dash" <?php echo($this->controller->delimiter == "dash" ? "selected" : "")?>><?=t('Dash ("-")')?></option>
            <option value="semicolon" <?php echo($this->controller->delimiter == "semicolon" ? "selected" : "")?>><?=t('Semicolon (";")')?></option>
            <option value="semicolonSpace" <?php echo($this->controller->delimiter == "semicolonSpace" ? "selected" : "")?>><?=t('Semicolon With Space After ("; ")')?></option>
            <option value="break" <?php echo($this->controller->delimiter == "break" ? "selected" : "")?>><?=t('Newline')?></option>
        </select>
    </div>
    <fieldset>
        <legend><?=t('Thumbnail')?></legend>
        <div class="form-group">
            <label class="control-label" for="thumbnail_width"><?php echo t('Max Width'); ?></label>
            <div class="input-group">
                <input id="thumbnail_width" class="form-control" type="text" name="thumbnailWidth" value="<?php echo $this->controller->thumbnailWidth; ?>"/>
                <span class="input-group-addon"><?php echo t('px'); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="thumbnail_height"><?php echo t('Max Height'); ?></label>
            <div class="input-group">
                <input id="thumbnail_height" class="form-control" type="text" name="thumbnailHeight" value="<?php echo $this->controller->thumbnailHeight; ?>"/>
                <span class="input-group-addon"><?php echo t('px'); ?></span>
            </div>
        </div>
    </fieldset>
</div>
<div id="ccm-tab-content-add" class="ccm-tab-content">
    <div class="form-group">
        <label class="control-label"><?php  echo t('Property to Display')?></label>

        <select name="attributeHandle" class="form-control">
        <optgroup label="<?php  echo t('Page Values');?>">
        <?php
        $corePageValues = $this->controller->getAvailablePageValues();
        foreach (array_keys($corePageValues) as $cpv) {
            echo "<option value=\"".$cpv."\" ".($cpv == $this->controller->attributeHandle ? "selected=\"selected\"" : "").">".
            $corePageValues[$cpv]."</option>\n";
        }
        ?>
        </optgroup>
        <optgroup label="<?php  echo t('Page Attributes');?>">
        <?php
        $aks = $this->controller->getAvailableAttributes();
        foreach ($aks as $ak) {
            echo "<option value=\"".$ak->getAttributeKeyHandle()."\" ".($ak->getAttributeKeyHandle() == $this->controller->attributeHandle ? "selected=\"selected\"" : "").">".
            $ak->getAttributeKeyDisplayName()."</option>\n";
        }
        ?>
        </optgroup>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label"><?php  echo t('Title Text')?></label>
        <input type="text" class="form-control" name="attributeTitleText" value="<?php  echo $this->controller->attributeTitleText ?>"/>
    </div>
</div>
