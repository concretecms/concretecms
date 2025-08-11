// Include core libraries for panels, etc...
import "@concretecms/bedrock/assets/cms/js/base";

// Make sure things that need vue contexts to fire automatically do so
// Note - we can't just include this in base.js because then when base.js is
// included in dashboard main.js it fires too early. So we have to separate it out.
$(function() {
    $('[data-vue]').concreteVue({'context': 'cms'})
})
