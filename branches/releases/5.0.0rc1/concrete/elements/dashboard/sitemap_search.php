<?php  Loader::model('collection_types'); ?>

<div id="ccm-sitemap-search">
<form method=get id="ccm-dashboard-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/sitemap_data.php">
	<input type="hidden" name="search" value="1" />
	<h2>Search Pages</h2>
	
	<div id="ccm-sitemap-search-inner" > 
	
		<div class="fieldRow">
			Search:<br><input type=text name="cKeywords" value="<?php echo $_REQUEST[cKeywords]?>" style="width:170px">	
		</div>
		
		<div class="fieldRow">
			Created on or after:<br>
			<input type="text" name="cStartDate" value="<?php echo $_REQUEST[cStartDate]?>" id="cStartDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			Created on or before:<br>
			<input type="text" name="cEndDate" value="<?php echo $_REQUEST[cEndDate]?>" id="cEndDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			Type of Page:<br/>
			<select name="ctID" style="width: 170px">
				<option value="">N/A</option>
				<?php  
					$ctArray = CollectionType::getList();
					foreach($ctArray as $ct) { ?>
					
					<option value="<?php echo $ct->getCollectionTypeID()?>"<?php  if ($_REQUEST['ctID'] == $ct->getCollectionTypeID()) { ?> selected <?php  } ?>><?php echo $ct->getCollectionTypeName()?></option>
					<?php  }
					?>
			</select>
		</div>	
		
		<div class="fieldRow">
			Pages Created by:<br>
			<input type="text" id="uIDAutocomplete" name="uName" value="<?php echo $_REQUEST[uName]?>" style="width: 170px" />
		</div>	
		
		<div class="fieldRow">
			Containing Versions by:<br>
			<input type="text" id="uIDAutocomplete2" name="uVersionCreator" value="<?php echo $_REQUEST[uVersionCreator]?>" style="width: 170px" />
		</div>	
	
		
		<div class="fieldRow">
			# Children:<br>
			<select name="cChildrenSelect" style="width: 135px">
				<option value="">N/A</option>
				<option value="lt"<?php  if ($_GET['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>>Fewer Than</option>
				<option value="="<?php  if ($_GET['cChildrenSelect'] == '=') { ?> selected <?php  } ?>>Equal To</option>
				<option value="gt"<?php  if ($_GET['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>>More Than</option>
			</select>
			<input type=text name="cChildren" value="<?php echo $_REQUEST[cChildren]?>" style="width: 30px">
		</div>	
	 
		
		<input type="submit" name="submit" id="ccm-dashboard-search-button" style="display: none" /> 
		<a href="javascript:void(0)" onclick="$('#ccm-dashboard-search-button').get(0).click()" class="ccm-button-right accept"><span>Search</span></a>
		<div style="clear:both"></div>
	
	</div>
	
</form>
</div>