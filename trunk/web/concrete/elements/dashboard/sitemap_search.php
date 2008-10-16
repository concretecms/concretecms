<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_types'); ?>

<div id="ccm-sitemap-search">
<form method=get id="ccm-dashboard-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/sitemap_data.php">
	<input type="hidden" name="search" value="1" />
	<h2><?=t('Search Pages')?></h2>
	
	<div id="ccm-sitemap-search-inner" > 
	
		<div class="fieldRow">
			<?=t('Search')?>:<br><input type=text name="cKeywords" value="<?=$_REQUEST[cKeywords]?>" style="width:170px">	
		</div>
		
		<div class="fieldRow">
			<?=t('Created on or after')?>:<br>
			<input type="text" name="cStartDate" value="<?=$_REQUEST[cStartDate]?>" id="cStartDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			<?=t('Created on or before')?>:<br>
			<input type="text" name="cEndDate" value="<?=$_REQUEST[cEndDate]?>" id="cEndDate" style="width: 170px">
		</div>	
		
		<div class="fieldRow">
			<?=t('Page Type')?>:<br/>
			<select name="ctID" style="width: 170px">
				<option value=""><?=t('N/A')?></option>
				<? 
					$ctArray = CollectionType::getList();
					foreach($ctArray as $ct) { ?>
					
					<option value="<?=$ct->getCollectionTypeID()?>"<? if ($_REQUEST['ctID'] == $ct->getCollectionTypeID()) { ?> selected <? } ?>><?=$ct->getCollectionTypeName()?></option>
					<? }
					?>
			</select>
		</div>	
		
		<div class="fieldRow">
			<?=t('Owner')?>:<br>
			<input type="text" id="uIDAutocomplete" name="uName" value="<?=$_REQUEST[uName]?>" style="width: 170px" />
		</div>	
		
		<div class="fieldRow">
			<?=t('Containing Versions by')?>:<br>
			<input type="text" id="uIDAutocomplete2" name="uVersionCreator" value="<?=$_REQUEST[uVersionCreator]?>" style="width: 170px" />
		</div>	
	
		
		<div class="fieldRow">
			<?=t('# Children')?>:<br>
			<select name="cChildrenSelect" style="width: 135px">
				<option value="">N/A</option>
				<option value="lt"<? if ($_GET['cChildrenSelect'] == 'lt') { ?> selected <? } ?>>Fewer Than</option>
				<option value="="<? if ($_GET['cChildrenSelect'] == '=') { ?> selected <? } ?>>Equal To</option>
				<option value="gt"<? if ($_GET['cChildrenSelect'] == 'gt') { ?> selected <? } ?>>More Than</option>
			</select>
			<input type=text name="cChildren" value="<?=$_REQUEST[cChildren]?>" style="width: 30px">
		</div>	
	 
		
		<input type="submit" name="submit" id="ccm-dashboard-search-button" style="display: none" /> 
		<a href="javascript:void(0)" onclick="$('#ccm-dashboard-search-button').get(0).click()" class="ccm-button-right accept"><span><?=t('Search')?></span></a>
		<div style="clear:both"></div>
	
	</div>
	
</form>
</div>