<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 

<?php  if (isset($error)) { ?>
	<?php echo $error?><br/><br/>
<?php  } ?>

<form action="<?php echo $this->url( $resultTargetURL )?>" method="get">

	<?php  if( strlen($title)>0){ ?><h3><?php echo $title?></h3><?php  } ?>
	
	<?php  if(strlen($query)==0){ ?>
	<input name="search_paths[]" type="hidden" value="<?php echo htmlentities($baseSearchPath, ENT_COMPAT, APP_CHARSET) ?>" />
	<?php  } else if (is_array($_REQUEST['search_paths'])) { 
		foreach($_REQUEST['search_paths'] as $search_path){ ?>
			<input name="search_paths[]" type="hidden" value="<?php echo htmlentities($search_path, ENT_COMPAT, APP_CHARSET) ?>" />
	<?php   }
	} ?>
	
	<input name="query" type="text" value="<?php echo htmlentities($query, ENT_COMPAT, APP_CHARSET)?>" />
	
	<input name="submit" type="submit" value="<?php echo $buttonText?>" />

<?php  
$tt = Loader::helper('text');
if ($do_search) {
	if(count($results)==0){ ?>
		<h4 style="margin-top:32px"><?php echo t('There were no results found. Please try another keyword or phrase.')?></h4>	
	<?php  }else{ ?>
		<div id="searchResults">
		<?php  foreach($results as $r) { 
			$currentPageBody = $this->controller->highlightedExtendedMarkup($r->getBodyContent(), $query);?>
			<div class="searchResult">
				<h3><a href="<?php echo $r->getPath()?>"><?php echo $r->getName()?></a></h3>
				<p>
					<?php  echo ($currentPageBody ? $currentPageBody .'<br />' : '')?>
					<?php  echo $this->controller->highlightedMarkup($tt->shortText($r->getDescription()),$query)?>
					<span class="pageLink"><?php  echo $this->controller->highlightedMarkup($r->getPath(),$query)?></span>
				</p>
			</div>
		<?php  	}//foreach search result ?>
		</div>
		
		<?php 
		if($paginator && strlen($paginator->getPages())>0){ ?>	
		<div class="pagination">	
			 <span class="pageLeft"><?php echo $paginator->getPrevious()?></span>
			 <span class="pageRight"><?php echo $paginator->getNext()?></span>
			 <?php echo $paginator->getPages()?>
		</div>	
		<?php  } ?>

	<?php 				
	} //results found
} 
?>

</form>