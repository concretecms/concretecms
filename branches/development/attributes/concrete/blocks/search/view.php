<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<style>
#searchResults .pageLink{ font-size:12px; color:#999; margin:2px 0px 8px 0px; padding:0px; display:block }
#searchResults .searchResult{ margin-bottom:16px; margin-top:24px }
#searchResults .searchResult h3{ margin-bottom:0px; padding-bottom:0px }
#searchResults .searchResult p{margin-top:4px}
</style>

<? if (isset($error)) { ?>
	<?=$error?><br/><br/>
<? } ?>

<form action="<?=$this->url( $resultTargetURL )?>" method="get">

	<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>
	
	<? if(strlen($query)==0){ ?>
	<input name="search_paths[]" type="hidden" value="<?=htmlentities($baseSearchPath, ENT_COMPAT, APP_CHARSET) ?>" />
	<? } else if (is_array($_REQUEST['search_paths'])) { 
		foreach($_REQUEST['search_paths'] as $search_path){ ?>
			<input name="search_paths[]" type="hidden" value="<?=htmlentities($search_path, ENT_COMPAT, APP_CHARSET) ?>" />
	<?  }
	} ?>
	
	<input name="query" type="text" value="<?=htmlentities($query, ENT_COMPAT, APP_CHARSET)?>" />
	
	<input name="submit" type="submit" value="<?=$buttonText?>" />

<? 
$tt = Loader::helper('text');
if (strlen($query)) { 
	if(count($results)==0){ ?>
		<h4 style="margin-top:32px"><?=t('There were no results found. Please try another keyword or phrase.')?></h4>	
	<? }else{ ?>
		<div id="searchResults">
		<? foreach($results as $r) { 
			$currentPageBody = $this->controller->highlightedExtendedMarkup($r->getBodyContent(), $query);?>
			<div class="searchResult">
				<h3><a href="<?=DIR_REL?>/index.php?cID=<?=$r->getID()?>"><?=$r->getName()?></a></h3>
				<p>
					<?php echo ($currentPageBody ? $currentPageBody .'<br />' : '')?>
					<?php echo $this->controller->highlightedMarkup($tt->shortText($r->getDescription()),$query)?>
					<span class="pageLink"><?php echo $this->controller->highlightedMarkup(BASE_URL.DIR_REL.$r->getCollectionPath(),$query)?></span>
				</p>
			</div>
		<? 	}//foreach search result ?>
		</div>
		
		<?
		if($paginator && strlen($paginator->getPages())>0){ ?>	
		<div class="pagination">	
			 <span class="pageLeft"><?=$paginator->getPrevious()?></span>
			 <span class="pageRight"><?=$paginator->getNext()?></span>
			 <?=$paginator->getPages()?>
		</div>	
		<? } ?>

	<?				
	} //results found
} 
?>

</form>