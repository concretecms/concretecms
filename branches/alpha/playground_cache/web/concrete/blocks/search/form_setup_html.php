<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<style>
table#searchBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top }
table#searchBlockSetup td{ font-size:12px; vertical-align:top }
table#searchBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style> 

<? if (!$controller->indexExists()) { ?>
	<div class="ccm-error">The search index does not appear to exist. This block will not function until the reindex job has been run at least once in the dashboard.<br/><br/></div>
<? } ?>
<table id="searchBlockSetup" width="100%"> 
	<tr>
		<th>Search Title:</th>
		<td><input id="ccm_search_block_title" name="title" value="<?=$searchObj->title?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>	
	<tr>
		<th>Submit Button Text:</th>
		<td><input name="buttonText" value="<?=$searchObj->buttonText?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>
	<tr>
		<th>Search Within Path:</th>
		<td> 
			<?
			$searchWithinOther=($searchObj->baseSearchPath!=$c->getCollectionPath() && $searchObj->baseSearchPath!='' && strlen($searchObj->baseSearchPath)>0)?true:false;
			?>
			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathEverywhere" value="" <?=($searchObj->baseSearchPath=='' || !$searchObj->baseSearchPath)?'checked':''?> onchange="searchBlock.pathSelector(this)" />
				everywhere
			</div>
				
			<div> 
				<input type="radio" name="baseSearchPath" id="baseSearchPathThis" value="<?=$c->getCollectionPath()?>" <?=( $searchObj->baseSearchPath==$c->getCollectionPath() )?'checked':''?> onchange="searchBlock.pathSelector(this)" >
				beneath this page
			</div>
				
			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathOther" value="OTHER" onchange="searchBlock.pathSelector(this)" <?=($searchWithinOther)?'checked':''?>>
				beneath another page	
				<div id="basePathSelector" style="display:<?=($searchWithinOther)?'block':'none'?>" >
					<a id="ccm-sitemap-select-page" class="dialog-launch" dialog-width="600" dialog-height="450" dialog-modal="false" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page">Select Page</a>
					<input type="hidden" name="searchUnderCID" id="searchUnderCID" value="">
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<th>Results Page:</th>
		<td>
			<div>
				<input id="ccm-searchBlock-externalTarget" name="externalTarget" type="checkbox" value="1" <?=(strlen($searchObj->resultsURL))?'checked':''?> />
				Post to Another Page Elsewhere
			</div>
			<div id="ccm-searchBlock-resultsURL-wrap" style=" <?=(strlen($searchObj->resultsURL))?'':'display:none'?>" >
				<input id="ccm-searchBlock-resultsURL" name="resultsURL" value="<?=$searchObj->resultsURL?>" maxlength="255" type="text" style="width:100%">
				<div>ex. /search-results/</div>
			</div>
		</td>
	</tr>			
</table>