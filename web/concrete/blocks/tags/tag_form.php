<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$c = Page::getCurrentPage();

if (!$ak instanceof Concrete\Core\Entity\Attribute\Key\Key) {
    ?>
	<div class="ccm-error"><?=t('Error: The required page attribute with the handle of: "%s" doesn\'t exist', $controller->attributeHandle)?><br/><br/></div>
<?php 
} else {
    ?>
<input type="hidden" name="attributeHandle" value="<?=$controller->attributeHandle?>" />

	<div class="form-group">
        <?=$form->label('title', t('Title'))?>
		<?php echo $form->text('title', $title);
    ?>
	</div>

    <div class="form-group">
        <label class="control-label"><?=t('Display a List of Tags From')?></label>
        <div class="radio">
            <label>
                <?php echo $form->radio('displayMode', 'page', $displayMode)?><?php echo t('The Current Page.')?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php echo $form->radio('displayMode', 'cloud', $displayMode)?><?php echo t('The Entire Site.')?>
            </label>
        </div>
    </div>

	<?php if (!$inStackDashboardPage) {
    ?>
	<div id="ccm-tags-display-page" class="form-group">
	<label><?php echo $ak->getAttributeKeyDisplayName();
    ?></label>
            <?php
                $av = $c->getAttributeValueObject($ak);
    $ak->render('form', $av);
    ?>
	</div>
	<?php 
}
    ?>

	<div id="ccm-tags-display-cloud" class="form-group">
     <?=$form->label('cloudCount', t('Number to Display'))?>
		<?php echo $form->text('cloudCount', $cloudCount, array('size' => 4))?>
	</div>


	<div class="form-group">
	<label class="control-label"><?=t('Link Tags to Filtered Page List')?></label>
		<?php
        $form_selector = Loader::helper('form/page_selector');
    echo $form_selector->selectPage('targetCID', $targetCID);
    ?>
	</div>

<?php 
} ?>
<script>
    $(function(){ tags.init(); });
</script>
