var ccm_searchActivatePostFunction = false;

ccm_setupAdvancedSearchFields = function(searchType) {
	$("#ccm-" + searchType + "-search-add-option").unbind();
	$("#ccm-" + searchType + "-search-add-option").click(function() {
		ccm_totalAdvancedSearchFields++;
		$("#ccm-search-fields-wrapper").append('<div class="ccm-search-field" id="ccm-' + searchType + '-search-field-set' + ccm_totalAdvancedSearchFields + '">' + $("#ccm-search-field-base").html() + '<\/div>');
		ccm_activateAdvancedSearchFields(searchType, ccm_totalAdvancedSearchFields);
	});
}

ccm_setupAdvancedSearch = function(searchType) {
	ccm_setupAdvancedSearchFields(searchType);
	$("#ccm-" + searchType + "-advanced-search").ajaxForm({
		beforeSubmit: function() {
			ccm_deactivateSearchResults();
		},
		
		success: function(resp) {
			ccm_parseAdvancedSearchResponse(resp);
		}
	});
	ccm_setupInPagePaginationAndSorting();
	ccm_setupSortableColumnSelection();
	
}

ccm_parseAdvancedSearchResponse = function(resp) {
	$("#ccm-search-results").html(resp);
	ccm_activateSearchResults();
}

ccm_deactivateSearchResults = function() {
	$("#ccm-search-fields-submit input").attr('disabled', true);
	$("#ccm-search-loading").show();
}

ccm_activateSearchResults = function() {
	$("#ccm-search-loading").hide();
	$("#ccm-search-fields-submit input").attr('disabled', false);
	ccm_setupInPagePaginationAndSorting();
	ccm_setupSortableColumnSelection();
	if(typeof(ccm_searchActivatePostFunction) == 'function') {
		ccm_searchActivatePostFunction();
	}
}

ccm_setupInPagePaginationAndSorting = function() {
	$(".ccm-results-list th a").click(function() {
		ccm_deactivateSearchResults();
		$("#ccm-search-results").load($(this).attr('href'), false, function() {
			ccm_activateSearchResults();
		});
		return false;
	});
	$("div.ccm-pagination a").click(function() {
		ccm_deactivateSearchResults();
		$("#ccm-search-results").load($(this).attr('href'), false, function() {
			ccm_activateSearchResults();
			$("div.ccm-dialog-content").attr('scrollTop', 0);
		});
		return false;
	});
}

ccm_setupSortableColumnSelection = function() {
	$("#ccm-search-add-column").unbind();
	$("#ccm-search-add-column").click(function() {
		jQuery.fn.dialog.open({
			width: 400,
			height: 350,
			modal: false,
			href: $(this).attr('href'),
			title: ccmi18n.customizeSearch				
		});
		return false;
	});
}

ccm_activateAdvancedSearchFields = function(searchType, fieldset) {
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " select[name=searchField]").unbind();
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " select[name=searchField]").change(function() {
		var selected = $(this).find(':selected').val(); 
		$(this).next('input.ccm-' + searchType + '-selected-field').val(selected);
		
		var itemToCopy = $('#ccm-' + searchType + '-search-field-base-elements span[search-field=' + selected + ']');
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content").html('');
		itemToCopy.clone().appendTo("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content");
		
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content .ccm-search-option").show();

		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_added] input").each(function() {
			if ($(this).attr('id') == 'date_from') {
				$(this).attr('id', 'date_from' + fieldset);
			} else if ($(this).attr('id') == 'date_to') {
				$(this).attr('id', 'date_to' + fieldset);
			}
		});
	
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").each(function() {
			$(this).attr('id', $(this).attr('id') + fieldset);
		});
		
		
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_added] input").datepicker({
			showAnim: 'fadeIn'
		});
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").datepicker({
			showAnim: 'fadeIn'
		});
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-rating input").rating();
		
	});

	
	// add the initial state of the latest select menu
	var lastSelect = $("#ccm-" + searchType + "-search-field-set" + fieldset + " select[name=searchField]").eq($(".ccm-" + searchType + "-search-field select[name=searchField]").length-1);
	var selected = lastSelect.find(':selected').val();
	lastSelect.next('input.ccm-" + searchType + "-selected-field').val(selected);

	
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-remove-option").unbind();
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-remove-option").click(function() {
		$(this).parents('div.ccm-search-field').remove();
		//ccm_totalAdvancedSearchFields--;
	});
	
}


ccm_activateEditablePropertiesGrid = function() {
	$("tr.ccm-attribute-editable-field").each(function() {
		var trow = $(this);
		$(this).find('a').click(function() {
			trow.find('.ccm-attribute-editable-field-text').hide();
			trow.find('.ccm-attribute-editable-field-clear-button').hide();
			trow.find('.ccm-attribute-editable-field-form').show();
			trow.find('.ccm-attribute-editable-field-save-button').show();
		});
		
		trow.find('form').submit(function() {
			ccm_submitEditablePropertiesGrid(trow);
			return false;
		});
		
		trow.find('.ccm-attribute-editable-field-save-button').parent().click(function() {
			ccm_submitEditablePropertiesGrid(trow);
		});

		trow.find('.ccm-attribute-editable-field-clear-button').parent().unbind();
		trow.find('.ccm-attribute-editable-field-clear-button').parent().click(function() {
			trow.find('form input[name=task]').val('clear_extended_attribute');
			ccm_submitEditablePropertiesGrid(trow);
			return false;
		});

	});
}

ccm_submitEditablePropertiesGrid = function(trow) {
	trow.find('.ccm-attribute-editable-field-save-button').hide();
	trow.find('.ccm-attribute-editable-field-clear-button').hide();
	trow.find('.ccm-attribute-editable-field-loading').show();
	trow.find('form').ajaxSubmit(function(resp) {
		// resp is new HTML to display in the div
		trow.find('.ccm-attribute-editable-field-loading').hide();
		trow.find('.ccm-attribute-editable-field-save-button').show();
		trow.find('.ccm-attribute-editable-field-text').html(resp);
		trow.find('.ccm-attribute-editable-field-form').hide();
		trow.find('.ccm-attribute-editable-field-save-button').hide();
		trow.find('.ccm-attribute-editable-field-text').show();
		trow.find('.ccm-attribute-editable-field-clear-button').show();
		trow.find('td').show('highlight', {
			color: '#FFF9BB'
		});

	});
}


