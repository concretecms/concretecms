<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$c = Page::getCurrentPage();

if(!$ak instanceof CollectionAttributeKey) {?>
	<div class="ccm-error"><?=t('Error: The required page attribute with the handle of: "%s" doesn\'t exist',$controller->attributeHandle)?><br/><br/></div>
<? } else { ?>
<input type="hidden" name="attributeHandle" value="<?=$controller->attributeHandle?>" />

    <?=$form->label('title', t('Title'))?>
	<div class="form-group">
		<?php echo $form->text('title',$title);?>
	</div>

	<label><?=t('Display a List of Tags From')?></label>
    <div class="form-group">
        <div class="radio">
            <label>
                <?php echo $form->radio('displayMode','page',$displayMode)?><?php echo t('The Current Page.')?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php echo $form->radio('displayMode','cloud',$displayMode)?><?php echo t('The Entire Site.')?>
            </label>
        </div>
    </div>

	<?php if (!$inStackDashboardPage) { ?>
	<div id="ccm-tags-display-page" class="form-group">
	<label><?php echo $ak->getAttributeKeyDisplayName();?></label>
        <div class="input">
            <?php
                $av = $c->getAttributeValueObject($ak);
                $ak->render('form',$av);
            ?>
        </div>
	</div>
	<?php } ?>

	<div id="ccm-tags-display-cloud" class="form-group">
     <?=$form->label('cloudCount', t('Number to Display'))?>
	<div class="input">
		<?php echo $form->text('cloudCount',$cloudCount,array('size'=>4))?>
	</div>
	</div>


	<div class="clearfix">
	<label style="margin-bottom: 0px;"><?=t('Link Tags to Filtered Page List')?></label>
	<div class="input">
		<?php
		$form_selector = Loader::helper('form/page_selector');
		print $form_selector->selectPage('targetCID', $targetCID);
		?>
	</div>
	</div>

<?php } ?>
<script>
    $(function(){ tags.init(); });
</script>
