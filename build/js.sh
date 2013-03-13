uglifyjs -o ../web/concrete/js/jquery.cookie.js ../web/concrete/js/ccm_app/jquery.cookie.js

uglifyjs -o ../web/concrete/js/ccm.profile.js ../web/concrete/js/ccm_profile/base.js

cat ../web/concrete/js/redactor/redactor.js \
../web/concrete/js/redactor/redactor.concrete5.js > redactor.tmp.js
uglifyjs -o ../web/concrete/js/redactor.js ./redactor.tmp.js
rm redactor.tmp.js

cat ../web/concrete/js/gridster/jquery.gridster.js > jquery.gridster.tmp.js
uglifyjs --no-copyright --no-seqs -o ../web/concrete/js/jquery.gridster.js ./jquery.gridster.tmp.js 
rm jquery.gridster.tmp.js

cat ../web/concrete/js/ccm_app/dashboard.js > ccm.dashboard.tmp.js
uglifyjs --no-copyright --no-seqs -o ../web/concrete/js/ccm.dashboard.js ./ccm.dashboard.tmp.js 
rm ccm.dashboard.tmp.js

cat ../web/concrete/js/ccm_app/conversations.js > ccm.conversations.tmp.js
uglifyjs --no-copyright --no-seqs -o ../web/concrete/js/ccm.conversations.js ./ccm.conversations.tmp.js 
rm ccm.conversations.tmp.js

# Note: The order here is important
cat ../web/concrete/js/ccm_app/pubsub.js \
../web/concrete/js/ccm_app/jquery.colorpicker.js \
../web/concrete/js/ccm_app/jquery.hoverIntent.js \
../web/concrete/js/ccm_app/jquery.liveupdate.js \
../web/concrete/js/ccm_app/jquery.metadata.js \
../web/concrete/js/ccm_app/chosen.jquery.js \
../web/concrete/js/ccm_app/base.js \
../web/concrete/js/ccm_app/ui.js \
../web/concrete/js/ccm_app/filemanager.js \
../web/concrete/js/ccm_app/jquery.cookie.js \
../web/concrete/js/ccm_app/legacy_dialog.js \
../web/concrete/js/ccm_app/newsflow.js \
../web/concrete/js/ccm_app/page_reindexing.js \
../web/concrete/js/ccm_app/in_context_menu.js \
../web/concrete/js/ccm_app/block_types.js \
../web/concrete/js/ccm_app/quicksilver.js \
../web/concrete/js/ccm_app/inline_edit.js \
../web/concrete/js/ccm_app/remote_marketplace.js \
../web/concrete/js/ccm_app/search.js \
../web/concrete/js/ccm_app/sitemap.js \
../web/concrete/js/ccm_app/custom_style.js \
../web/concrete/js/ccm_app/tabs.js \
../web/concrete/js/ccm_app/page_areas.js \
../web/concrete/js/ccm_app/toolbar.js \
../web/concrete/js/ccm_app/themes.js > ccm.app.tmp.js

uglifyjs --no-seqs -o ../web/concrete/js/ccm.app.js ./ccm.app.tmp.js
rm ccm.app.tmp.js

uglifyjs -o ../web/concrete/js/ccm.layouts.js ../web/concrete/js/layouts/layouts.js


cat ../web/concrete/js/image_editor/build/kinetic.prototype.js \
    ../web/concrete/js/image_editor/build/imageeditor.js \
    ../web/concrete/js/image_editor/build/history.js \
    ../web/concrete/js/image_editor/build/events.js \
    ../web/concrete/js/image_editor/build/elements.js \
    ../web/concrete/js/image_editor/build/controls.js \
    ../web/concrete/js/image_editor/build/save.js \
    ../web/concrete/js/image_editor/build/extend.js \
    ../web/concrete/js/image_editor/build/background.js \
    ../web/concrete/js/image_editor/build/imagestage.js \
    ../web/concrete/js/image_editor/build/image.js \
    ../web/concrete/js/image_editor/build/actions.js \
    ../web/concrete/js/image_editor/build/slideOut.js \
    ../web/concrete/js/image_editor/build/jquerybinding.js \
    ../web/concrete/js/image_editor/build/filters.js > image_editor.tmp.js

uglifyjs --no-seqs --no-squeeze -b -o ../web/concrete/js/image_editor/image_editor.js ./image_editor.tmp.js

cat ../web/concrete/js/image_editor/build/kinetic.js \
    ../web/concrete/js/image_editor/image_editor.js > image_editor.tmp.js

uglifyjs --no-seqs -o ../web/concrete/js/image_editor.min.js ./image_editor.tmp.js
rm image_editor.tmp.js
