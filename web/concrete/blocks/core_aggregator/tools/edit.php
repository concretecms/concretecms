<?php
defined('C5_EXECUTE') or die("Access Denied.");
$cID = intval($_REQUEST['cID']);
$bID = intval($_REQUEST['bID']);
$arHandle = Loader::helper('security')->sanitizeString($_REQUEST['arHandle']);
if ($cID > 0 && $bID > 0) {
	$c = Page::getByID($cID);
	$b = Block::getByID($bID, $c, $arHandle);
	if (is_object($b) && !$b->isError()) {
		$bp = new Permissions($b);
		if ($bp->canEditBlock()) { 

			$bt = $b->getBlockTypeObject();
			$controller = $b->getController();
			$controller->runTask('edit', array());
			$sets = $controller->getSets();
			$helpers = $controller->getHelperObjects();
			$data = array_merge($sets, $helpers);
			$data['b'] = $b;
			$data['controller'] = $controller;

			?>

			<form method="post" id="ccm-aggregator-edit-form" action="<?=$b->getBlockEditAction()?>" enctype="multipart/form-data">
				<?=Loader::helper('validation/token')->output()?>
				<input type="hidden" name="arHandle" value="<?=$arHandle?>" />
				<input type="hidden" name="cID" value="<?=$cID?>" />
				<input type="hidden" name="bID" value="<?=$bID?>" />
				<input type="hidden" name="processBlock" value="1" />
				<input type="hidden" name="update" value="1" />

			<?

			switch($_REQUEST['tab']) {
				case 'sources':
					$bt->inc('form/sources.php', $data);
					break;
				default: // output
					$bt->inc('form/output.php', $data);
					break;
			}

			?>

			<div class="dialog-buttons">
				<button class="btn btn-hover-danger pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
				<button class="btn btn-primary pull-right" onclick="$('#ccm-aggregator-edit-form').submit()"><?=t('Save')?></button>		
			</div>

			</form>

			<script type="text/javascript">
			$(function() {
				$('#ccm-aggregator-edit-form').ajaxForm({
					dataType: 'json',
					beforeSubmit: function() {
						jQuery.fn.dialog.showLoader();
					},
					success: function(resp) {
						var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=<?=$cID?>&bID=<?=$bID?>&arHandle=<?=Loader::helper('text')->entities($arHandle)?>&btask=view_edit_mode';	 
						$.get(action, 		
							function(r) {
								CCMInlineEditMode.editBlock(<?=$cID?>, resp.aID, resp.arHandle, <?=$bID?>);
								CCMToolbar.disableDirectExit();
								jQuery.fn.dialog.hideLoader();
								jQuery.fn.dialog.closeTop();
							}
						);
					}
				});
			});
			</script>

			<?
		}
	}
}