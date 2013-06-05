	function toggleCustomPage(value) {
		if (value == "custom") {
			$("#ccm-autonav-page-selector").css('display','block');
		} else {
			$("#ccm-autonav-page-selector").hide();
		}
	}

	function toggleSubPageLevels(value) {
		if (value == "none") {
			$("#displaySubPageLevels").get(0)[0].selected = true;
			$("#displaySubPageLevels").get(0).disabled = true;
			document.getElementById("displaySubPageLevels").disabled = true;
		} else {
			$("#displaySubPageLevels").get(0).disabled = false;
		}
	}

	function toggleSubPageLevelsNum(value) {
		if (value == "custom") {
			$("#divSubPageLevelsNum").css('display','block');
		} else {
			$("#divSubPageLevelsNum").hide();
		}
	}

	reloadPreview = function(blockForm) {
		orderBy = $("select[name=orderBy]").val();
		displayPages = $("select[name=displayPages]").val();
		displaySubPages = $("select[name=displaySubPages]").val();
		displaySubPageLevels = $("select[name=displaySubPageLevels]").val();
		displaySubPageLevelsNum = $("input[name=displaySubPageLevelsNum]").val();
		displayUnavailablePages = $("input[name=displayUnavailablePages]").val();
		displayPagesCID = $("input[name=displayPagesCID]").val();
		displayPagesIncludeSelf = $("input[name=displayUnavailablePages]").val();

		if(displayPages == "custom" && !displayPagesCID) { return false; }
		
		//$("#ccm-dialog-throbber").css('visibility', 'visible');

		var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
		$('#ccm-autonavPane-preview').html(loaderHTML);
		
		$.ajax({
			type: 'POST',
			url: $("input[name=autonavPreviewPane]").val(),
			data: 'orderBy=' + orderBy + '&cID=' + $("input[name=autonavCurrentCID]").val() + '&displayPages=' + displayPages + '&displaySubPages=' + displaySubPages + '&displaySubPageLevels=' + displaySubPageLevels + '&displaySubPageLevelsNum=' + displaySubPageLevelsNum + '&displayUnavailablePages=' + displayUnavailablePages + '&displayPagesCID=' + displayPagesCID + '&displayPagesIncludeSelf=' + displayPagesIncludeSelf,
			success: function(resp) {
				//$("#ccm-dialog-throbber").css('visibility', 'hidden');
				$("#ccm-autonavPane-preview").html(resp);
				$("#ccm-auto-nav").css('opacity',1);
			}		
		});
	}
	
	function reloadCCMCall() {
		reloadPreview(document.blockForm);
	}
	
	autonavTabSetup = function() {
		$('ul#ccm-autonav-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-autonav-tab-','');
				autonavShowPane(pane);
			}
		});		
	}
	
	autonavShowPane = function (pane){
		$('ul#ccm-autonav-tabs li').each(function(num,el){ $(el).removeClass('active') });
		$(document.getElementById('ccm-autonav-tab-'+pane).parentNode).addClass('active');
		$('div.ccm-autonavPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-autonavPane-'+pane).css('display','block');
		if(pane=='preview') reloadPreview(document.blockForm);
	}
	
	$(function() {	
		autonavTabSetup();		
	});

