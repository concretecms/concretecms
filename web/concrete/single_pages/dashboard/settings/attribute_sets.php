<h1><span><?=t("Attribute Sets")?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">

<? if (count($sets) > 0) { ?>

	<? foreach($sets as $asl) { ?>
		<div class="ccm-group">
			<a class="ccm-group-inner" href="<?=$this->url('/dashboard/settings/attribute_sets/', 'edit', $asl->getAttributeSetID())?>" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$asl->getAttributeSetName()?></a>
		</div>
	<? } ?>
</ul>
<? } else { ?>
	<?=t('No attribute sets currently defined.')?>
<? } ?>

<br/>

<h2><?=t('Add Set')?></h2>
<p><?=t('Group attributes into sets for better organization and management.')?></p>

<form method="post" action="<?=$this->action('add_set')?>">
<input type="hidden" name="categoryID" value="<?=$categoryID?>" />
<?=Loader::helper('validation/token')->output('add_set')?>
<div class="clearfix">
	<?=$form->label('asHandle', t('Handle'))?>
	<div class="input">
		<?=$form->text('asHandle')?>
	</div>
</div>

<div class="clearfix">
	<?=$form->label('asName', t('Name'))?>
	<div class="input">
		<?=$form->text('asName')?>
	</div>
</div>

<div class="actions">
	<?=$form->submit('submit', t('Add Set'), array('class' => 'primary'))?>
</div>
</form>

</div>

