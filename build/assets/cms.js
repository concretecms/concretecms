// Include core libraries for panels, etc...
import "@concretecms/bedrock/assets/cms/js/base";

// Import only the bootstrap libraries required for the CMS domain.
import "bootstrap/js/dist/tooltip";
import "bootstrap/js/dist/popover";
import "bootstrap/js/dist/tab";
import "bootstrap/js/dist/toast";

// Activate our CMS components for use
Concrete.Vue.activateContext('cms', '[vue-enabled]', document)
