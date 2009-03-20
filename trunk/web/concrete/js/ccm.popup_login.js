
var ccmPopupLogin = { 
	
	init : function(){
		//alert('remove popup_login.js from header_required.php');
		//setTimeout('ccmPopupLogin.show("refresh")',1000);
	},
	
	//"redirect" can be a cID or a URL, or it can be the string "refresh" to reload the current page after login
	//successFunction is a callback run after a successful login
	show : function( redirect, successFunction, targetId, remote ){ 
		var rcID=(redirect && typeof(redirect)!='undefined')?redirect:'';
		this.loggedInFunction=successFunction;
		var loginFormUrl = CCM_TOOLS_PATH + '/popup_login?rcID='+rcID;
		if(remote) loginFormUrl=loginFormUrl+'&remote=1';
		if(targetId){
			var targetEl=$('#'+targetId)
			if(!targetEl) alert('Error: Target Not Found');
			else targetEl.load(loginFormUrl); 
		}else{
			$.fn.dialog.open({
				href: loginFormUrl, 
				title: "Login",
				width: 550,
				modal: false, 
				//onLoad:function(){alert('onLoad')},
				onClose: function(){}, 
				height: 220
			});
		}
	},
	
	login:function(form){ 
		try{
			var qStr = $(form).formSerialize();
			this.showLoading();
			$.ajax({ 
				url: form.action,
				type: 'POST',
				data: qStr,
				success: function(json){ 
					ccmPopupLogin.hideLoading();
					$('.ccm-dialog-content').scrollTop(0);
					if(!json){
						ccmPopupLogin.loginMsg('Error: Invalid response.',1);
						return false;
					}
					eval('var jsObj='+json)
					if(!jsObj.success){
						if(jsObj.error) ccmPopupLogin.loginMsg(jsObj.error,1); 
						else ccmPopupLogin.loginMsg('Unknown Error: Login Failed.',1)
					}else{ 
						ccmPopupLogin.loginMsg('<div class="success">'+jsObj.msg+'</div>'); 
						ccmPopupLogin.postLoginAction(jsObj);
					}
				}
			});	
		}catch(e){
			alert(e.message);
		}
		return false;
	},
	
	postLoginAction:function(jsObj){
		ccmPopupLogin.uID=(jsObj.uID>0)?jsObj.uID:0; 
		if(typeof(ccmPopupLogin.loggedInFunction)=='function' && ccmPopupLogin.uID>0) 
			ccmPopupLogin.loggedInFunction();
		if(typeof(jsObj.redirectURL)!='undefined' && (jsObj.redirectURL.toLowerCase()=='refresh' || jsObj.redirectURL.toLowerCase()=='reload')){
			window.location.href = unescape(window.location.pathname); //reload() for old browsers
		}else if(typeof(jsObj.redirectURL)!='undefined'){
			window.location=jsObj.redirectURL;
		}
	}, 
	
	loginMsg:function(msg,isError){
		var el=$('#ccm-popupLoginMsg');
		el.css('display','block');
		el.html(msg);
		if(isError) el.addClass('ccm-error');
		else el.removeClass('ccm-error');
	},
	
	toggleForgot:function(){ 
		this.hideMsgs(); 
		$('.ccm-dialog-content').scrollTop(0);
		if($('#ccm-popupForgotPasswordWrap').css('display')!='block'){
			var plw = $('#ccm-popupLoginWrap');
			
			if( plw.css('display')!='none' ) {
				plw.fadeOut( 500, function(){
					$(this).css('display','none');
					$('#ccm-popupForgotPasswordWrap').fadeIn(500);
				})
			}else{
				$('#ccm-popupForgotPasswordWrap').fadeIn(500);
			}
			
			var prw = $('#ccm-popupRegisterWrap');
			if(prw.css('display')!='none') 
				plw.fadeOut( 500, function(){ $(this).css('display','none'); } 	);			
		}else{			
			var fpw = $('#ccm-popupForgotPasswordWrap');			
			if( fpw.css('display')!='none' ) {
				fpw.fadeOut( 500, function(){
					$(this).css('display','none');
					$('#ccm-popupLoginWrap').fadeIn(500);
				})
			}else{
				$('#ccm-popupLoginWrap').fadeIn(500);
			}
			
			var prw = $('#ccm-popupRegisterWrap');
			if(prw.css('display')!='none') 
				prw.fadeOut( 500, function(){ $(this).css('display','none'); } 	);			
		}
	},
	
	toggleRegister:function(){ 
		this.hideMsgs();
		$('.ccm-dialog-content').scrollTop(0);
		if($('#ccm-popupRegisterWrap').css('display')!='block'){			
			var plw = $('#ccm-popupLoginWrap');
			
			if( plw.css('display')!='none' ) {
				plw.fadeOut( 500, function(){
					$(this).css('display','none');
					$('#ccm-popupRegisterWrap').fadeIn(500);
				})
			}else{
				$('#ccm-popupRegisterWrap').fadeIn(500);
			}
			
			var fpw = $('#ccm-popupForgotPasswordWrap');
			if(fpw.css('display')!='none') 
				fpw.fadeOut( 500, function(){ $(this).css('display','none'); } 	);					
		}else{			
			var prw = $('#ccm-popupRegisterWrap');			
			if( prw.css('display')!='none' ) {
				prw.fadeOut( 500, function(){
					$(this).css('display','none');
					$('#ccm-popupLoginWrap').fadeIn(500);
				})
			}else{
				$('#ccm-popupLoginWrap').fadeIn(500);
			}
			
			var fpw = $('#ccm-popupForgotPasswordWrap');
			if(fpw.css('display')!='none') 
				fpw.fadeOut( 500, function(){ $(this).css('display','none'); } 	);			
		}
	},	
	
	submitForgotPassword:function(form){
		try{
			var qStr = $(form).formSerialize();
			this.showLoading();
			$.ajax({ 
				url: form.action,
				type: 'POST',
				data: qStr,
				success: function(json){ 
					ccmPopupLogin.hideLoading();
					$('.ccm-dialog-content').scrollTop(0);
					if(!json){
						ccmPopupLogin.forgotMsg('Error: Invalid response.',1);
						return false;
					}
					eval('var jsObj='+json)
					if(!jsObj.success){
						if(jsObj.error) ccmPopupLogin.forgotMsg(jsObj.error,1); 
						else ccmPopupLogin.forgotMsg('Unknown Error',1)
					}else{ 
						ccmPopupLogin.forgotMsg('<div class="success">'+jsObj.msg+'</div>');
						$('#popupForgotPasswordForm').css('display','none');
					}
				}
			});	
		}catch(e){
			alert(e.message);
		}
		return false;
	},

	forgotMsg:function(msg,isError){
		var el=$('#ccm-popupForgotMsg');
		el.css('display','block');
		el.html(msg);
		if(isError) el.addClass('ccm-error');
		else el.removeClass('ccm-error');
	},
	
	submitRegister:function(form){
		try{
			var qStr = $(form).formSerialize();
			this.showLoading();
			$.ajax({ 
				url: form.action,
				type: 'POST',
				data: qStr,
				success: function(json){ 
					ccmPopupLogin.hideLoading();
					$('.ccm-dialog-content').scrollTop(0);
					if(!json){
						ccmPopupLogin.registerMsg('Error: Invalid response.',1);
						return false;
					}					
					eval('var jsObj='+json)
					if(!jsObj.success){
						if(jsObj.errors && jsObj.errors.length>0){ 
							ccmPopupLogin.registerMsg('<div>'+jsObj.errors.join('</div> <div>')+'</div>',1);							
						} 
					}else{ 
						ccmPopupLogin.registerMsg('<div class="success">'+jsObj.msg+'</div>'); 
						ccmPopupLogin.postLoginAction(jsObj);
					}
				}
			});	
		}catch(e){
			alert(e.message);
		}
		return false;
	},	

	registerMsg:function(msg,isError){
		var el=$('#ccm-popupRegisterMsg');
		el.css('display','block');
		el.html(msg);
		if(isError) el.addClass('ccm-error');
		else el.removeClass('ccm-error');
	},
	
	showLoading:function(){
		$('.ccm-dialog-content').scrollTop(0); 
		var l=$('#ccm-popupLoginWrap'), fp=$('#ccm-popupForgotPasswordWrap'), r=$('#ccm-popupRegisterWrap');	
		if(l.css('display')=='block') l.fadeTo(500,.25)
		if(fp.css('display')=='block') fp.fadeTo(500,.25)
		if(r.css('display')=='block') r.fadeTo(500,.25)
		var dialogWrap=$(document.getElementById('ccm-popupAuth').parentNode);
		dialogWrap.css('position','relative');
		dialogWrap.css('top',0);
		dialogWrap.css('left',0);
		$('#ccm-popupLoginThrobber').css('display','block');
	},
	
	hideLoading:function(){
		$('#ccm-popupLoginWrap').fadeTo(200,1);
		$('#ccm-popupForgotPasswordWrap').fadeTo(200,1);
		$('#ccm-popupRegisterWrap').fadeTo(200,1);
		$('#ccm-popupLoginThrobber').css('display','none');
	},
	
	hideMsgs:function(){
		$('#ccm-popupRegisterMsg').css('display','none');
		$('#ccm-popupForgotMsg').css('display','none');
		$('#ccm-popupLoginMsg').css('display','none');	
	}
}

//$(function(){ setTimeout('ccmPopupLogin.show("refresh")',1000); })