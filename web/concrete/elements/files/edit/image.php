<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canWrite()) {
	die(t("Access Denied."));
}
?>

<div class="ccm-ui">

<div class="ccm-pane-options">
<form class="clearfix">
<a href="javascript:void(0)" class="btn primary" id="ccm-file-manager-edit-save" style="float: right; margin-left: 10px"><?=t('Save')?></a>
<a href="javascript:void(0)" class="btn" id="ccm-file-manager-edit-restore" style="float: right"><?=t('Undo')?></a>

<div class="span6">
	<label><?=t('Zoom')?></label>
	<div class="input" style="margin-top: 11px">
		<div id="ccm-file-manager-zoom-slider"></div>
	</div>
</div>

<div class="span6">
	<label><?=t('Rotate')?></label>
	<div class="input" style="margin-top: 11px">
		<div id="ccm-file-manager-rotate"></div>
	</div>
</div>

</form>
</div>

<div class="clearfix"></div>


<div id="ccm-file-manager-edit-image">

	<div class="PostContent">
		  <div class="boxes">
			  <div id="crop_container"></div>
			  <div class="cleared"></div> 
		  </div>  
	</div>

</div>

</div>


    <script type="text/javascript">
    $(document).ready(function(){
       var iw = <?=$f->getAttribute('width')?>;
       var ih = <?=$f->getAttribute('height')?>;
	   var w = $('#ccm-file-manager-edit-image').closest('.ui-dialog-content').width();
	   var h = $('#ccm-file-manager-edit-image').closest('.ui-dialog-content').height();
	   if (iw > (w + 20)) {
	   	w = iw;
	   } else {
	   	w = w - 20;
	   }
	   
	   if (ih > (h + 100)) {
	   	h = ih;
	   } else {
	   	h = h - 100;
	   }
	   var cropzoom = $('#crop_container').cropzoom({
            width: w,
            height: h,
            bgColor: '#CCC',
            enableRotation:true,
            enableZoom:true,
            zoomSteps:10,
            rotationSteps:90,
            expose: {
            slidersOrientation: 'horizontal',
            rotationElement: '#ccm-file-manager-rotate',
            zoomElement: '#ccm-file-manager-zoom-slider'
            },
            selector:{        
              centered:true,
              borderColor:'blue',
              borderColorHover:'red'
            },
            image:{
                source:'<?=$f->getRelativePath()?>',
                width: <?=$f->getAttribute('width')?>,
                height:<?=$f->getAttribute('height')?>,
                minZoom:5,
                startZoom: 100,
                maxZoom:300
            }
        });
       cropzoom.setSelector(45,45,200,150,true);
       
       $('#ccm-file-manager-edit-save').click(function(){ 
       		jQuery.fn.dialog.showLoader();
            cropzoom.send('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/image/process','POST',{
            	'fID': <?=$f->getFileID()?>,
            },function(rta){
            	jQuery.fn.dialog.hideLoader();
				highlight = new Array();
				highlight.push(<?=$f->getFileID()?>);
				jQuery.fn.dialog.closeTop();
				ccm_alRefresh(highlight, '<?=$_REQUEST['searchInstance']?>');
            });            
        });
       
       $('#ccm-file-manager-edit-restore').click(function(){
            cropzoom.restore();
        })
    })
</script>
<style type="text/css">
	#img_to_crop{
		-webkit-user-drag: element;
		-webkit-user-select: none;
	}
</style>