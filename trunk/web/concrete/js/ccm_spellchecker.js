var SpellChecker={

	checkField:function(field,trigger,editingTxt){
		
		
		if(field.toString() === field) { // if it's a string, we'll consider it an id
			var f = $('#'+field); 
		} else {
			var f = field; // expecting jquery element
			field = f.attr('id');
		}
		
		//determine and display mode
		if(!editingTxt) editingTxt= ccmi18n_spellchecker.resumeEditing; //'<img src="'+CCM_REL+'/images/btn_resume_editing.gif" />';
		
		//second click - switch back to edit mode 
		if( trigger.innerHTML.indexOf(ccmi18n_spellchecker.resumeEditing)>=0 ) {
			trigger.innerHTML=trigger.initTxt;
			f.css('display','block');
			$('#'+field+'SuggestBox').css('display','none');
			return false;
		}
		
		trigger.initTxt=trigger.innerHTML;
		trigger.innerHTML=editingTxt;
		
		//query spelling suggestions
		qStr='fieldId='+field+'&txt='+f.val();	
		var url=CCM_TOOLS_PATH+"/spellchecker_service.php";
		//CCM_REL+'/concrete/tools/spellchecker_service.php'
		$.ajax({type:"POST",url:url,data:qStr,
			success: function(json){
				eval('var jobj='+json);
				SpellChecker.suggestMode(jobj,f);
			}
		});
	},

	suggestMode:function(jobj,field){ 
		if(!jobj || !jobj.fieldId) return false;

		var f = field; // expecting jquery element
		field = f.attr('id');
		
		//swap text area / field with suggest box div
		var suggestId=jobj.fieldId+'SuggestBox';
		suggestBox=document.getElementById(suggestId)
		if( !suggestBox ){
			suggestBox=document.createElement('div');
			suggestBox.id=suggestId;
			suggestBox.className='spellingSuggestBox';
			suggestBox.onclick=function(){ 
				if(this.popupShown!=true) return false;
				var pu=$('#suggestPopup',this); 
				if(pu.css('display')=='block'){  
					pu.css('display','none'); 
				}
				this.popupShown=false; 
			}
			suggestBox.style.height=f.height()+'px';
			suggestBox.style.width=f.width()+'px';
			suggestBox.masterRegion=f;
			f.after(suggestBox);
		}else suggestBox.style.display='block';
		suggestBox.innerHTML=jobj.html;
		var correctedHTML=$('.correctedHTML',suggestBox);
		correctedHTML.css('height',f.height()+'px' ); 
		correctedHTML.css('width',f.width() );
		f.css('display','none');
		suggestBox.suggestions = jobj.suggestions;
		
		//add misspelled words suggestions
		$('.misspelled',suggestBox).each( function(pos,el){
			el.wordNumber=el.id.substring(14);
			el.onclick=function(){ SpellChecker.suggestPopup( this ) }; 
		});
	},

	suggestPopup:function(el){
		el.parentNode.parentNode.popupShown=false;		
		var pos=$(el).position();
		var popup=$('#suggestPopup',el.parentNode.parentNode);
		popup.css('top',pos.top+$(el).height()+2);
		popup.css('left',pos.left);
		//eval('var suggestions=el.parentNode.parentNode.suggestions.'+el.innerHTML); 
		eval('var suggestions=el.parentNode.parentNode.suggestions.word'+el.wordNumber); 
		
		var html='';
		for(var i=0;i<suggestions.length;i++){
			if(suggestions[i].length==0) continue;
			var params='\''+el.parentNode.parentNode.id+'\',\''+this.addSlashes(el.innerHTML)+'\',\''+this.addSlashes(suggestions[i])+'\','+parseInt(el.wordNumber); 
			var newhtml='<div class="suggestion"><a onclick="SpellChecker.replaceWord('+(params)+')">'+suggestions[i]+"</a></div>";
			html=html+newhtml; 
			if(i>8) break;			
		}
		if(html=='') html='<div>' + ccmi18n_spellchecker.noSuggestions + '</div>'; 
		popup.html(html);
		popup.css('display','block');
		setTimeout( 'document.getElementById("'+el.parentNode.parentNode.id+'").popupShown=true;',5 );
	},

	replaceWord:function(suggestBoxId,originalWord,newWord,wordNumber){
		var suggestBox=document.getElementById(suggestBoxId);
		var word=$('#misspelledWord'+wordNumber,suggestBox);
		word.html(newWord);
		word.addClass("fixed");
		word.click(function(){})
		var popup=$('#suggestPopup',suggestBox);
		popup.html('');
		var tarea = document.createElement('textarea');
		
		var tempDiv=document.createElement('div');
		tempDiv.innerHTML=suggestBox.innerHTML;
		$('.correctedHTML',tempDiv).each(function(i,el){ $(el).after(el.innerHTML); $(el).remove() })
		$('.misspelled',tempDiv).each(function(i,el){ $(el).after(el.innerHTML); $(el).remove() })
		$('#suggestPopup',tempDiv).each(function(i,el){ $(el).remove() })		
		tarea.innerHTML = tempDiv.innerHTML.replace(/(<(br[^>]?)>)/ig," \r\n");	
		suggestBox.masterRegion.val( tarea.value ); 
		popup.css('display','none'); 
		suggestBox.popupShown=false;
	},
	
	addSlashes:function(str){
		return str.replace(/\'/g,"\\'");
	}
}