<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>


<div class="ccm-block-field-group">
  <h2><?=t('Location in Website')?></h2>
  Display discussions that are located:<br/>
  <br/>
  <div>

		<input type="radio" name="cParentID" id="cThisPageField" value="<?=$c->getCollectionID()?>" checked>
		Beneath this page
		
		&nbsp;&nbsp;
		<input type="radio" name="cParentID" id="cOtherField" value="OTHER">
		Beneath another page </div>
		<div id="ccm-discussion-selected-page-wrapper" style=" <? if (!$isOtherPage) { ?>display: none;<? } ?> padding: 8px 0px 8px 0px">
			<div id="ccm-discussion-selected-page">
				<b id="ccm-discussion-underCName"></b>
			</div>
			<a id="ccm-sitemap-select-page" class="dialog-launch" dialog-width="600" dialog-height="450" dialog-modal="false" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page">Select Page</a>
			<input type="hidden" name="cParentIDValue" id="ccm-discussion-cValueField" value="<?=$cParentID?>">				
		</div>
</div>