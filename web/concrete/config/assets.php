<?
defined('C5_EXECUTE') or die("Access Denied.");

$al = AssetList::getInstance();

/** 
 * Third party libraries we rely on
 */

// jquery
$al->register('javascript', 'jquery', 'jquery.js', 100, Asset::ASSET_POSITION_HEADER, false);

// jquery ui
$al->register('javascript', 'jqueryui', 'jquery.ui.js', 95, false, false);
$al->register('css', 'jqueryui', 'jquery.ui.css', false, false, false);
$al->registerGroup('jqueryui', array(
	array('javascript', 'jqueryui'),
	array('css', 'jqueryui')
));

// dropzone
$al->register('javascript', 'dropzone', 'dropzone.js');

// jquery form
$al->register('javascript', 'jquery/form', 'jquery.form.js');

// swfobject
$al->register('javascript', 'swfobject', 'swfobject.js');

// redactor
$al->register('javascript', 'redactor', 'redactor.js', false, false, false);
$al->register('css', 'redactor', 'redactor.css');
$al->registerGroup('redactor', array(
	array('javascript', 'redactor'),
	array('css', 'redactor')
));

// backstretch
$al->register('javascript', 'backstretch', 'jquery.backstretch.js');

// dynatree
$al->register('javascript', 'dynatree', 'dynatree.js', false, false, false);
$al->register('css', 'dynatree', 'dynatree.css', false, false, false);

// hoverIntent
$al->register('javascript', 'hoverintent', 'ccm_app/jquery.hoverIntent.js');

// bootstrap
$al->register('javascript', 'bootstrap/dropdown', 'bootstrap/dropdown.js');
$al->register('javascript', 'bootstrap/tooltip', 'bootstrap/tooltip.js', 95); // has to come before popover
$al->register('javascript', 'bootstrap/popover', 'bootstrap/popover.js');
$al->register('javascript', 'bootstrap/alert', 'bootstrap/alert.js');
$al->register('javascript', 'bootstrap/transition', 'bootstrap/transition.js');
$al->register('css', 'bootstrap/dropdown', 'ccm.app.css', false, false, false);
$al->register('css', 'bootstrap/tooltip', 'ccm.app.css', false, false, false);
$al->register('css', 'bootstrap/popover', 'ccm.app.css', false, false, false);
$al->register('css', 'bootstrap/alert', 'ccm.app.css', false, false, false);
$al->register('css', 'bootstrap/transition', 'ccm.app.css', false, false, false);
$al->register('css', 'bootstrap', 'ccm.app.css', false, false, false);

/** 
 * ## Core functionality and styles
 */

// JS Events
$al->register('javascript', 'core/observer', 'ccm.pubsub.js');

// Core App
$al->register('css', 'core/app', 'ccm.app.css', false, false, false);
$al->register('javascript', 'core/app', 'ccm.app.js', false, false, false);
$al->registerGroup('core/app', array(
	array('javascript', 'jquery'),
	array('javascript', 'core/observer'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'bootstrap/popover'),
	array('javascript', 'bootstrap/tooltip'),
	array('javascript', 'jqueryui'),
	array('javascript', 'core/app'),
	array('javascript', 'redactor'),
	array('javascript', 'jquery/form'),
	array('css', 'core/app'),
	array('css', 'redactor'),
	array('css', 'jqueryui')
));

// Dashboard
$al->register('css', 'dashboard', 'ccm.dashboard.css');
$al->register('javascript', 'dashboard', 'ccm.dashboard.js');
$al->registerGroup('dashboard', array(
	array('javascript', 'jquery'),
	array('javascript', 'jqueryui'),
	array('javascript', 'jquery/form'),
	array('javascript', 'dashboard'),
	array('javascript', 'backstretch'),
	array('javascript', 'core/observer'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'bootstrap/popover'),
	array('javascript', 'bootstrap/tooltip'),
	array('javascript', 'bootstrap/transition'),
	array('javascript', 'bootstrap/alert'),
	array('javascript', 'core/app'),
	array('javascript', 'redactor'),
	array('css', 'core/app'),
	array('css', 'redactor'),
	array('css', 'jqueryui'),
	array('css', 'dashboard')
));


// Basic styles (used to be in ccm.base.css)
$al->register('css', 'core/captcha', 'frontend/captcha.css');
$al->register('css', 'core/pagination', 'frontend/pagination.css');
$al->register('css', 'core/errors', 'frontend/errors.css');
$al->register('css', 'core/calendar', 'frontend/calendar.css');

// Sitemap
$al->register('javascript', 'core/sitemap', 'ccm.sitemap.js', false, false, false);
$al->register('css', 'core/sitemap', 'ccm.sitemap.css', false, false, false);
$al->registerGroup('core/sitemap', array(
	array('javascript', 'core/sitemap'),
	array('javascript', 'dynatree'),
	array('css', 'dynatree'),
	array('css', 'core/sitemap')
));

// Composer
$al->register('javascript', 'core/composer', 'ccm.composer.js', false, false, false);
$al->register('css', 'core/composer', 'ccm.composer.css', false, false, false);
$al->registerGroup('core/composer', array(
	array('javascript', 'core/composer'),
	array('css', 'core/composer')
));


// Gathering
$al->register('javascript', 'core/gathering', 'ccm.gathering.js');
$al->register('css', 'core/gathering/display', 'ccm.gathering.display.css');
$al->register('css', 'core/gathering/base', 'ccm.gathering.base.css');
$al->registerGroup('core/gathering', array(
	array('javascript', 'core/gathering'),
	array('css', 'core/gathering/base'),
	array('css', 'core/gathering/display')
));

// Conversation
$al->register('javascript', 'core/conversation', 'ccm.conversations.js');
$al->register('css', 'core/conversation', 'ccm.conversations.css');
$al->registerGroup('core/conversation', array(
	array('javascript', 'dropzone'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'core/observer'),
	array('javascript', 'core/conversation'),
	array('css', 'core/conversation'),
	array('css', 'bootstrap/dropdown')
), true);

// Overlay
$al->register('javascript', 'core/overlay', 'overlay/jquery.magnific-popup.js');
$al->register('css', 'core/overlay', 'overlay/jquery.magnific-popup.css');
$al->registerGroup('core/overlay', array(
	array('javascript', 'core/overlay'),
	array('css', 'core/overlay')
));

// My Account
$al->register('javascript', 'core/account', 'ccm.profile.js');
$al->register('css', 'core/account', 'ccm.account.css');
$al->registerGroup('core/account', array(
	array('javascript', 'core/account'),
	array('javascript', 'bootstrap/dropdown'),
	array('css', 'bootstrap/dropdown'),
	array('css', 'core/account')
));