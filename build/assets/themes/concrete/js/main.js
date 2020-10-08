import * as FrontendBase from '@concretecms/bedrock/assets/bedrock/js/frontend';
import LoginTabs from './login-tabs';
//import BackgroundImage from './background-image';

// Handle profile picture
import '@concretecms/bedrock/assets/account/js/frontend';

// Handle desktop
import '@concretecms/bedrock/assets/desktop/js/frontend';

import NProgress from 'nprogress';
window.NProgress = NProgress;

$('.launch-tooltip').tooltip({
    placement: 'bottom'
});
