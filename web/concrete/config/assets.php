<?
defined('C5_EXECUTE') or die("Access Denied.");

$al = AssetList::getInstance();

/** 
 * Third party libraries we rely on
 */

// jquery
$al->register('javascript', 'jquery', false, 100, Asset::ASSET_POSITION_HEADER);

// dropzone
$al->register('javascript', 'dropzone', 'dropzone.js');

// redactor
$al->register('javascript', 'redactor');
$al->register('css', 'redactor');
$al->registerGroup('redactor', array(
	array('javascript', 'redactor'),
	array('css', 'redactor')
));

// bootstrap
$al->register('javascript', 'bootstrap/dropdown', 'bootstrap/dropdown.js');
$al->register('javascript', 'bootstrap/tooltip', 'bootstrap/tooltip.js', 95); // has to come before popover
$al->register('javascript', 'bootstrap/popover', 'bootstrap/popover.js');
$al->register('css', 'bootstrap/dropdown', 'ccm.app.css');
$al->register('css', 'bootstrap/tooltip', 'ccm.app.css');
$al->register('css', 'bootstrap/popover', 'ccm.app.css');
$al->register('css', 'bootstrap', 'ccm.app.css');

/** 
 * ## Core functionality and styles
 */

// JS Events
$al->register('javascript', 'core/observer', 'ccm.pubsub.js');

// Basic styles (used to be in ccm.base.css)
$al->register('css', 'core/captcha', 'frontend/captcha.css');
$al->register('css', 'core/pagination', 'frontend/pagination.css');
$al->register('css', 'core/errors', 'frontend/errors.css');
$al->register('css', 'core/calendar', 'frontend/calendar.css');

// Gathering
$al->register('javascript', 'core/gathering', 'ccm.gathering.js');
$al->register('css', 'core/gathering', 'ccm.gathering.css');
$al->registerGroup('core/gathering', array(
	array('javascript', 'core/gathering'),
	array('css', 'core/gathering')
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