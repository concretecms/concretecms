<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
table#searchBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top }
table#searchBlockSetup td{ font-size:12px; vertical-align:top }
table#searchBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style>

<? if (!$controller->indexExists()) { ?>
	<div class="ccm-error"><?=t('The search index does not appear to exist. This block will not function until the reindex job has been run at least once in the dashboard.')?><br/><br/></div>
<? } ?>
<table id="searchBlockSetup" width="100%">
	<tr>
		<th><?=t('Search Title')?>:</th>
		<td><input id="ccm_search_block_title" name="title" value="<?=$searchObj->title?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>
	<tr>
		<th><?=t('Submit Button Text')?>:</th>
		<td><input name="buttonText" value="<?=$searchObj->buttonText?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>
	<tr>
		<th><?=t('Search Within Path')?>:</th>
		<td>
			<?
			$searchWithinOther=($searchObj->baseSearchPath!=$c->getCollectionPath() && $searchObj->baseSearchPath!='' && strlen($searchObj->baseSearchPath)>0)?true:false;
			$alternatPagePath=($searchObj->pagePath!=$c->getCollectionPath() && $searchObj->pagePath!='' && strlen($searchObj->pagePath)>0)?true:false;
			?>
			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathEverywhere" value="" <?=($searchObj->baseSearchPath=='' || !$searchObj->baseSearchPath)?'checked':''?> onchange="searchBlock.pathSelector(this)" />
				<?=t('everywhere')?>
			</div>

			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathThis" value="<?=$c->getCollectionPath()?>" <?=( $searchObj->baseSearchPath==$c->getCollectionPath() )?'checked':''?> onchange="searchBlock.pathSelector(this)" >
				<?=t('beneath this page')?>
			</div>

			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathOther" value="OTHER" onchange="searchBlock.pathSelector(this)" <?=($searchWithinOther)?'checked':''?>>
				<?=t('beneath another page')?>
				<div id="ccm-searchBlock-resultsURL-wrap" style=" <?=(strlen($searchObj->resultsURL))?'':'display:none'?>">

					<?
					if ($alternatPagePath) {
						$cpo = Page::getByPath($pagePath);
						print $form->selectPage('pagePath', $cpo->getCollectionID());
					} else {
						print $form->selectPage('pagePath');
					}
					?>
					OR  Path:
					<input id="ccm-searchBlock-resultsURL" name="resultsURL" value="<?=$searchObj->resultsURL?>" maxlength="255" type="text" style="width:100%">
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<th><?=t('Results Page')?>:</th>
		<td>
			<div>
				<input id="ccm-searchBlock-externalTarget" name="externalTarget" type="checkbox" value="1" <?=(strlen($searchObj->resultsURL))?'checked':''?> />
				<?=t('Post to Another Page Elsewhere')?>
			</div>
			<div id="ccm-searchBlock-resultsURL-wrap" style=" <?=(strlen($searchObj->resultsURL))?'':'display:none'?>" >
				<input id="ccm-searchBlock-resultsURL" name="resultsURL" value="<?=$searchObj->resultsURL?>" maxlength="255" type="text" style="width:100%">
			</div>
		</td>
	</tr>
</table>