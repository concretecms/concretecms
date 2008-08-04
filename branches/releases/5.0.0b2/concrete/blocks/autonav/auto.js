	function toggleCustomPage(value) {
		if (value == "custom") {
			$("#divInclude").css('display','block');
		} else {
			$("#divInclude").hide();
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
		orderBy = $("select[@name=orderBy]").val();
		displayPages = $("select[@name=displayPages]").val();
		displaySubPages = $("select[@name=displaySubPages]").val();
		displaySubPageLevels = $("select[@name=displaySubPageLevels]").val();
		displaySubPageLevelsNum = $("input[@name=displaySubPageLevelsNum]").val();
		displayUnavailablePages = $("input[@name=displayUnavailablePages]").val();
		displayPagesCID = $("input[@name=displaySubPageLevelsNum]").val();
		displayPagesIncludeSelf = $("input[@name=displayUnavailablePages]").val();

		if(displayPages == "custom" && !displayPagesCID) { return false; }
		
		//$("#ccm-dialog-throbber").css('visibility', 'visible');
		
		$.ajax({
			type: 'POST',
			url: $("input[@name=autonavPreviewPane]").val(),
			data: 'orderBy=' + orderBy + '&cID=' + $("input[@name=autonavCurrentCID]").val() + '&displayPages=' + displayPages + '&displaySubPages=' + displaySubPages + '&displaySubPageLevels=' + displaySubPageLevels + '&displaySubPageLevelsNum=' + displaySubPageLevelsNum + '&displayUnavailablePages=' + displayUnavailablePages + '&displayPagesCID=' + displayPagesCID + '&displayPagesIncludeSelf=' + displayPagesIncludeSelf,
			success: function(resp) {
				//$("#ccm-dialog-throbber").css('visibility', 'hidden');
				$("#autoPreview").html(resp);
				$("#ccm-auto-nav").css('opacity',1);
			}		
		});
	}
	
	function reloadCCMCall() {
		reloadPreview(document.blockForm);
	}
	
	$(function() {reloadPreview(document.blockForm);});

