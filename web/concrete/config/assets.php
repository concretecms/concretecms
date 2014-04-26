<?
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Asset\AssetList as AssetList;
use Concrete\Core\Asset\Asset as Asset;

$al = AssetList::getInstance();

/**
 * Third party libraries we rely on
 */

// jquery
$al->register('javascript', 'jquery', 'js/jquery.js', array('position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false));

// jquery ui
$al->register('javascript', 'jqueryui', 'js/jquery.ui.js', array('minify' => false, 'combine' => false));
$al->register('css', 'jqueryui', 'css/jquery.ui.css', array('minify' => false));

$al->registerGroup('jqueryui', array(
	array('javascript', 'jqueryui'),
	array('css', 'jqueryui')
));

// Underscore
$al->register('javascript', 'underscore', 'js/underscore.js', array('minify' => false));

// dropzone
$al->register('javascript', 'dropzone', 'js/dropzone.js');

// jquery form
$al->register('javascript', 'jquery/form', 'js/jquery.form.js');

// jquery rating
$al->register('javascript', 'jquery/rating', 'js/jquery.rating.js');
$al->register('css', 'jquery/rating', 'css/jquery.rating.css', array('minify' => false));
$al->registerGroup('jquery/rating', array(
	array('javascript', 'jquery/metadata'),
	array('javascript', 'jquery/rating'),
	array('css', 'jquery/rating')
));

// jquery color picker
$al->register('javascript', 'spectrum', 'js/spectrum.js', array('minify' => false));
$al->register('css', 'spectrum', 'css/spectrum.css', array('minify' => false));
$al->registerGroup('core/colorpicker', array(
	array('javascript', 'jquery'),
	array('javascript', 'spectrum'),
	array('css', 'spectrum')
));

// concrete5 font picker
$al->register('javascript', 'core/fontpicker', 'js/font-selector.js', array('minify' => false));
$al->register('css', 'core/fontpicker', 'css/font-selector.css', array('minify' => false));
$al->registerGroup('core/fontpicker', array(
	array('javascript', 'jquery'),
	array('javascript', 'jqueryui'),
	array('javascript', 'core/colorpicker'),
	array('javascript', 'core/fontpicker'),
	array('css', 'jqueryui'),
	array('css', 'core/colorpicker'),
	array('css', 'core/fontpicker')
));


// jquery file uploader
$al->register('javascript', 'jquery/fileupload', 'js/jquery.fileupload.js');
$al->registerGroup('jquery/fileupload', array(
	array('javascript', 'jquery/fileupload')
));

// swfobject
$al->register('javascript', 'swfobject', 'js/swfobject.js');
$al->registerGroup('swfobject', array(
	'javascript', 'swfobject'
));

// redactor
$al->register('javascript', 'redactor', 'js/redactor.js', array('minify' => false));
$al->register('css', 'redactor', 'css/redactor.css');
$al->registerGroup('redactor', array(
	array('javascript', 'redactor'),
	array('css', 'redactor')
));

// backstretch
$al->register('javascript', 'backstretch', 'js/jquery.backstretch.js');

// dynatree
$al->register('javascript', 'dynatree', 'js/dynatree.js', array('minify' => false));
$al->register('css', 'dynatree', 'css/dynatree.css', array('minify' => false));
$al->registerGroup('dynatree', array(
	array('javascript', 'dynatree'),
	array('css', 'dynatree')
));

// hoverIntent
$al->register('javascript', 'hoverintent', 'js/ccm_app/jquery.hoverIntent.js');

// bootstrap
$al->register('javascript', 'bootstrap/dropdown', 'js/bootstrap/dropdown.js');
$al->register('javascript', 'bootstrap/tooltip', 'js/bootstrap/tooltip.js'); // has to come before popover
$al->register('javascript', 'bootstrap/popover', 'js/bootstrap/popover.js');
$al->register('javascript', 'bootstrap/alert', 'js/bootstrap/alert.js');
$al->register('javascript', 'bootstrap/button', 'js/bootstrap/button.js');
$al->register('javascript', 'bootstrap/transition', 'js/bootstrap/transition.js');
$al->register('css', 'bootstrap/dropdown', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap/tooltip', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap/popover', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap/alert', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap/transition', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap/button', 'css/ccm.app.css', array('minify' => false));
$al->register('css', 'bootstrap', 'css/ccm.app.css', array('minify' => false));

/**
 * ## Core functionality and styles
 */

// JS Events
$al->register('javascript', 'core/observer', 'js/ccm.pubsub.js', array('minify' => false));

// Core App
$al->register('css', 'core/app', 'css/ccm.app.css', array('minify' => false));
$al->register('javascript', 'core/app', 'js/ccm.app.js', array('minify' => false, 'combine' => false));
$al->registerGroup('core/app', array(
	array('javascript', 'jquery'),
	array('javascript', 'core/observer'),
	array('javascript', 'underscore'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'bootstrap/tooltip'),
	array('javascript', 'bootstrap/popover'),
	array('javascript', 'jqueryui'),
	array('javascript', 'core/app'),
	array('css', 'core/app'),
	array('css', 'jqueryui')
));

$al->register('javascript', 'bootstrap-editable', 'js/bootstrap-editable.js', array('minify' => false));
$al->register('css', 'core/app/editable-fields', 'css/ccm.editable.fields.css', array('minify' => false));
$al->registerGroup('core/app/editable-fields', array(
	array('javascript', 'jquery'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'bootstrap/tooltip'),
	array('javascript', 'bootstrap/popover'),
	array('javascript', 'jqueryui'),
	array('javascript', 'core/observer'),
	array('javascript', 'underscore'),
	array('javascript', 'core/app'),
	array('javascript', 'bootstrap-editable'),
	array('css', 'core/app/editable-fields'),
	array('javascript', 'jquery/fileupload')
));

// Image Editor
$al->register('javascript', 'kinetic', 'js/kinetic.js');
$al->register('css', 'core/imageeditor', 'css/ccm.image_editor.css');
$al->register('javascript', 'core/imageeditor', 'js/ccm.imageeditor.js');
$al->registerGroup('core/imageeditor', array(
	array('javascript', 'kinetic'),
	array('javascript', 'core/imageeditor'),
	array('css', 'core/imageeditor')
));


// Dashboard
$al->register('css', 'dashboard', 'css/ccm.dashboard.css');
$al->register('javascript', 'dashboard', 'js/ccm.dashboard.js');
$al->registerGroup('dashboard', array(
	array('javascript', 'jquery'),
	array('javascript', 'jqueryui'),
	array('javascript', 'underscore'),
	array('javascript', 'dashboard'),
	array('javascript', 'core/observer'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'bootstrap/tooltip'),
	array('javascript', 'bootstrap/popover'),
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
$al->register('css', 'core/frontend/captcha', 'css/frontend/captcha.css');
$al->register('css', 'core/frontend/pagination', 'css/frontend/pagination.css');
$al->register('css', 'core/frontend/errors', 'css/frontend/errors.css');

// File Manager
$al->register('javascript', 'core/filemanager', 'js/ccm.filemanager.js', array('minify' => false));
$al->register('css', 'core/filemanager', 'css/ccm.filemanager.css', array('minify' => false));
$al->registerGroup('core/filemanager', array(
	array('css', 'core/app'),
	array('css', 'core/filemanager'),
	array('javascript', 'core/observer'),
	array('javascript', 'underscore'),
	array('javascript', 'jqueryui'),
	array('javascript', 'core/app'),
	array('javascript', 'core/filemanager')
));

// Sitemap
$al->register('javascript', 'core/sitemap', 'js/ccm.sitemap.js', array('minify' => false));
$al->register('css', 'core/sitemap', 'css/ccm.sitemap.css', array('minify' => false));
$al->registerGroup('core/sitemap', array(
	array('javascript', 'core/observer'),
	array('javascript', 'underscore'),
	array('javascript', 'jqueryui'),
	array('javascript', 'dynatree'),
	array('javascript', 'core/app'),
	array('javascript', 'core/sitemap'),
	array('css', 'dynatree'),
	array('css', 'core/sitemap')
));

// Topics
$al->register('javascript', 'core/topics', 'js/ccm.topics.js', array('minify' => false));
$al->register('css', 'core/topics', 'css/ccm.topics.css', array('minify' => false));
$al->registerGroup('core/topics', array(
	array('javascript', 'core/observer'),
	array('javascript', 'jqueryui'),
	array('javascript', 'dynatree'),
	array('javascript', 'core/topics'),
	array('css', 'dynatree'),
	array('css', 'core/topics')
));

// Groups (Group Tree)
$al->register('javascript', 'core/groups', 'js/ccm.groups.js', array('minify' => false));
$al->registerGroup('core/groups', array(
	array('javascript', 'core/observer'),
	array('javascript', 'underscore'),
	array('javascript', 'jqueryui'),
	array('javascript', 'dynatree'),
	array('javascript', 'core/groups'),
	array('css', 'dynatree')
));

// Page Type Compose Form
$al->register('css', 'core/composer', 'css/ccm.composer.css', array('minify' => false));
$al->registerGroup('core/composer', array(
	array('css', 'core/composer')
));

// Gathering
$al->register('javascript', 'core/gathering', 'js/ccm.gathering.js');
$al->register('css', 'core/gathering/display', 'css/ccm.gathering.display.css');
$al->register('css', 'core/gathering/base', 'css/ccm.gathering.base.css');
$al->registerGroup('core/gathering', array(
	array('javascript', 'core/gathering'),
	array('javascript', 'redactor'),
	array('css', 'core/gathering/base'),
	array('css', 'core/conversation'),
	array('css', 'core/gathering/display'),
	array('css', 'redactor')
));

// Conversation
$al->register('javascript', 'core/conversation', 'js/ccm.conversations.js');
$al->register('css', 'core/conversation', 'css/ccm.conversations.css');
$al->registerGroup('core/conversation', array(
	array('javascript', 'dropzone'),
	array('javascript', 'bootstrap/dropdown'),
	array('javascript', 'core/observer'),
	array('javascript', 'core/conversation'),
	array('css', 'core/conversation'),
	array('css', 'bootstrap/dropdown')
), true);

// Overlay
$al->register('javascript', 'core/overlay', 'js/overlay/jquery.magnific-popup.js');
$al->register('css', 'core/overlay', 'css/overlay/jquery.magnific-popup.css');
$al->registerGroup('core/overlay', array(
	array('javascript', 'core/overlay'),
	array('css', 'core/overlay')
));

// My Account
$al->register('javascript', 'core/account', 'js/ccm.profile.js');
$al->register('css', 'core/account', 'css/ccm.account.css');
$al->registerGroup('core/account', array(
	array('javascript', 'core/account'),
	array('javascript', 'bootstrap/dropdown'),
	array('css', 'bootstrap/dropdown'),
	array('css', 'core/account')
));
