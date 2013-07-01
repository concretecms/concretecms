<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<? 
$ci = Loader::helper('concrete/urls'); 
$st = Stack::getByID($_REQUEST['stackID']);
$blocks = $st->getBlocks(STACKS_AREA_NAME);
if (count($blocks) == 0) { ?>
	<p><?=t('There are no blocks in this stack.')?></p>
	<div id="ccm-tab-content-add-stack">
        <h3><?=t('Add Stack')?></h3>
        <p><?=t('Add the entire stack to this page.')?></p>
        <p><a class="btn primary" href="javascript:void(0)" onclick="ccmStackAddToArea(<?=$st->getCollectionID()?>, '<?=Loader::helper('text')->entities($a->getAreaHandle())?>')"><?=t("Add Stack")?></a></p>
    </div>

<? } else { ?>
	
	<?=Loader::helper('concrete/interface')->tabs(array(
		array('add-stack', t('Full Stack'), true),
		array('add-stack-block', t('Individual Block'))
	));
	?>
		
	<div id="ccm-tab-content-add-stack">
        <h3><?=t('Add Stack')?></h3>
        <p><?=t('Add the entire stack to this page.')?></p>
        <p><a class="btn primary" href="javascript:void(0)" onclick="ccmStackAddToArea(<?=$st->getCollectionID()?>, '<?=Loader::helper('text')->entities($a->getAreaHandle())?>')"><?=t("Add Stack")?></a></p>
    </div>

	<div id="ccm-tab-content-add-stack-block" style="display: none">
	
	<? foreach($blocks as $b) { 
		$bt = $b->getBlockTypeObject();
		$btIcon = $ci->getBlockTypeIconURL($bt);
		$name = t($bt->getBlockTypeName());
		if ($b->getBlockName() != '') {
			$name = $b->getBlockName();
		}
		?>			
		<div class="ccm-scrapbook-list-item" id="ccm-stack-block-<?=$b->getBlockID()?>">
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" href="javascript:void(0)" onclick="var me=this; if(me.disabled)return; me.disabled=true; jQuery.fn.dialog.showLoader();$.get('<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?bID=<?=$b->getBlockID()?>&add=1&processBlock=1&cID=<?=$c->getCollectionID()?>&arHandle=<?=$a->getAreaHandle()?>&btask=alias_existing_block&<?=$token?>', function(r) { me.disabled=false; ccm_parseBlockResponse(r, false, 'add'); })"><?=$name?></a>
				<div class="ccm-scrapbook-list-item-detail">	
					<?	
					try {
						$bv = new BlockView();
						$bv->render($b, 'scrapbook');
					} catch(Exception $e) {
						print BLOCK_NOT_AVAILABLE_TEXT;
					}	
					?>
				</div>
			</div>
		</div>	
		<?
		}
	} ?>
	</div>
	
</div>
