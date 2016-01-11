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
        <label class="control-label"><?php  echo t('Display property with formatting')?></label>
        <select name="displayTag" class="form-control">
            <option value="">- none -</option>
            <option value="h1" <?php echo $this->controller->displayTag == "h1" ? "selected" : ""?>>H1 (Heading 1)</option>
            <option value="h2" <?php echo $this->controller->displayTag == "h2" ? "selected" : ""?>>H2 (Heading 2)</option>
            <option value="h3" <?php echo $this->controller->displayTag == "h3" ? "selected" : ""?>>H3 (Heading 3)</option>
            <option value="p" <?php echo $this->controller->displayTag == "p" ? "selected" : ""?>>p (paragraph)</option>
            <option value="b" <?php echo $this->controller->displayTag == "b" ? "selected" : ""?>>b (bold)</option>
            <option value="address" <?php echo $this->controller->displayTag == "address" ? "selected" : ""?>>address</option>
            <option value="pre" <?php echo $this->controller->displayTag == "pre" ? "selected" : ""?>>pre (preformatted)</option>
            <option value="blockquote" <?php echo $this->controller->displayTag == "blockquote" ? "selected" : ""?>>blockquote</option>
            <option value="div" <?php echo $this->controller->displayTag == "div" ? "selected" : ""?>>div</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label"><?php  echo t('Format of Date Properties')?></label>
        <input type="text" class="form-control" name="dateFormat" value="<?php  echo $this->controller->dateFormat ?>"/>
        <div class="text-muted"><?php echo sprintf(t('See the formatting options at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?></div>
    </div>
    <fieldset>
        <legend><?=t('Thumbnail')?></legend>
        <div class="form-group">
            <label class="control-label" for="thumbnail_width"><?php echo t('Width'); ?></label>
            <input id="thumbnail_width" class="form-control" type="text" name="thumbnailWidth" value="<?php echo $this->controller->thumbnailWidth; ?>"/>
        </div>
        <div class="form-group">
            <label class="control-label" for="thumbnail_height"><?php echo t('Height'); ?></label>
            <input id="thumbnail_height" class="form-control" type="text" name="thumbnailHeight" value="<?php echo $this->controller->thumbnailHeight; ?>"/>
        </div>
    </fieldset>
</div>
<div id="ccm-tab-content-add" class="ccm-tab-content">
    <div class="form-group">
        <label class="control-label"><?php  echo t('Property to Display:')?></label>

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
