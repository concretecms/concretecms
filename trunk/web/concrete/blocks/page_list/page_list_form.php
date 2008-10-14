<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<ul id="ccm-pagelist-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-pagelist-tab-add" href="javascript:void(0);"><?=($bID>0)?'Edit':'Add'?></a></li>
	<li class=""><a id="ccm-pagelist-tab-preview"  href="javascript:void(0);">Preview</a></li>
</ul>

<input type="hidden" name="pageListToolsDir" value="<?=$uh->getBlockTypeToolsURL($bt)?>/" />
<div id="ccm-pagelistPane-add" class="ccm-pagelistPane">
	<div class="ccm-block-field-group">
	  <h2><?=t('Number and Type of Pages')?></h2>
	  <?=t('Display')?>
	  <input type="text" name="num" value="<?=$num?>" style="width: 30px">
	  <?=t('pages of type')?>
	  <?
			$ctArray = CollectionType::getList();
	
			if (is_array($ctArray)) { ?>
	  <select name="ctID" id="selectCTID">
		<option value="0">** All **</option>
		<? foreach ($ctArray as $ct) { ?>
		<option value="<?=$ct->getCollectionTypeID()?>" <? if ($ctID == $ct->getCollectionTypeID()) { ?> selected <? } ?>>
		<?=$ct->getCollectionTypeName()?>
		</option>
		<? } ?>
	  </select>
	  <? } ?>
	</div>
	<div class="ccm-block-field-group">
	  <h2><?=t('Location in Website')?></h2>
	  <?=t('Display pages that are located')?>:<br/>
	  <br/>
	  <div>
			<input type="radio" name="cParentID" id="cEverywhereField" value="0" <? if ($cParentID == 0) { ?> checked<? } ?> />
			<?=t('everywhere')?>
			
			&nbsp;&nbsp; 
			<input type="radio" name="cParentID" id="cThisPageField" value="<?=$c->getCollectionID()?>" <? if ($bCID == $cParentID || $cThis) { ?> checked<? } ?>>
			<?=t('beneath this page')?>
			
			&nbsp;&nbsp;
			<input type="radio" name="cParentID" id="cOtherField" value="OTHER" <? if ($isOtherPage) { ?> checked<? } ?>>
			<?=t('beneath another page')?> </div>
			<div id="ccm-summary-selected-page-wrapper" style=" <? if (!$isOtherPage) { ?>display: none;<? } ?> padding: 8px 0px 8px 0px">
				<div id="ccm-summary-selected-page">
					<b id="ccm-pageList-underCName">
					  <? if ($isOtherPage) { 
						$oc = Page::getByID($cParentID);
						print $oc->getCollectionName();
					} ?>
					</b>
				</div>
				<a id="ccm-sitemap-select-page" class="dialog-launch" dialog-width="600" dialog-height="450" dialog-modal="false" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page"><?=t('Select Page')?></a>
				<input type="hidden" name="cParentIDValue" id="ccm-pageList-cValueField" value="<?=$cParentID?>">				
			</div>
	</div>
	<div class="ccm-block-field-group">
	  <h2><?=t('Sort Pages')?></h2>
	  <?=t('Pages should appear')?>
	  <select name="orderBy">
		<option value="display_asc" <? if ($orderBy == 'display_asc') { ?> selected <? } ?>><?=t('in their sitemap order')?></option>
		<option value="chrono_desc" <? if ($orderBy == 'chrono_desc') { ?> selected <? } ?>><?=t('with the most recent first')?></option>
		<option value="chrono_asc" <? if ($orderBy == 'chrono_asc') { ?> selected <? } ?>><?=t('with the earlist first')?></option>
		<option value="alpha_asc" <? if ($orderBy == 'alpha_asc') { ?> selected <? } ?>><?=t('in alphabetical order')?></option>
		<option value="alpha_desc" <? if ($orderBy == 'alpha_desc') { ?> selected <? } ?>><?=t('in reverse alphabetical order')?></option>
	  </select>
	</div>
	
	<div class="ccm-block-field-group">
	  <h2><?=t('Provide RSS Feed')?></h2>
	   <input id="ccm-pagelist-rssSelectorOn" type="radio" name="rss" class="rssSelector" value="1" <?=($rss?"checked=\"checked\"":"")?>/> <?=t('Yes')?>   
	   &nbsp;&nbsp;
	   <input type="radio" name="rss" class="rssSelector" value="0" <?=($rss?"":"checked=\"checked\"")?>/> <?=t('No')?>   
	   <br /><br />
	   <div id="ccm-pagelist-rssDetails" <?=($rss?"":"style=\"display:none;\"")?>>
		   <strong><?=t('RSS Feed Title')?></strong><br />
		   <input id="ccm-pagelist-rssTitle" type="text" name="rssTitle" style="width:250px" value="<?=$rssTitle?>" /><br /><br />
		   <strong><?=t('RSS Feed Description')?></strong><br />
		   <textarea name="rssDescription" style="width:250px" ><?=$rssDescription?></textarea>
	   </div>
	</div>
</div>

<div id="ccm-pagelistPane-preview" style="display:none" class="ccm-preview-pane ccm-pagelistPane">
	<div id="pagelist-preview-content"><?=t('Preview Pane')?></div>
</div>