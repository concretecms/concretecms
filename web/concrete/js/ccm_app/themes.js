/** 
 * Theme JS
 */

function ccm_previewInternalTheme(cID, themeID,themeName){
	var ctID=$("input[name=ctID]").val();
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeID="+themeID+'&previewCID='+cID+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});	
}

function ccm_previewMarketplaceTheme(cID, themeCID,themeName,themeHandle){
	var ctID=$("input[name=ctID]").val();
	
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeCID="+themeCID+'&previewCID='+cID+'&themeHandle='+encodeURIComponent(themeHandle)+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});
}

ccm_marketplaceDetailShowMore = function() {	
	$(".ccm-marketplace-item-information-more").hide();
	$(".ccm-marketplace-item-information-inner").css('max-height', 'none');
	//ccm_marketplaceBrowserSetupNextAndPrevious();
}

ccm_marketplaceUpdatesShowMore = function(obj) {	
	$(obj).parent().hide();
	$(obj).parent().parent().find('.ccm-marketplace-update-changelog').css('max-height', 'none');
}

ccm_enableDesignScrollers = function() {
	$("a.ccm-scroller-l").hover(function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_l_active.png');
	}, function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_l.png');
	});

	$("a.ccm-scroller-r").hover(function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_r_active.png');
	}, function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_r.png');
	});
	
	var numThumbs = 4;	
	var thumbWidth = 132;
	
	$('a.ccm-scroller-r').unbind('click');
	$('a.ccm-scroller-l').unbind('click');
	
	$('a.ccm-scroller-r').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');

		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) - migratePos;
		currentPage++;
		
		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == numPages) {
			$(this).hide();
		}
		if (currentPage > 1) {
			$(this).siblings('a.ccm-scroller-l').show();
		}
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/
		
		$(item).css('left', currentPos + 'px');
		
		
	});

	$('a.ccm-scroller-l').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');
		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) + migratePos;
		currentPage--;

		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == 1) {
			$(this).hide();
		}
		
		if (currentPage < numPages) {
			$(this).siblings('a.ccm-scroller-r').show();
		}
		
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/

		$(item).css('left', currentPos + 'px');
		
		
	});
	$('a.ccm-scroller-l').hide();
	$('a.ccm-scroller-r').each(function() {
		if (parseInt($(this).parent().attr('num-pages')) == 1) {
			$(this).hide();
		}
	});

	$("#ccm-select-page-type a").click(function() {
		$("#ccm-select-page-type li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		$("input[name=ctID]").val($(this).attr('ccm-page-type-id'));
	});

	$("#ccm-select-theme a").click(function() {
		$("#ccm-select-theme li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		$("input[name=plID]").val($(this).attr('ccm-theme-id'));
	});



}
