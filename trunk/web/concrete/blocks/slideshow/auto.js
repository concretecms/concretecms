
var SlideshowBlock = {
	
	init:function(){},	
	
	chooseImg:function(){ 
		jQuery.fn.dialog.open({ 
			width: 650,
			height: 450,
			modal: false,
			href: CCM_TOOLS_PATH + "/al.php?launch_in_page=1&cID="+0,
			title: "Choose File/Image"
		});
	},
	
	selectImg:function(obj){
		//alert( $.toJSON(obj) );			
		this.addNewImage(obj.bID,obj.thumbPath,obj.fileName,obj.height,obj.origfilename);
	},

	addImages:0, 
	addNewImage: function(bID, thumbPath, thumbName, imgHeight, origFileName) { 
		this.addImages--; //negative counter - so it doesn't compete with real slideshowImgIds
		var slideshowImgId=this.addImages;
		var templateHTML=$('#imgRowTemplateWrap .ccm-slideshowBlock-imgRow').html().replace(/tempBID/g,bID)
		templateHTML=templateHTML.replace(/tempFilename/g,thumbName).replace(/tempThumbPath/g,thumbPath).replace(/tempOrigFilename/g,origFileName);
		templateHTML=templateHTML.replace(/tempSlideshowImgId/g,slideshowImgId).replace(/tempHeight/g,imgHeight);
		var imgRow = document.createElement("div");
		imgRow.innerHTML=templateHTML;
		imgRow.id='ccm-slideshowBlock-imgRow'+parseInt(slideshowImgId);	
		imgRow.className='ccm-slideshowBlock-imgRow';
		document.getElementById('ccm-slideshowBlock-imgRows').appendChild(imgRow);
		var bgRow=$('#ccm-slideshowBlock-imgRow'+parseInt(bID)+' .backgroundRow');
		bgRow.css('background','url('+thumbPath+') no-repeat left top');
	},
	
	removeImage: function(bID){
		//$('#ccm-slideshowBlock-imgRow'+parseInt(bID)).hide();
		$('#ccm-slideshowBlock-imgRow'+parseInt(bID)).remove();
	},
	
	moveUp:function(thisImgID){
		var thisImg=$('#ccm-slideshowBlock-imgRow'+thisImgID);
		var qIDs=this.serialize();
		var previousQID=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisImgID){
				if(previousQID==0) break; 
				thisImg.after($('#ccm-slideshowBlock-imgRow'+previousQID));
				break;
			}
			previousQID=qIDs[i];
		}	 
	},
	moveDown:function(thisImgID){
		var thisImg=$('#ccm-slideshowBlock-imgRow'+thisImgID);
		var qIDs=this.serialize();
		var thisQIDfound=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisImgID){
				thisQIDfound=1;
				continue;
			}
			if(thisQIDfound){
				$('#ccm-slideshowBlock-imgRow'+qIDs[i]).after(thisImg);
				break;
			}
		} 
	},
	serialize:function(){
		var t = document.getElementById("ccm-slideshowBlock-imgRows");
		var qIDs=[];
		for(var i=0;i<t.childNodes.length;i++){ 
			if( t.childNodes[i].className && t.childNodes[i].className.indexOf('ccm-slideshowBlock-imgRow')>=0 ){ 
				var qID=t.childNodes[i].id.replace('ccm-slideshowBlock-imgRow','');
				qIDs.push(qID);
			}
		}
		return qIDs;
	},	

	validate:function(){
		var failed=0; 
		
		qIDs=this.serialize();
		if( qIDs.length<2 ){
			alert('Please add at least two images.');
			$('#ccm-slideshowBlock-AddImg').focus();
			failed=1;
		}	
		
		if(failed){
			ccm_isBlockError=1;
			return false;
		}
		return true;
	} 
}

ccmValidateBlockForm = function() { return SlideshowBlock.validate(); }
ccm_chooseAsset = function(obj) { SlideshowBlock.selectImg(obj); }