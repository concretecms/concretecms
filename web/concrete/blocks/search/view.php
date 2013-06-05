<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($error)) { ?>
	<?=$error?><br/><br/>
<? } ?>

<form action="<?=$this->url( $resultTargetURL )?>" method="get" class="ccm-search-block-form">

	<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>
	
	<? if(strlen($query)==0){ ?>
	<input name="search_paths[]" type="hidden" value="<?=htmlentities($baseSearchPath, ENT_COMPAT, APP_CHARSET) ?>" />
	<? } else if (is_array($_REQUEST['search_paths'])) { 
		foreach($_REQUEST['search_paths'] as $search_path){ ?>
			<input name="search_paths[]" type="hidden" value="<?=htmlentities($search_path, ENT_COMPAT, APP_CHARSET) ?>" />
	<?  }
	} ?>
	
	<input name="query" type="text" value="<?=htmlentities($query, ENT_COMPAT, APP_CHARSET)?>" class="ccm-search-block-text" />
	
	<input name="submit" type="submit" value="<?=$buttonText?>" class="ccm-search-block-submit" />

<? 
$tt = Loader::helper('text');
if ($do_search) {
	if(count($results)==0){ ?>
		<h4 style="margin-top:32px"><?=t('There were no results found. Please try another keyword or phrase.')?></h4>	
	<? }else{ ?>
		<div id="searchResults">
		<? foreach($results as $r) { 
			$currentPageBody = $this->controller->highlightedExtendedMarkup($r->getBodyContent(), $query);?>
			<div class="searchResult">
				<h3><a href="<?=$r->getPath()?>"><?=$r->getName()?></a></h3>
				<p>
					<? if ($r->getDescription()) { ?>
						<?php  echo $this->controller->highlightedMarkup($tt->shortText($r->getDescription()),$query)?><br/>
					<? } ?>
					<?php echo $currentPageBody; ?>
					<a href="<?php  echo $r->getPath(); ?>" class="pageLink"><?php  echo $this->controller->highlightedMarkup($r->getPath(),$query)?></a>
				</p>
			</div>
		<? 	}//foreach search result ?>
		</div>
		
		<?
		if($paginator && strlen($paginator->getPages())>0){ ?>	
		<div class="ccm-pagination">	
			 <span class="ccm-page-left"><?=$paginator->getPrevious()?></span>
			 <?=$paginator->getPages()?>
			 <span class="ccm-page-right"><?=$paginator->getNext()?></span>
		</div>	
		<? } ?>

	<?				
	} //results found
} 
?>

</form>