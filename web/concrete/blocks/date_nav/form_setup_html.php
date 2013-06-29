<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>  

<style>
#ccm-pagelist-truncateTxt.faintText, #ccm-pagelist-truncateTitleTxt.faintText{ color:#999; }
</style>

<script type="text/javascript">
$("select#cParentIDLocation").change(function() {
	if ($(this).attr("value") == 'OTHER') {
		$("div.ccm-page-list-page-other").show();
	} else {
		$("div.ccm-page-list-page-other").hide();
	}
});
</script>

<input type="hidden" name="dateNavToolsDir" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/" />
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
		<option value="0">** <?php echo t('All')?> **</option>
		<?php  foreach ($ctArray as $ct) { ?>
		<option value="<?php echo $ct->getCollectionTypeID()?>" <?php  if ($controller->ctID == $ct->getCollectionTypeID()) { ?> selected <?php  } ?>>
		<?php echo $ct->getCollectionTypeName()?>
		</option>
		<?php  } ?>
	  </select>
	  <?php  } ?>
	  <label class="checkbox">
		  <input type="checkbox" name="displayFeaturedOnly" value="1" <?php  if ($controller->displayFeaturedOnly == 1) { ?> checked <?php  } ?> />
		  <?php echo t('Featured pages only.')?>
	  </label>

	</div>
	
	
	<div class="ccm-block-field-group">
		<h2><?php echo t('Location in Website')?></h2>
		<?php echo t('Display pages that are located')?>:<br/>
		<br/>
		<div>
			<select name="cParentID" id="cParentIDLocation">
			<option value="0" <?php  if ($controller->cParentID == 0) { ?> selected="selected"<?php  } ?>><?php echo t('everywhere')?></option>
			<option value="<?php echo $c->getCollectionParentID()?>" <?php  if ($controller->cParentID == $c->getCollectionParentID()) { ?> selected="selected"<?php  } ?>>
			<?php echo t('at the current level')?></option>
			<option value="<?php echo $c->getCollectionID()?>" <?php  if ($bCID == $controller->cParentID || $controller->cThis) { ?> selected="selected"<?php  } ?>>
			<?php echo t('beneath this page')?></option>
			<option value="OTHER" <?php  if ($isOtherPage) { ?> selected="selected"<?php  } ?>>
			<?php echo t('beneath another page')?></option>
			</select>
		</div>
			
		<div class="ccm-page-list-page-other" <?php  if (!$isOtherPage) { ?> style="display: none" <?php  } ?>>			
			<?php  $form = Loader::helper('form/page_selector');
			if ($isOtherPage) {
				print $form->selectPage('cParentIDValue', $controller->cParentID);
			} else {
				print $form->selectPage('cParentIDValue');
			}
			?>
		</div> 
	</div> 
	
	<div class="ccm-block-field-group">
	 	<h2><?php echo t('Display Format')?></h2>
	 	<label class="radio inline">
			<input type="radio" name="flatDisplay" value="0" <?php  if (!$controller->flatDisplay) { ?> checked<?php  } ?> /><? echo t('Hierarchy')?>&nbsp; 
		</label>
	 	<label class="radio inline">
			<input type="radio" name="flatDisplay" value="1" <?php  if ($controller->flatDisplay) { ?> checked<?php  } ?> /><? echo t('Flat')?> 
		</label>
	</div>		
	 
	<div class="ccm-block-field-group">
	 	<h2><?php echo t('Open by default to the...')?></h2>	  
	 	<label class="radio inline">
			<input type="radio" name="defaultNode" value="current_page" <?php  if ($controller->defaultNode!='current_month') { ?> checked<?php  } ?> /><? echo t('Current Page')?>&nbsp; 
		</label>
	 	<label class="radio inline">
			<input type="radio" name="defaultNode" value="current_month" <?php  if ($controller->defaultNode=='current_month') { ?> checked<?php  } ?> /><? echo t('Current Month')?> 
		</label>
	</div>		 
	 
	<div class="ccm-block-field-group">
	 	<h2><?php echo t('Page Info')?></h2>	  
	 	<label class="radio inline">
			<input type="radio" name="showDescriptions" value="0" <?php  if (!$controller->showDescriptions) { ?> checked<?php  } ?> /><? echo t('Titles')?>&nbsp;
		</label>
	 	<label class="radio inline">
			<input type="radio" name="showDescriptions" value="1" <?php  if ($controller->showDescriptions) { ?> checked<?php  } ?> /><? echo t('Titles &amp; Descriptions')?>
		</label>
	</div>	

	<?php  if($controller->truncateTitleChars==0 && !$controller->truncateTitles) $controller->truncateTitleChars=128; ?>
	<div class="ccm-block-field-group">
	   <h2><?php echo t('Truncate Titles')?></h2>	  
   		<label class="radio inline">
		   <input id="ccm-pagelist-truncateTitlesOn" name="truncateTitles" type="checkbox" value="1" <?php echo ($controller->truncateTitles?"checked=\"checked\"":"")?> /> 
		   <span class="ccm-pagelist-truncateTitleTxt" <?php echo ($controller->truncateTitles?"":"class=\"faintText\"")?>>
	   			<?php echo t('Truncate titles after')?> 
	   		</span>
	   	</label>
		   <span class="ccm-pagelist-truncateTitleTxt" <?php echo ($controller->truncateTitles?"":"class=\"faintText\"")?>>
			<input id="ccm-pagelist-truncateTitleChars" <?php echo ($controller->truncateTitles?"":"disabled=\"disabled\"")?> type="text" name="truncateTitleChars" size="3" value="<?php echo intval($controller->truncateTitleChars)?>" /> 
			<?php echo t('characters')?>
	   </span>
	</div>	

	<?php  if($controller->truncateChars==0 && !$controller->truncateSummaries) $controller->truncateChars=128; ?>
	<div id="ccm-pagelist-summariesOptsWrap" class="ccm-block-field-group" style=" <?php echo (!$controller->showDescriptions)?'display:none':''?>">
	   <h2><?php echo t('Truncate Summaries')?></h2>	  
	   <input id="ccm-pagelist-truncateSummariesOn" name="truncateSummaries" type="checkbox" value="1" <?php echo ($controller->truncateSummaries?"checked=\"checked\"":"")?> /> 
	   <span id="ccm-pagelist-truncateTxt" <?php echo ($controller->truncateSummaries?"":"class=\"faintText\"")?>>
	   		<?php echo t('Truncate descriptions after')?> 
			<input id="ccm-pagelist-truncateChars" <?php echo ($controller->truncateSummaries?"":"disabled=\"disabled\"")?> type="text" name="truncateChars" size="3" value="<?php echo intval($controller->truncateChars)?>" /> 
			<?php echo t('characters')?>
	   </span>
	</div>

	<script>

	</script>
	
</div>