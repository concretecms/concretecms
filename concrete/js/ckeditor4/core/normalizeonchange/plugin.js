(function () {
    CKEDITOR.plugins.add('normalizeonchange', {
        init: function (editor) {
			CKEDITOR.on('instanceReady', function (ck) {
				ck.editor.on("change", function (e) {
					var sel = ck.editor.getSelection();
					if (sel) {
						var selected = sel.getStartElement();
						if (selected && selected.$)
							sel.getStartElement().$.normalize();
					}
				});
			 });
        }
    });
})();