
<? 
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
	$sets = $category->getAttributeSets();
}
?>

<div class="ccm-pane-options">
<form class="form-horizontal">
<div class="ccm-pane-options-permanent-search">

	<? $form = Loader::helper('form'); ?>

	<? if (count($sets) > 0) { ?>
	<div class="span6">
	<?=$form->label('asGroupAttributes', t('View'))?>
	<div class="controls">
	<select class="span3" onchange="window.location.href='<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=' + this.value" id="asGroupAttributes" name="asGroupAttributes">
		<option value="1" <? if ($_REQUEST['asGroupAttributes'] !== '0') { ?> selected <? } ?>><?=t('Grouped by set')?></option>
		<option value="0" <? if ($_REQUEST['asGroupAttributes'] === '0') { ?> selected <? } ?>><?=t('In one list')?></option>
	</select>
	</div>
	</div>
	
	<? } ?>
	<a href="<?=$this->url('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" id="ccm-list-view-customize-top"><span class="ccm-menu-icon ccm-icon-properties"></span><?=t('Manage Sets')?></a>
</div>
</form>
</div>

<div class="ccm-pane-body">

<?
if (count($attribs) > 0) { ?>


	<?
	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');

	
	if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) { ?>
	
	
		<?
	
		foreach($sets as $as) { ?>
	
		
		<h3><?=tc('AttributeSetName', $as->getAttributeSetName())?></h3>
	
		<?
		
		$setattribs = $as->getAttributeKeys();
		if (count($setattribs) == 0) { ?>
		
			<div class="ccm-attribute-list-wrapper"><?=t('No attributes defined.')?></div>
		
		<? } else { ?>
			
			<div class="ccm-attribute-sortable-set-list ccm-attribute-list-wrapper" attribute-set-id="<?=$as->getAttributeSetID()?>" id="asID_<?=$as->getAttributeSetID()?>">			
			
			<?
			
			foreach($setattribs as $ak) { ?>
			
			<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></a>
			</div>
	

			<? } ?>
			
			</div>
			
			<? } ?>
			
			
		<? } 
		
		$unsetattribs = $category->getUnassignedAttributeKeys();
		if (count($unsetattribs) > 0) { ?>
		
			<h3><?=t('Other')?></h3>
			<div class="ccm-attribute-list-wrapper">
			<?
			foreach($unsetattribs as $ak) { ?>
	
			<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></a>
			</div>
	

			<? } ?>
			</div>
		
		<?
		
		}
	
	} else { ?>
		
		<div class="ccm-attributes-list">
		
		<?
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></a>
		</div>
		
		<? } ?>
	
		</div>
	
	<? } ?>
	
<? } else { ?>
	
	<p>
		<?
	 echo t('No attributes defined.');
		?>
	</p>
	
<? } ?>

</div>

<script type="text/javascript">
$(function() {
	$("div.ccm-attribute-sortable-set-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			ualist += '&cID=<?=$c->getCollectionID()?>';
			ualist += '&asID=' + $(this).attr('attribute-set-id');
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/attribute_sets_update', ualist, function(r) {

			});
		}
	});
});
</script>

<style type="text/css">
div.ccm-attribute-sortable-set-list img.ccm-attribute-icon:hover {cursor: move}
</style>
