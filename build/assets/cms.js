// Include core libraries for panels, etc...
import "@concretecms/bedrock/assets/cms/js/base";

// Import only the bootstrap libraries required for the CMS domain.
import "bootstrap/js/dist/tooltip";
import "bootstrap/js/dist/popover";
import "bootstrap/js/dist/tab";
import "bootstrap/js/dist/toast";

// Activate our CMS components for use
const vueInstances = document.querySelectorAll('[vue-enabled]')
vueInstances.forEach(function (element) {
    if (element.getAttribute('vue-enabled') !== 'activated') {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: element,
                components: config.components
            })
        })
        element.setAttribute('vue-enabled', 'activated');
    }
})
