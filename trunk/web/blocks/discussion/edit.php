<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>


<div class="ccm-block-field-group">
  <h2>Location in Website</h2>
  Display discussions that are located:<br/>
  <br/>
  <div>

		<input type="radio" name="cParentID" id="cThisPageField" value="<?=$c->getCollectionID()?>" <? if ($c->getCollectionID() == $cParentID || $cThis) { ?> checked<? } else { $isOtherPage = true; } ?>>
		Beneath this page
		
		&nbsp;&nbsp;
		<input type="radio" name="cParentID" id="cOtherField" value="OTHER" <? if ($isOtherPage) { ?> checked<? } ?>>
		Beneath another page </div>
		<div id="ccm-discussion-selected-page-wrapper" style=" <? if (!$isOtherPage) { ?>display: none;<? } ?> padding: 8px 0px 8px 0px">
			<div id="ccm-discussion-selected-page">
				<b id="ccm-discussion-underCName">
				  <? if ($isOtherPage) { 
					$oc = Page::getByID($cParentID);
					print $oc->getCollectionName();
				} ?>
				</b>
			</div>
			<a id="ccm-sitemap-select-page" class="dialog-launch" dialog-width="600" dialog-height="450" dialog-modal="false" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page">Select Page</a>
			<input type="hidden" name="cParentIDValue" id="ccm-discussion-cValueField" value="<?=$cParentID?>">				
		</div>
</div>