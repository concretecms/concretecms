
<? 
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
	$sets = $category->getAttributeSets();
}
?>


<div class="ccm-dashboard-header-buttons">
	<? if (count($sets) > 0) { ?>
		<button type="button" class="btn btn-default" data-toggle="dropdown">
		<?=t('View')?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=1"><?=t('Grouped by Set')?></a></li>
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=0"><?=t('In One List')?></a></li>
		</ul>
	<? } ?>
	<button type="submit" name="task" value="activate" class="btn btn-default"><?=t('Manage Sets')?></i></button>
</div>

<?
if (count($attribs) > 0) { ?>


	<?
	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');

	
	if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) { ?>
	
	
		<?
	
		foreach($sets as $as) { ?>
	
	
		<fieldset>
			<legend><?=$as->getAttributeSetDisplayName()?></legend>
	
		<?
		
		$setattribs = $as->getAttributeKeys();
		if (count($setattribs) == 0) { ?>
		
			<div class="ccm-attribute-list-wrapper"><?=t('No attributes defined.')?></div>
		
		<? } else { ?>
			
			<div class="ccm-attribute-sortable-set-list ccm-attribute-list-wrapper" attribute-set-id="<?=$as->getAttributeSetID()?>" id="asID_<?=$as->getAttributeSetID()?>">			
			
			<?
			
			foreach($setattribs as $ak) { ?>
			
			<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /> <a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyDisplayName()?></a>
			</div>
	

			<? } ?>
			
			</div>
			
			<? } ?>
			
		</fieldset>

			
		<? } 
		
		$unsetattribs = $category->getUnassignedAttributeKeys();
		if (count($unsetattribs) > 0) { ?>
		
			<fieldset>
				<legend><?=t('Other')?></legend>
	
				<div class="ccm-attribute-list-wrapper">
				<?
				foreach($unsetattribs as $ak) { ?>
		
				<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
					<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /> <a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyDisplayName()?></a>
				</div>
	

			<? } ?>
			</fieldset>
		
		<?
		
		}
	
	} else { ?>
		
				<div class="ccm-attribute-list-wrapper">
		
		<?
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyDisplayName()?></a>
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
