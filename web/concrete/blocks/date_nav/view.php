<?php 
defined('C5_EXECUTE') or die("Access Denied.");

global $c;
$textHelper = Loader::helper("text"); 
$dateHelper = Loader::helper("date"); 
?>

<div id="ccmDateNav<?php echo $bID?>" class="ccmDateNav">
<?php   

$currentPageCID = intval($c->getCollectionID());

//group entries into years and months
$postsByDate=array();
$currentYear=date('Y');
$currentMonth=date('m'); 
foreach($cArray as $page){ 
	$year=date('Y', strtotime($page->getCollectionDatePublic()) );
	$month=date('m', strtotime($page->getCollectionDatePublic()) );
	//echo $year.' '.$month.' '.$page->title.'<br>';
	$postsByDate[$year][$month][]=$page;
}

//print yearly UL if multiple years exist
//don't show the outer years UL with flat display
if( count($postsByDate)>1 && !$controller->flatDisplay ) echo "<ul class='years'> \r \n";
		
$monthDisplayFormat = ($controller->flatDisplay && count($postsByDate)>1) ? 'F Y' : 'F'; 
		
//show the outer months UL with flat display
if($controller->flatDisplay) echo "\t <ul class='months collapsible ".$hideMonths."'> \r \n";		
		
//loop through years 
foreach($postsByDate as $year=>$postsByMonth ){
	if(count($postsByDate)>1 && !$controller->flatDisplay){
		echo "<li class='year'> \r \n";
		//$yearClosed=($currentYear!=$year)?'closed':'';
		echo '<div class="section trigger '.$yearClosed.' year'.$year.'">'.$year."</div> \r \n";
		$hasYears=1;
		//if($hasYears && $currentYear!=$year) $hideMonths='none';
	}
	
	
	//don't show the inner months UL with flat display
	if(!$controller->flatDisplay) echo "\t <ul class='months collapsible ".$hideMonths."'> \r \n";
	
	//print the months that have postings
	foreach($postsByMonth as $month=>$pagesArray){
		
		echo "\t <li class='month'> \r \n";
		//$monthClosed=($currentMonth!=$month || $currentYear!=$year)?'closed':'';
		echo "\t <div class='section trigger ".$monthClosed." month".$month.'_'.$year."'>".$dateHelper->date($monthDisplayFormat,mktime(0,0,0,$month,1,$year))."</div> \r \n"; 
		
		//print this months pages
		//$monthClosed=($currentMonth!=$month || $currentYear!=$year)?'none':'';
		echo "\t\t <ul class='monthsPages collapsible ".$monthClosed."'>";
		$pagesPerNodeCount=0;
		foreach($pagesArray as $page){
			if($currentPageCID==$page->getCollectionId()) $currentPageFound=1;
			$pagesPerNodeCount++;
			$title = $page->getCollectionName();
			$selected=( $page->getCollectionId()==$c->getCollectionID() ) ? 'selected':'';
			//$read=($page->hasBeenViewed(1))?' read':'';
			if($pagesPerNodeCount>$this->display_limit && $this->display_limit!=0){
				echo "\t\t <li class=\"ellipsis\"><a href=\"/contents/\">...</a></li> \r\n"; 
				break;
			}
			
			echo "\t\t <li class='monthsPage pageNode ".$selected." pageId".intval($page->getCollectionId())."' >"; 
			
			echo "\t\t\t <a class=\"".$read."\" href=\"".Loader::helper('navigation')->getLinkToCollection($page)."\">"; 
			if(!$controller->truncateTitles){
				echo $title;
			}else{
				echo $textHelper->shorten($title,$controller->truncateTitleChars);
			}			
			echo "</a>";
			
			if($controller->showDescriptions){ ?>
				<div class="pageSummary">
					<?php  if(!$controller->truncateSummaries){
						echo $page->getCollectionDescription();
					}else{
						echo $textHelper->shorten($page->getCollectionDescription(),$controller->truncateChars);
					} ?>
				</div>
			<?php   }
			echo "\t\t </li> \n \r";					
		}
		echo "\t\t </ul>";
						
		echo "\t </li> \r \n";
	}		
	
	//don't show the inner months UL with flat display 
	if(!$controller->flatDisplay)  echo "\t </ul> \r \n";
		
	if(count($postsByDate)>1) echo "</li> \r \n";
}

//show the outer months UL with flat display
if( $controller->flatDisplay )  echo "\t </ul> \r \n";

//don't show the outer years UL with flat display
if( count($postsByDate)>1 && !$controller->flatDisplay ) echo "</ul> \r \n"; 
?>
</div>

<script type="text/javascript">
<?php  if($controller->defaultNode=='current_month'){ ?>
ccmDateNav.dateKey='<?php echo  date( 'm_Y' ) ?>';
<?php  }else{  // current page
	//if( intval(strtotime($c->getCollectionDatePublic()))>0 ) 
	$createdDate = strtotime($c->getCollectionDatePublic()); 
	//else $createdDate = strtotime($c->cDateAdded); 
	?>
	ccmDateNav.dateKey='<?php echo  date( 'm_Y' , $createdDate ) ?>'; 
	ccmDateNav.loadCurrentPage=<?php echo intval($currentPageFound) ?>;
<?php  } ?> 
ccmDateNav.loadPg=<?php echo intval($c->getCollectionID()) ?>;
$(function(){ ccmDateNav.init(); });
</script>