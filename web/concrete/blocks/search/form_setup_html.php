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
				
				/**
				 * Post to another page, get page object.
				 */
				$basePostPage = Null;
				if (isset($searchObj->postTo_cID) && intval($searchObj->postTo_cID) > 0) {
					$basePostPage = Page::getById($searchObj->postTo_cID);
				} else if ($searchObj->pagePath != $c->getCollectionPath() && strlen($searchObj->pagePath)) {
					$basePostPage = Page::getByPath($searchObj->pagePath);
				}
				/**
				 * Verify object.
				 */
				if (is_object($basePostPage) && $basePostPage->isError()) {
					$basePostPage = NULL;
				}
			?>
			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathEverywhere" value="" <?=($searchObj->baseSearchPath=='' || !$searchObj->baseSearchPath)?'checked':''?> onchange="searchBlock.pathSelector(this)" />
				<?=t('everywhere')?>
			</div>

			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathThis" value="<?=$c->getCollectionPath()?>" <?=( $searchObj->baseSearchPath != '' && $searchObj->baseSearchPath==$c->getCollectionPath() )?'checked':''?> onchange="searchBlock.pathSelector(this)" >
				<?=t('beneath this page')?>
			</div>

			<div>
				<input type="radio" name="baseSearchPath" id="baseSearchPathOther" value="OTHER" onchange="searchBlock.pathSelector(this)" <?=($searchWithinOther)?'checked':''?>>
				<?=t('beneath another page')?>
				<div id="basePathSelector" style="display:<?=($searchWithinOther)?'block':'none'?>" >

					<? $form = Loader::helper('form/page_selector');
					if ($searchWithinOther) {
						$cpo = Page::getByPath($baseSearchPath);
						if (is_object($cpo)) {
							print $form->selectPage('searchUnderCID', $cpo->getCollectionID());
						} else {
							print $form->selectPage('searchUnderCID');
						}
					} else {
						print $form->selectPage('searchUnderCID');
					}
					?>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<th><?=t('Results Page')?>:</th>
		<td>
			<div>
				<input id="ccm-searchBlock-externalTarget" name="externalTarget" type="checkbox" value="1" <?=(strlen($searchObj->resultsURL) || $basePostPage !== NULL)?'checked':''?> />
				<?=t('Post to Another Page Elsewhere')?>
			</div>
			<div id="ccm-searchBlock-resultsURL-wrap" style=" <?=(strlen($searchObj->resultsURL) || $basePostPage !== NULL)?'':'display:none'?>" >
				<?
				if ($basePostPage !== NULL) {
					print $form->selectPage('postTo_cID', $basePostPage->getCollectionID());
				} else {
					print $form->selectPage('postTo_cID');
				}
				?>
				<?=t('OR Path')?>:
				<input id="ccm-searchBlock-resultsURL" name="resultsURL" value="<?=$searchObj->resultsURL?>" maxlength="255" type="text" style="width:100%">
			</div>
		</td>
	</tr>
</table>