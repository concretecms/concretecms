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

<form action="<?=$this->url( $resultTargetURL )?>" method="post">

	<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>
	
	<? if(strlen($query)==0){ ?>
	<input name="search_paths[]" type="hidden" value="<?=$baseSearchPath?>" />
	<? } else if (is_array($_REQUEST['search_paths'])) { 
		foreach($_REQUEST['search_paths'] as $search_path){ ?>
			<input name="search_paths[]" type="hidden" value="<?=$search_path?>" />
	<?  }
	} ?>
	
	<input name="query" type="text" value="<?=$query?>" />
	
	<input name="submit" type="submit" value="<?=$buttonText?>" />

<? 
if (strlen($query)) { 
	if(count($results)==0){ ?>
		<h4 style="margin-top:32px"><?=t('There were no results found. &nbsp;Please try another keyword or phrase.')?></h4>	
	<? }else{ ?>
		<div id="searchResults">
		<? foreach($results as $r) { ?>
			<div class="searchResult">
				<h3><a href="<?=DIR_REL?>/index.php?cID=<?=$r->getID()?>"><?=$r->getName()?></a></h3>
				<p>
					<?=$r->getDescription()?>
					<span class="pageLink"><?=BASE_URL.$r->getCPath() ?></span>
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