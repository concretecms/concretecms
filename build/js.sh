cat ../web/concrete/js/bootstrap/bootstrap.tooltip.js \
../web/concrete/js/bootstrap/bootstrap.popover.js \
../web/concrete/js/bootstrap/bootstrap.dropdown.js \
../web/concrete/js/bootstrap/bootstrap.transitions.js \
../web/concrete/js/bootstrap/bootstrap.alert.js > bootstrap.tmp.js

uglifyjs --no-copyright -o ../web/concrete/js/bootstrap.js ./bootstrap.tmp.js 
rm bootstrap.tmp.js

uglifyjs --no-copyright -o ../web/concrete/js/jquery.cookie.js ../web/concrete/js/ccm_app/jquery.cookie.js

cat ../web/concrete/js/ccm_app/dashboard.js > ccm.dashboard.tmp.js
uglifyjs --no-copyright --no-seqs -o ../web/concrete/js/ccm.dashboard.js ./ccm.dashboard.tmp.js 
rm ccm.dashboard.tmp.js

# Note: The order here is important

cat ../web/concrete/js/ccm_app/jquery.colorpicker.js \
../web/concrete/js/ccm_app/jquery.hoverIntent.js \
../web/concrete/js/ccm_app/jquery.liveupdate.js \
../web/concrete/js/ccm_app/jquery.metadata.js \
../web/concrete/js/ccm_app/chosen.jquery.js \
../web/concrete/js/ccm_app/filemanager.js \
../web/concrete/js/ccm_app/jquery.cookie.js \
../web/concrete/js/ccm_app/layouts.js \
../web/concrete/js/ccm_app/legacy_dialog.js \
../web/concrete/js/ccm_app/newsflow.js \
../web/concrete/js/ccm_app/page_reindexing.js \
../web/concrete/js/ccm_app/quicksilver.js \
../web/concrete/js/ccm_app/remote_marketplace.js \
../web/concrete/js/ccm_app/search.js \
../web/concrete/js/ccm_app/sitemap.js \
../web/concrete/js/ccm_app/status_bar.js \
../web/concrete/js/ccm_app/tabs.js \
../web/concrete/js/ccm_app/tinymce_integration.js \
../web/concrete/js/ccm_app/ui.js \
../web/concrete/js/ccm_app/toolbar.js \
../web/concrete/js/ccm_app/themes.js \
../web/concrete/js/ccm_app/composer.js > ccm.app.tmp.js

uglifyjs --no-copyright --no-seqs -o ../web/concrete/js/ccm.app.js ./ccm.app.tmp.js 
rm ccm.app.tmp.js
