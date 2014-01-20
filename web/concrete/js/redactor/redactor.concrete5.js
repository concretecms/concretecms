// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = {

	init: function() {

		this.$toolbar.addClass("ccm-inline-toolbar");
		this.$toolbar.append($('<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">Cancel</button></li><li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>'));
		var toolbar = this.$toolbar;
		var editor = this.$editor;


		$('#ccm-redactor-cancel-button').on('click', function() {
			toolbar.hide();
			$('li#ccm-redactor-actions-buttons').hide();
			ConcreteEvent.fire('EditModeExitInline');
			editor.destroyEditor();
		});
		$('#ccm-redactor-save-button').on('click', function() {
			$('#redactor-content').val(editor.getCode());
			toolbar.hide();
			editor.destroyEditor();
			$('#ccm-block-form').submit();
		});

	}

}

RedactorPlugins.concrete5 = {

	init: function() {

		var plugin = this;

		$.ajax({
			'type': 'get',
			'dataType': 'json',
			'url': CCM_TOOLS_PATH + '/system_content_editor_menu?ccm_token=' + CCM_EDITOR_SECURITY_TOKEN,
			success: function(response) {
				dropdownOptions = {};
				$.each(response.coreMenus, function(i, menu) {
					switch(menu) {
						case 'insert_page':
							dropdownOptions.insertLinkToPage = {
								title: ccmi18n_editor.insertLinkToPage,
								callback: function(obj) {

								    $.fn.dialog.open({
								        title: ccmi18n_sitemap.choosePage,
								        href: CCM_TOOLS_PATH + '/sitemap_search_selector.php?sitemap_select_mode=select_page',
								        width: '90%',
								        modal: false,
								        height: '70%'
								    });

								    ccm_selectSitemapNode = function(cID, cName) {				    
										var url = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID;	
										try {
											selectedText = obj.$el.getSelected();
										} catch (e) {
											selectedText = obj.$editor.getSelected();
										}
										if (selectedText != '') {
											obj.execCommand('inserthtml', '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID + '" title="' + cName + '">' + selectedText + '<\/a>');
										} else {
											var selectedText = '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID + '" title="' + cName + '">' + cName + '<\/a>';
											obj.insertHtml(selectedText);
										}
								    }

								}
							}
							break;
						case 'insert_file':
							dropdownOptions.insertLinkToFile = {
								title: ccmi18n_editor.insertLinkToFile,
								callback: function(obj) {
									obj.saveSelection();
									ccm_launchFileManager();
									ccm_chooseAsset = function(res) {
										obj.restoreSelection();
										try {
											selectedText = obj.$el.getSelected();
										} catch (e) {
											selectedText = obj.$editor.getSelected();
										}

										if (selectedText != '') {
											var html = '<a href="' + res.filePathInline + '">' + selectedText + '<\/a>';
											obj.execCommand('inserthtml', html);
										} else {
											var html = '<a href="' + res.filePathInline + '">' + res.title + '<\/a>';
											obj.insertHtml(html);
										}
									}
								}
							}
							break;
						case 'insert_image':
							dropdownOptions.insertImage = {
								title: ccmi18n_editor.insertImage,
								callback: function(obj) {
									obj.saveSelection();
									ccm_launchFileManager();
									ccm_chooseAsset = function(res) {
										obj.restoreSelection();
										obj.insertHtml('<img src="' + res.filePathInline + '" alt="' + res.title + '" width="' + res.width + '" height="' + res.height + '">');
									}
								}
							}
							break;
					}
				});

				if (response.coreMenus.length > 0 && response.snippets.length > 0) {
					dropdownOptions.snippet_separator = {
						name: 'separator'
					}
				}

				plugin.snippetsByHandle = {};
				$.each(response.snippets, function(i, snippet) {
					plugin.snippetsByHandle[snippet.scsHandle] = {
						'scsHandle': snippet.scsHandle,
						'scsName': snippet.scsName
					}
					dropdownOptions[snippet.scsHandle] = {
						'title': snippet.scsName,
						'callback': function(obj, e, option) {
							var selectedSnippet = plugin.snippetsByHandle[option];
							var html = String() + 
								'<span class="ccm-content-editor-snippet" contenteditable="false" data-scsHandle="' + selectedSnippet.scsHandle + '">' + 
								selectedSnippet.scsName + 
								'</span>';
							obj.insertHtml(html);
						}
					}
				});
			
				if (response.coreMenus.length > 0 || response.snippets.length > 0) {
					plugin.addBtnFirst('concrete5', 'concrete5', false, dropdownOptions);
				}
			}
		});
		this.opts.observeImages = false;
	}


}
