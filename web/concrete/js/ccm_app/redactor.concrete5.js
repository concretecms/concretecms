// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = {

	init: function() {

		this.$toolbar.css({ position: 'fixed', width: '100%', zIndex: 999, top: '50px', left: '0px' });
		this.$toolbar.append($('<li id="ccm-redactor-actions-buttons" class="ccm-ui"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">Cancel</button><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>'));
		this.$toolbar.appendTo($(document.body));
		var toolbar = this.$toolbar;
		var editor = this.$editor;


		$('#ccm-redactor-cancel-button').on('click', function() {
			$('li#ccm-redactor-actions-buttons').hide();
			toolbar.appendTo(editor);
			ccm_onInlineEditCancel(function() {
				editor.destroyEditor();
			});
		});
		$('#ccm-redactor-save-button').on('click', function() {
			$('#redactor-content').val(editor.getCode());
			$('li#ccm-redactor-actions-buttons').hide();
			toolbar.appendTo(editor); // we have to move these for style purposes
			editor.destroyEditor();
			$('#ccm-block-form').submit();
		});

	}

}

RedactorPlugins.concrete5 = {

	init: function() {

		this.addBtnBefore('html', 'concrete5', 'concrete5', false, {

			insertLinkToPage:
			{
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
			},
			insertImage:
			{
				title: ccmi18n_editor.insertImage,
				callback: function(obj) {
					obj.saveSelection();
					ccm_launchFileManager();
					ccm_chooseAsset = function(res) {
						obj.restoreSelection();
						obj.insertHtml('<img src="' + res.filePathInline + '" alt="' + res.title + '" width="' + res.width + '" height="' + res.height + '">');
					}
				}
			},
			insertLinkToFile:
			{
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
		});
		this.addBtnSeparatorAfter('concrete5');
		this.opts.observeImages = false;


	}


}
