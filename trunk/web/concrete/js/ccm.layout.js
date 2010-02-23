

// use as an object: 
// var myLayout = new ccmLayout();

function ccmLayout( layout_id, area, locked ){
	
	this.layout_id = layout_id;
	this.locked = locked;
	this.area = area;
	
	
	this.init = function(){ 
	
		var layoutObj=this;
		this.layoutWrapper = $('#ccm-layout-wrapper-'+this.layout_id); 
		this.ccmControls = this.layoutWrapper.find("#ccm-layout-controls-"+this.layout_id);
	
		/*
		this.layoutWrapper.mouseover(function(){
			layoutObj.ccmControls.show(200);
		})
		
		this.ccmControls.mouseout(function(){
			layoutObj.ccmControls.hide(200).delay(5000);
		});
		*/
		
		this.ccmControls.mouseover(function(){ layoutObj.highlightAreas(1); });
		
		this.ccmControls.mouseout(function(){ if(!layoutObj.moving) layoutObj.highlightAreas(0); });
	 	
		this.ccmControls.find('.ccm-layout-menu-button').click(function(e){ 
			layoutObj.optionsMenu(e);
		})
	
		this.gridSizing();
	}
	
	this.highlightAreas=function(show){
		var els=this.layoutWrapper.find('.ccm-add-block');
		if(show) els.addClass('ccm-layout-area-highlight'); 
		else els.removeClass('ccm-layout-area-highlight'); 
	} 
	
	this.optionsMenu=function(e){ 
		
		ccm_hideMenus();
		e.stopPropagation();
		ccm_menuActivated = true;  
		
		// now, check to see if this menu has been made
		var aobj = document.getElementById("ccm-layout-options-menu-" + this.layout_id);
		
		if (!aobj) {
			// create the 1st instance of the menu
			el = document.createElement("DIV");
			el.id = "ccm-layout-options-menu-" + this.layout_id;
			el.className = "ccm-menu";
			el.style.display = "none";
			document.body.appendChild(el);
			
			aobj = $(el);
			aobj.css("position", "absolute");
			
			//contents  of menu
			var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
			html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
			html += '<ul>';
			
			
			html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.editAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuEditLayout' + this.layout_id + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + this.layout_id +  '&atask=layout"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/layout_small.png)">' + ccmi18n.editAreaLayout + '</span></a></li>';
			
			html += '<li><a class="ccm-icon" id="menuAreaLayoutMoveUp' + this.layout_id + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/arrow_up.png)">' + ccmi18n.moveLayoutUp + '</span></a></li>';
						
			html += '<li><a class="ccm-icon" id="menuAreaLayoutMoveDown' + this.layout_id + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/arrow_down.png)">' + ccmi18n.moveLayoutDown + '</span></a></li>';
			
			var lockText = (this.locked) ? ccmi18n.unlockAreaLayout : ccmi18n.lockAreaLayout ; 
			html += '<li><a class="ccm-icon" id="menuAreaLayoutLock' + this.layout_id + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + lockText + '</span></a></li>';
			
			html += '<li><a class="ccm-icon" id="menuAreaLayoutDelete' + this.layout_id + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n.deleteLayout + '</span></a></li>';
			
			html += '</ul>';
			html += '</div></div>';
			html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
			aobj.append(html);
			
			var aJQobj = $(aobj);
			var layoutObj=this;
			
			aJQobj.find('#menuEditLayout' + this.layout_id).dialog(); 
			
			aJQobj.find('#menuAreaLayoutMoveUp' + this.layout_id).click(function(){ layoutObj.moveLayout('up'); }); 
			
			aJQobj.find('#menuAreaLayoutMoveDown' + this.layout_id).click(function(){ layoutObj.moveLayout('down'); }); 
			
			//lock click 
			aJQobj.find('#menuAreaLayoutLock' + this.layout_id).click( function(){ layoutObj.lock(); } ); 
			
			//delete click
			aJQobj.find('#menuAreaLayoutDelete' + this.layout_id).click(function(){ layoutObj.deleteLayout(); }); 
			
			
		
		} else {
			aobj = $("#ccm-layout-options-menu-" + this.layout_id);
		}

		ccm_fadeInMenu(aobj, e);		
	}
	
	this.moveLayout=function(direction){ 
	
		this.moving=1;
		this.highlightAreas(1);
		this.servicesAjax = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + this.layout_id +  '&task=move&direction=' + direction,
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
				}
			}
		});		
		
		var el = $('#ccm-layout-wrapper-'+this.layout_id);
		var layoutObj = this;
		if(direction=='down'){
			var nextLayout = el.next();
			if( nextLayout.hasClass('ccm-layout-wrapper') ){
				el.slideUp(600,function(){
					el.insertAfter(nextLayout);
					el.slideDown(600,function(){ layoutObj.highlightAreas(0); layoutObj.moving=0; }); 
				})
				return;
			}
		}else if(direction=='up'){
			var previousLayout = el.prev();
			if( previousLayout.hasClass('ccm-layout-wrapper') ){ 
				el.slideUp(600,function(){
					el.insertBefore(previousLayout);
					el.slideDown(600,function(){ layoutObj.highlightAreas(0); layoutObj.moving=0; }); 
				})
				return;
			} 
		}
		
		//at boundary, can't move further. 
	}
	
	this.lock=function(lock){  
		var a = $('#menuAreaLayoutLock' + this.layout_id); 
		this.locked = !this.locked;
		if( this.locked ){ 
			a.find('span').html(ccmi18n.unlockAreaLayout);
			if(this.s) this.s.slider( 'disable' ); 
		}else{ 
			a.find('span').html(ccmi18n.lockAreaLayout);
			if(this.s) this.s.slider( 'enable');
		}
		
		var lock = (this.locked) ? 1 : 0;
		this.servicesAjax = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + this.layout_id +  '&task=lock&lock=' + lock,
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
				}
			}
		});	 
	}
	
	this.hasBeenQuickSaved=0;
	this.quickSaveLayoutId=0;
	this.quickSave=function(){  
		var breakPoints=this.ccmControls.find('#layout_col_break_points_'+this.layout_id).val();  
		clearTimeout(this.secondSavePauseTmr);
		if(!this.hasBeenQuickSaved && this.quickSaveInProgress){
			quickSaveLayoutObj=this;
			this.secondSavePauseTmr=setTimeout('quickSaveLayoutObj.quickSave()',100);
			return;
		}
		this.quickSaveInProgress=1;
		var layoutObj = this; 
		var modifyLayoutId = (this.quickSaveLayoutId) ? this.quickSaveLayoutId : this.layout_id; 
		this.quickSaveAjax  = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + modifyLayoutId +  '&task=quicksave&breakpoints='+encodeURI(breakPoints),
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
					layoutObj.hasBeenQuickSaved=1;
					layoutObj.quickSaveInProgress=0;
					if(jObj.layoutID){
						layoutObj.quickSaveLayoutId = jObj.layoutID;
					}
				}
			}
		}); 
	}
	
	this.deleteLayout=function(){  
															
		ccm_hideMenus();  
		 
		if( !confirm( ccmi18n.deleteLayoutConfirmMsg ) ) return false; 
		
		this.layoutWrapper.slideUp(300); 
		 
		var layoutId = this.layout_id;
		this.servicesAjax = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + this.layout_id +  '&task=delete',
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
					$('#ccm-layout-wrapper-'+layoutId).remove();
				}
			}
		});	
		
	}	


	this.gridSizing = function(){
		this.ccmGrid=$("#ccm-layout-"+this.layout_id); 
		
		//append layout id to start of all selectors
		var cols=parseInt( this.ccmControls.find('.layout_column_count').val() );  
		
		if(cols>1){ 
			var startPoints=this.ccmControls.find('#layout_col_break_points_'+this.layout_id).val().replace(/%/g,'').split('|');  
			
			this.s = this.ccmControls.find(".ccm-layout-controls-slider");
			
			this.s.get(0).layoutObj=this;
			this.s.get(0).ccmGrid=this.ccmGrid;
			
			this.s.slider( { 
				step: 1, 
				values: startPoints,
				change: function(){  
					this.layoutObj.resizeGrid(this.childNodes); 
					var breakPoints=[];			
					for(var i=0;i<this.childNodes.length;i++)
						breakPoints.push( parseFloat(this.childNodes[i].style.left.replace('%','')) );
						
					breakPoints.sort( function(a, b){ return (a-b); } );
						
					this.layoutObj.ccmControls.find('.layout_col_break_points').val( breakPoints.join('%|')+'%' ); 
					this.layoutObj.quickSave();
				},
				slide:function(){ 			 
					this.layoutObj.resizeGrid(this.childNodes); 
				}
			});
			if( parseInt(this.ccmControls.find('.layout_locked').val()) ) this.s.slider( 'disable' );
		}	
	}
		
		
	this.resizeGrid=function(childNodes){	
		/*
		//item list type 
		var pos=parseInt(childNodes[0].style.left.replace('%',''));
		if(this.ccmGrid.hasClass('ccm-layout-type-itemlist')){ 
			this.ccmGrid.find('.ccm-layout-cell-left').css('width',pos+'%');
			this.ccmGrid.find('.ccm-layout-cell-right').css('width',(99-pos)+'%');
		}
		
		//staggered type 
		if(this.ccmGrid.hasClass('ccm-layout-type-staggered')){ 
			this.ccmGrid.find('.ccm-layout-row-odd .ccm-layout-cell-short').css('width',pos+'%');
			this.ccmGrid.find('.ccm-layout-row-odd .ccm-layout-cell-long').css('width',(99-pos)+'%');
			var pos2=parseInt(childNodes[1].style.left.replace('%',''));
			this.ccmGrid.find('.ccm-layout-row-even .ccm-layout-cell-long').css('width',pos2+'%');
			this.ccmGrid.find('.ccm-layout-row-even .ccm-layout-cell-short').css('width',(99-pos2)+'%');
		}
		*/

		//column & table type
		//if(this.ccmGrid.hasClass('ccm-layout-type-columns') || this.ccmGrid.hasClass('ccm-layout-type-table')){ 
			var i, positions=[]; 					
			for(i=0;i<childNodes.length;i++){ 
				var pos=parseFloat(childNodes[i].style.left.replace('%',''));
				positions.push(pos);
			}
			positions.sort( function(a, b){ return (a-b); } );
		
			var prevW=0;
			var i; 					
			for(i=0;i<positions.length;i++){ 
				var pos=positions[i];
				var w=pos-prevW;
				prevW+=w;
				this.ccmGrid.find('#ccm-layout-'+this.layout_id+'-col-'+(i+1)).css('width',w+'%');						
			}
			this.ccmGrid.find('#ccm-layout-'+this.layout_id+'-col-'+(i+1)).css('width',(100-prevW)+'%');  
		//}
	}
	
} 

var quickSaveLayoutObj;