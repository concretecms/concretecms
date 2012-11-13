// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = {

	activeEditor: false,

	selectSitemapNode: function(cID, cName) {
		var url = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID;		
		var editor = RedactorPlugins.concrete5inline.activeEditor;
		var selectedText = editor.getSelected();

		if (selectedText != '') {
			editor.execCommand('inserthtml', '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID + '" title="' + cName + '">' + selectedText + '<\/a>');
		} else {
			var selectedText = '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID + '" title="' + cName + '">' + cName + '<\/a>';
			editor.insertHtml(selectedText);
		}

	},


	init: function() {
		var editor = this.$editor;
		var iobj = this;

		this.addBtnBefore('html', 'concrete5', 'concrete5', false, {

			insertLinkToPage:
			{
				title: ccmi18n_editor.insertLinkToPage,
				callback: function() {

			    $.fn.dialog.open({
			        title: ccmi18n_sitemap.choosePage,
			        href: CCM_TOOLS_PATH + '/sitemap_search_selector.php?sitemap_select_mode=select_page&callback=RedactorPlugins.concrete5inline.selectSitemapNode',
			        width: '90%',
			        modal: false,
			        height: '70%'
			    });

				}
			},
			insertImage:
			{
				title: ccmi18n_editor.insertImage,
				callback: function() {
					var editor = RedactorPlugins.concrete5inline.activeEditor;
					iobj.saveSelection();
					ccm_launchFileManager();
					ccm_chooseAsset = function(obj) {
						iobj.restoreSelection();
						editor.insertHtml('<img src="' + obj.filePathInline + '" alt="' + obj.title + '" width="' + obj.width + '" height="' + obj.height + '">');
					}
				}
			},
			insertLinkToFile:
			{
				title: ccmi18n_editor.insertLinkToFile,
				callback: function() {
					var editor = RedactorPlugins.concrete5inline.activeEditor;
					iobj.saveSelection();
					ccm_launchFileManager();
					ccm_chooseAsset = function(obj) {
						iobj.restoreSelection();
						var selectedText = editor.getSelected();
						if (selectedText != '') {
							var html = '<a href="' + obj.filePathInline + '">' + selectedText + '<\/a>';
						} else {
							var html = '<a href="' + obj.filePathInline + '">' + obj.title + '<\/a>';
						}
						editor.insertHtml(html);
					}
				}
			}
		});
		this.addBtnSeparatorAfter('concrete5');
		this.opts.observeImages = false;

		this.$toolbar.css({ position: 'fixed', width: '100%', zIndex: 999, top: '50px', left: '0px' });
		this.$toolbar.append($('<li id="ccm-redactor-actions-buttons" class="ccm-ui"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">Cancel</button><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>'));
		this.$toolbar.appendTo($(document.body));
		var toolbar = this.$toolbar;

		RedactorPlugins.concrete5inline.activeEditor = editor;

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
