<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<ul id="ccm-pagelist-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-pagelist-tab-add" href="javascript:void(0);"><?php echo ($bID>0)? t('Edit') : t('Add') ?></a></li>
	<li class=""><a id="ccm-pagelist-tab-preview"  href="javascript:void(0);"><?php echo t('Preview')?></a></li>
</ul>

<input type="hidden" name="pageListToolsDir" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/" />
<div id="ccm-pagelistPane-add" class="ccm-pagelistPane">
	<div class="ccm-block-field-group">
	  <h2><?php echo t('Number and Type of Pages')?></h2>
	  <?php echo t('Display')?>
	  <input type="text" name="num" value="<?php echo $num?>" style="width: 30px">
	  <?php echo t('pages of type')?>
	  <?php 
			$ctArray = CollectionType::getList();
	
			if (is_array($ctArray)) { ?>
	  <select name="ctID" id="selectCTID">
		<option value="0">** All **</option>
		<?php  foreach ($ctArray as $ct) { ?>
		<option value="<?php echo $ct->getCollectionTypeID()?>" <?php  if ($ctID == $ct->getCollectionTypeID()) { ?> selected <?php  } ?>>
		<?php echo $ct->getCollectionTypeName()?>
		</option>
		<?php  } ?>
	  </select>
	  <?php  } ?>
	  <input type="checkbox" name="displayFeaturedOnly" value="1" <?php  if ($displayFeaturedOnly == 1) { ?> checked <?php  } ?> style="vertical-align: middle" />
	  <?php echo t('Featured pages only.')?>

	</div>
	<div class="ccm-block-field-group">
	  <h2><?php echo t('Location in Website')?></h2>
	  <?php echo t('Display pages that are located')?>:<br/>
	  <br/>
	  <div>
			<input type="radio" name="cParentID" id="cEverywhereField" value="0" <?php  if ($cParentID == 0) { ?> checked<?php  } ?> />
			<?php echo t('everywhere')?>
			
			&nbsp;&nbsp; 
			<input type="radio" name="cParentID" id="cThisPageField" value="<?php echo $c->getCollectionID()?>" <?php  if ($bCID == $cParentID || $cThis) { ?> checked<?php  } ?>>
			<?php echo t('beneath this page')?>
			
			&nbsp;&nbsp;
			<input type="radio" name="cParentID" id="cOtherField" value="OTHER" <?php  if ($isOtherPage) { ?> checked<?php  } ?>>
			<?php echo t('beneath another page')?> </div>
			<div id="ccm-summary-selected-page-wrapper" style=" <?php  if (!$isOtherPage) { ?>display: none;<?php  } ?> padding: 8px 0px 8px 0px">
				<div id="ccm-summary-selected-page">
					<b id="ccm-pageList-underCName">
					  <?php  if ($isOtherPage) { 
						$oc = Page::getByID($cParentID);
						print $oc->getCollectionName();
					} ?>
					</b>
				</div>
				<a id="ccm-sitemap-select-page" class="dialog-launch" dialog-width="600" dialog-height="450" dialog-modal="false" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page"><?php echo t('Select Page')?></a>
				<input type="hidden" name="cParentIDValue" id="ccm-pageList-cValueField" value="<?php echo $cParentID?>">				
			</div>
	</div>
	<div class="ccm-block-field-group">
	  <h2><?php echo t('Sort Pages')?></h2>
	  <?php echo t('Pages should appear')?>
	  <select name="orderBy">
		<option value="display_asc" <?php  if ($orderBy == 'display_asc') { ?> selected <?php  } ?>><?php echo t('in their sitemap order')?></option>
		<option value="chrono_desc" <?php  if ($orderBy == 'chrono_desc') { ?> selected <?php  } ?>><?php echo t('with the most recent first')?></option>
		<option value="chrono_asc" <?php  if ($orderBy == 'chrono_asc') { ?> selected <?php  } ?>><?php echo t('with the earlist first')?></option>
		<option value="alpha_asc" <?php  if ($orderBy == 'alpha_asc') { ?> selected <?php  } ?>><?php echo t('in alphabetical order')?></option>
		<option value="alpha_desc" <?php  if ($orderBy == 'alpha_desc') { ?> selected <?php  } ?>><?php echo t('in reverse alphabetical order')?></option>
	  </select>
	</div>
	
	<div class="ccm-block-field-group">
	  <h2><?php echo t('Provide RSS Feed')?></h2>
	   <input id="ccm-pagelist-rssSelectorOn" type="radio" name="rss" class="rssSelector" value="1" <?php echo ($rss?"checked=\"checked\"":"")?>/> <?php echo t('Yes')?>   
	   &nbsp;&nbsp;
	   <input type="radio" name="rss" class="rssSelector" value="0" <?php echo ($rss?"":"checked=\"checked\"")?>/> <?php echo t('No')?>   
	   <br /><br />
	   <div id="ccm-pagelist-rssDetails" <?php echo ($rss?"":"style=\"display:none;\"")?>>
		   <strong><?php echo t('RSS Feed Title')?></strong><br />
		   <input id="ccm-pagelist-rssTitle" type="text" name="rssTitle" style="width:250px" value="<?php echo $rssTitle?>" /><br /><br />
		   <strong><?php echo t('RSS Feed Description')?></strong><br />
		   <textarea name="rssDescription" style="width:250px" ><?php echo $rssDescription?></textarea>
	   </div>
	</div>
	
	<style>
	#ccm-pagelist-truncateTxt.faintText{ color:#999; }
	<?php  if(truncateChars==0 && !$truncateSummaries) $truncateChars=128; ?>
	</style>
	<div class="ccm-block-field-group">
	   <h2><?php echo t('Truncate Summaries')?></h2>	  
	   <input id="ccm-pagelist-truncateSummariesOn" name="truncateSummaries" type="checkbox" value="1" <?php echo ($truncateSummaries?"checked=\"checked\"":"")?> /> 
	   <span id="ccm-pagelist-truncateTxt" <?php echo ($truncateSummaries?"":"class=\"faintText\"")?>>
	   		<?php echo t('Truncate descriptions after')?> 
			<input id="ccm-pagelist-truncateChars" <?php echo ($truncateSummaries?"":"disabled=\"disabled\"")?> type="text" name="truncateChars" size="3" value="<?php echo intval($truncateChars)?>" /> 
			<?php echo t('characters')?>
	   </span>
	</div>
	
</div>

<div id="ccm-pagelistPane-preview" style="display:none" class="ccm-preview-pane ccm-pagelistPane">
	<div id="pagelist-preview-content"><?php echo t('Preview Pane')?></div>
</div>