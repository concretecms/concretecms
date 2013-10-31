lessc --verbose -x ../web/concrete/css/ccm_app/build/ccm.app.less ../web/concrete/css/ccm.app.css

# Note: The order here is important
cat ../web/concrete/js/ccm_app/jquery.hoverIntent.js \
../web/concrete/js/ccm_app/jquery.liveupdate.js \
../web/concrete/js/ccm_app/chosen.jquery.js \
../web/concrete/js/ccm_app/base.js \
../web/concrete/js/ccm_app/ui.js \
../web/concrete/js/ccm_app/edit_page.js \
../web/concrete/js/ccm_app/filemanager.js \
../web/concrete/js/ccm_app/jquery.cookie.js \
../web/concrete/js/ccm_app/panels.js \
../web/concrete/js/ccm_app/dialog.js \
../web/concrete/js/ccm_app/alert.js \
../web/concrete/js/ccm_app/newsflow.js \
../web/concrete/js/ccm_app/page_reindexing.js \
../web/concrete/js/ccm_app/in_context_menu.js \
../web/concrete/js/ccm_app/quicksilver.js \
../web/concrete/js/ccm_app/remote_marketplace.js \
../web/concrete/js/ccm_app/progressive_operations.js \
../web/concrete/js/ccm_app/inline_edit.js \
../web/concrete/js/ccm_app/search.js \
../web/concrete/js/ccm_app/page_search.js \
../web/concrete/js/ccm_app/custom_style.js \
../web/concrete/js/ccm_app/tabs.js \
../web/concrete/js/ccm_app/toolbar.js \
../web/concrete/js/ccm_app/themes.js > ccm.app.tmp.js

uglifyjs --no-seqs -o ../web/concrete/js/ccm.app.js ./ccm.app.tmp.js
rm ccm.app.tmp.js

