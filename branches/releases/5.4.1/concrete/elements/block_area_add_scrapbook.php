<?php  
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">
ccmPrepareScrapbookItemsDelete=function(){
	$("a.ccm-scrapbook-delete").click(function() {
		var id=$(this).attr('id');
		var arHandle=$(this).attr('arHandle');
		var qStr='&ptask=delete_content&arHandle='+encodeURIComponent(arHandle)+'<?php echo $token?>'; 
		
		if( id.indexOf('bID')>-1 ){
			if(!confirm('<?php echo t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>'))
				return false;
			var bID = id.substring(13);
			qStr='bID=' + bID + qStr;
		}else{
			var pcID = id.substring(2);
			qStr='pcID=' + pcID + qStr;
		}  
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: qStr,
			success: function(msg) {
				if(pcID) $("#ccm-pc-" + pcID).fadeOut();
				if(bID)  $("#ccm-scrapbook-list-item-" + bID).fadeOut(); 
			}
		}); 
	});
}

$(function(){ ccmPrepareScrapbookItemsDelete(); });
	
ccmChangeDisplayedScrapbook = function(sel){  
	var scrapbook=$(sel).val(); 
	if(!scrapbook) return false;
	$('#ccm-scrapbookListsWrap').html("<div style='padding:16px;'><?php echo t('Loading...')?></div>");
	$.ajax({
	type: 'POST',
	url: CCM_TOOLS_PATH+"/edit_area_scrapbook_refresh.php",
	data: 'cID=<?php echo intval($_REQUEST['cID'])?>&arHandle=<?php echo urlencode($_REQUEST['arHandle'])?>&scrapbookName='+scrapbook+'&<?php echo $token?>',
	success: function(resp) { 
		$('#ccm-scrapbookListsWrap').html(resp);
		ccmPrepareScrapbookItemsDelete(); 
	}});		
	return false;
}
</script>


<?php  
$u = new User();
$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
$scrapBookAreasData = $scrapbookHelper->getAvailableScrapbooks(); 
$scrapbookName=$_SESSION['ccmLastViewedScrapbook']; 
?>	
<select name="scrapbookName" onchange="ccmChangeDisplayedScrapbook(this)" style=" float:right; margin-top:6px;"> 
	<option value="userScrapbook">
		<?php echo ucfirst($u->getUserName()) ?><?php echo t("'s Scrapbook") ?> 
	</option>
	<?php  foreach($scrapBookAreasData as $scrapBookAreaData){ ?>
		<option value="<?php echo addslashes($scrapBookAreaData['arHandle'])?>" <?php echo ($scrapbookName==$scrapBookAreaData['arHandle'])?'selected':''?>>
			<?php echo $scrapBookAreaData['arHandle'] ?>
		</option>
	<?php  } ?>
</select> 	

<h1><?php echo t('Add From Scrapbook')?></h1>		
	
<div id="ccm-scrapbookListsWrap">
<?php  Loader::element('scrapbook_lists', array( 'c'=>$c, 'a'=>$a, 'scrapbookName'=>$scrapbookName, 'token'=>$token ) );  ?>
</div>