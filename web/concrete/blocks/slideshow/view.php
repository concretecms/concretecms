<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/javascript">
//<![CDATA[
var ccmSlideShowHelper<?=intval($bID)?> = {

	bID:<?=intval($bID)?>,
	imgNum:0,
	
	init:function(){
		this.displayWrap=$('#ccm-SlideshowBlock-display'+this.bID); 
		if(this.imgInfos.length==0){
			//alert('There are no images in this slideshow');
			return false;
		}
		var maxHeight=0;
		for(var i=0;i<this.imgInfos.length;i++){
			this.addImg(i);
			if(maxHeight==0 || this.imgInfos[i].imgHeight > maxHeight)
				maxHeight=this.imgInfos[i].imgHeight;
		}
		this.displayWrap.css('height',maxHeight);

		
		//center images
		for(var i=0;i<this.imgInfos.length;i++){ 
			if( this.imgInfos[i].imgHeight < maxHeight){
				var t=((maxHeight - this.imgInfos[i].imgHeight)/2);
				this.imgEls[i].css('top',t);
			}
		}
		this.nextImg();
	}, 
	nextImg:function(){ 
		if(this.imgNum>=this.imgInfos.length) this.imgNum=0;  
		this.imgEls[this.imgNum].css('opacity',0);
		this.imgEls[this.imgNum].css('display','block');
		this.imgEls[this.imgNum].animate({opacity:1},
			this.imgInfos[this.imgNum].fadeDuration*1000,'',function(){ccmSlideShowHelper<?=intval($bID)?>.preparefadeOut()});
		var prevNum=this.imgNum-1;
		if(prevNum<0) prevNum=this.imgInfos.length-1;
		if(this.imgInfos.length==1) return;
		this.imgEls[prevNum].animate({opacity:0},this.imgInfos[this.imgNum].fadeDuration*800,function(){this.style.zIndex=1;});			
	},
	preparefadeOut:function(){
		if(this.imgInfos.length==1) return;
		var milisecDuration=parseInt(this.imgInfos[this.imgNum].duration)*1000;
		this.imgEls[this.imgNum].css('z-index',2);
		setTimeout('ccmSlideShowHelper'+<?=intval($bID)?>+'.nextImg();',milisecDuration);
		this.imgNum++;
	},
	maxHeight:0,
	imgEls:[],
	addImg:function(num){
		var el=document.createElement('div');
		el.id="slideImgWrap"+num;
		el.className="slideImgWrap"; 
		if(this.imgInfos[num].fullFilePath.length>0)
			 imgURL=this.imgInfos[num].fullFilePath;
		else imgURL='<?=REL_DIR_FILES_UPLOADED?>/'+this.imgInfos[num].fileName; 
		//el.innerHTML='<img src="'+imgURL+'" >';
		el.innerHTML='<div style="height:'+this.imgInfos[num].imgHeight+'px; background:url(\''+escape(imgURL)+'\') center no-repeat">&nbsp;</div>';
		//alert(imgURL);
		if(this.imgInfos[num].url.length>0) {
			//el.linkURL=this.imgInfos[num].url;
			var clickEvent='onclick="return ccmSlideShowHelper<?=intval($bID)?>.imgClick( this.href  );"';
			el.innerHTML='<a href="'+this.imgInfos[num].url+'" '+clickEvent+' >'+el.innerHTML+'</a>';			
		}
		el.style.display='none';
		this.displayWrap.append(el);
		var jqEl=$(el);
		this.imgEls.push(jqEl);
	},
	imgClick:function(linkURL){
		//override for custom behavior
	},
	imgInfos:[
	<? 
	$notFirst=1;
	foreach($images as $imgInfo) {
		$f = File::getByID($imgInfo['fID']);
		$fp = new Permissions($f);
		if ($fp->canViewFile()) {
			if(!$notFirst) echo ',';
			$notFirst=0
			?>
			{
				fileName:"<?=$f->getFileName()?>",
				fullFilePath:"<?=$f->getRelativePath()?>",
				duration:<?=intval($imgInfo['duration'])?>,
				fadeDuration:<?=intval($imgInfo['fadeDuration'])?>,		
				url:"<?=$imgInfo['url']?>",
				groupSet:<?=intval($imgInfo['groupSet'])?>,
				imgHeight:<?=intval($imgInfo['imgHeight'])?>
			}
		<? }
		} ?>
	]
}
$(function(){ccmSlideShowHelper<?=intval($bID)?>.init();}); 
//]]>
</script>

<div id="ccm-SlideshowBlock-display<?=intval($bID)?>" class="ccm-SlideshowBlock-display">
<div id="ccm-SlideshowBlock-heightSetter<?=intval($bID)?>" class="ccm-SlideshowBlock-heightSetter"></div>
<div class="ccm-SlideshowBlock-clear" ></div>
</div>
