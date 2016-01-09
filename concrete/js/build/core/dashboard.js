/** 
 * Basic concrete5 toolbar class
 */

var ConcreteDashboard = function() {
	setupResultMessages = function() {
		if ($('#ccm-dashboard-result-message').length > 0) { 
			if ($('.ccm-pane').length > 0) { 
				var pclass = $('.ccm-pane').parent().attr('class');
				var gpclass = $('.ccm-pane').parent().parent().attr('class');
				var html = $('#ccm-dashboard-result-message').html();
				$('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
			}
		} else {
			$("#ccm-dashboard-result-message").fadeIn(200);
		}
	};

	var setupTooltips = function() {
		if ($("#ccm-tooltip-holder").length == 0) {
			$('<div />').attr('id','ccm-tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
		}
		$('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
	};

    var setupDialogs = function() {
        $('.dialog-launch').dialog();
    };

    return {
		start: function(options) {
			setupTooltips();
			setupResultMessages();
            setupDialogs();

		}

	}

}();
