<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_types'); ?>

<div id="ccm-sitemap-search">
<form method=get id="ccm-dashboard-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/sitemap_data.php">
	<input type="hidden" name="search" value="1" />
	<h2><?php echo t('Search Pages')?></h2>
	
	<div id="ccm-sitemap-search-inner" > 
	
		<div class="fieldRow">
			<?php echo t('Search')?>:<br><input type=text name="cKeywords" value="<?php echo $_REQUEST[cKeywords]?>" style="width:170px">	
		</div>
		
		<div class="fieldRow">
			<?php echo t('Created on or after')?>:<br>
			<input type="text" name="cStartDate" value="<?php echo $_REQUEST[cStartDate]?>" id="cStartDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			<?php echo t('Created on or before')?>:<br>
			<input type="text" name="cEndDate" value="<?php echo $_REQUEST[cEndDate]?>" id="cEndDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			<?php echo t('Page Type')?>:<br/>
			<select name="ctID" style="width: 170px">
				<option value=""><?php echo t('N/A')?></option>
				<?php  
					$ctArray = CollectionType::getList();
					foreach($ctArray as $ct) { ?>
					
					<option value="<?php echo $ct->getCollectionTypeID()?>"<?php  if ($_REQUEST['ctID'] == $ct->getCollectionTypeID()) { ?> selected <?php  } ?>><?php echo $ct->getCollectionTypeName()?></option>
					<?php  }
					?>
			</select>
		</div>	
		
		<div class="fieldRow">
			<?php echo t('Owner')?>:<br>
			<input type="text" id="uIDAutocomplete" name="uName" value="<?php echo $_REQUEST[uName]?>" style="width: 170px" />
		</div>	
		
		<div class="fieldRow">
			<?php echo t('Containing Versions by')?>:<br>
			<input type="text" id="uIDAutocomplete2" name="uVersionCreator" value="<?php echo $_REQUEST[uVersionCreator]?>" style="width: 170px" />
		</div>	
	
		
		<div class="fieldRow">
			<?php echo t('# Children')?>:<br>
			<select name="cChildrenSelect" style="width: 135px">
				<option value=""><?php echo t('N/A')?></option>
				<option value="lt"<?php  if ($_GET['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>><?php echo t('Fewer Than')?></option>
				<option value="="<?php  if ($_GET['cChildrenSelect'] == '=') { ?> selected <?php  } ?>><?php echo t('Equal To')?></option>
				<option value="gt"<?php  if ($_GET['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>><?php echo t('More Than')?></option>
			</select>
			<input type=text name="cChildren" value="<?php echo $_REQUEST[cChildren]?>" style="width: 30px">
		</div>	
	 
		
		<input type="submit" name="submit" id="ccm-dashboard-search-button" style="display: none" /> 
		<a href="javascript:void(0)" onclick="$('#ccm-dashboard-search-button').get(0).click()" class="ccm-button-right accept"><span><?php echo t('Search')?></span></a>
		<div style="clear:both"></div>
	
	</div>
	
</form>
</div>