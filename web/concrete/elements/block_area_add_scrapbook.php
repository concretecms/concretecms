<? 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">
ccmPrepareScrapbookItemsDelete=function(){
	$("a.ccm-scrapbook-delete").click(function() {
		var id=$(this).attr('id');
		var arHandle=$(this).attr('arHandle');
		var qStr='&ptask=delete_content&arHandle='+encodeURIComponent(arHandle)+'<?=$token?>'; 
		
		if( id.indexOf('bID')>-1 ){
			if(!confirm('<?=t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>'))
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
				if(pcID) $("#ccm-pc-" + pcID).slideUp({easing: 'easeOutExpo', duration: 300});
				if(bID)  $("#ccm-scrapbook-list-item-" + bID).fadeOut.slideUp({easing: 'easeOutExpo', duration: 300}); 
			}
		}); 
	});
}

$(function(){ ccmPrepareScrapbookItemsDelete(); });
	
</script>


<div id="ccm-scrapbookListsWrap">
<? Loader::element('scrapbook_lists', array( 'c'=>$c, 'a'=>$a, 'scrapbookName'=>'userScrapbook', 'token'=>$token ) );  ?>
</div>