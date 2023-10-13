import '@concretecms/bedrock/assets/cms/js/ajax-request/base';

import ConcreteInstaller from './components/Installer'

$(function() {
    new Vue({
        el: '#ccm-page-install',
        components: {
            ConcreteInstaller
        }
    })
})