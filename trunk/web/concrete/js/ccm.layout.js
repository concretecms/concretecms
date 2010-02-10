

// use as an object: 
// var myLayout = new ccmLayout();

function ccmLayout( layout_id, area, locked ){
	
	this.layout_id = layout_id;
	this.locked = locked;
	this.area = area;
	
	this.init = function(){ 
	
		this.ccmControls=$("#ccm-layout-controls-"+this.layout_id);
	 
	 	var layoutOut=this;
		this.ccmControls.find('.ccm-layout-menu-button').click(function(e){ 
			layoutOut.optionsMenu(e);
		})
	
		this.gridSizing();
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
			
			
			html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.editAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuEditLayout' + this.layout_id + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + this.area + '&layoutID=' + this.layout_id +  '&atask=layout"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">' + ccmi18n.editAreaLayout + '</span></a></li>';
			
			
			if(this.locked){
				html += '<li><a class="ccm-icon ccm-icon-disabled" id="menuAreaLayoutLock' + this.layout_id + '" href="#"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + ccmi18n.areaLayoutLocked + '</span></a></li>';
			}else{
				html += '<li><a class="ccm-icon" id="menuAreaLayoutLock' + this.layout_id + '" href=""><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + ccmi18n.lockAreaLayout + '</span></a></li>';
			}
			
			html += '<li><a class="ccm-icon" id="menuAreaLayouDelete' + this.layout_id + '" href="#"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n.deleteLayout + '</span></a></li>';
			
			html += '</ul>';
			html += '</div></div>';
			html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
			aobj.append(html);
			
			var aJQobj = $(aobj);
			
			aJQobj.find('#menuEditLayout' + this.layout_id).dialog(); 
			
			//delete click
			aJQobj.find('#menuAreaLayouDelete' + this.layout_id).click(function(){
				ccm_hideMenus();
				if( !confirm(ccmi18n.deleteLayoutConfirmMsg) ){ 
					return false;
				}
				alert('delete logic here');
				return false;
			}); 
			
		
		} else {
			aobj = $("#ccm-layout-options-menu-" + this.layout_id);
		}

		ccm_fadeInMenu(aobj, e);		
	}
	
	this.quickSave=function(){
		alert('quick save');	
	}

	this.gridSizing = function(){
		this.ccmGrid=$("#ccm-layout-"+this.layout_id); 
		
		//append layout id to start of all selectors
		var cols=parseInt( this.ccmControls.find('.layout_column_count').val() );  
		
		if(cols>1){
			var startPoints=this.ccmControls.find('.layout_col_break_points').val().replace(/%/g,'').split('|');  
			
			var s = this.ccmControls.find(".ccm-layout-controls-slider");
			
			s.get(0).layoutObj=this;
			s.get(0).ccmGrid=this.ccmGrid;
			
			s.slider( { 
				step: 1, 
				values: startPoints,
				change: function(){  
					var breakPoints=[];			
					for(var i=0;i<this.childNodes.length;i++)
						breakPoints.push(this.childNodes[i].style.left); 
					parseInt( this.layoutObj.ccmControls.find('.layout_col_break_points').val( breakPoints.join('|') )); 
					this.layoutObj.quickSave();
				},
				slide:function(){ 				
					//item list type
					var pos=parseInt(this.childNodes[0].style.left.replace('%',''));
					if(this.ccmGrid.hasClass('ccm-layout-type-itemlist')){ 
						this.ccmGrid.find('.ccm-layout-cell-left').css('width',pos+'%');
						this.ccmGrid.find('.ccm-layout-cell-right').css('width',(99-pos)+'%');
					}
					
					//staggered type 
					if(this.ccmGrid.hasClass('ccm-layout-type-staggered')){ 
						this.ccmGrid.find('.ccm-layout-row-odd .ccm-layout-cell-short').css('width',pos+'%');
						this.ccmGrid.find('.ccm-layout-row-odd .ccm-layout-cell-long').css('width',(99-pos)+'%');
						var pos2=parseInt(this.childNodes[1].style.left.replace('%',''));
						this.ccmGrid.find('.ccm-layout-row-even .ccm-layout-cell-long').css('width',pos2+'%');
						this.ccmGrid.find('.ccm-layout-row-even .ccm-layout-cell-short').css('width',(99-pos2)+'%');
					}			

					//column & table type
					if(this.ccmGrid.hasClass('ccm-layout-type-columns') || this.ccmGrid.hasClass('ccm-layout-type-table')){ 
						var prevW=0;
						var i; 					
						for(i=0;i<this.childNodes.length;i++){
							var pos=parseInt(this.childNodes[i].style.left.replace('%',''));
							var w=pos-prevW;
							prevW+=w;
							this.ccmGrid.find('.ccm-layout-col-'+(i+1)).css('width',w+'%');						
						}
						this.ccmGrid.find('.ccm-layout-col-'+(i+1)).css('width',(100-prevW)+'%');  
					}
				}
			});
			if( parseInt(this.ccmControls.find('.layout_locked').val()) ) s.slider( 'disable' );
		}	
	}
		
}
