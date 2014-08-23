<?
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Asset\AssetList as AssetList;
use Concrete\Core\Asset\Asset as Asset;

$al = AssetList::getInstance();

/**
 * Third party libraries we rely on
 */

// jquery

// jquery ui

$al->registerGroup('jquery/ui', array(
    array('javascript', 'jquery/ui'),
    array('css', 'jquery/ui')
));

$al->registerGroup('jquery/rating', array(
    array('javascript', 'jquery/metadata'),
    array('javascript', 'jquery/rating'),
    array('css', 'jquery/rating')
));

// jquery color picker
$al->registerGroup('core/colorpicker', array(
    array('javascript', 'jquery'),
    array('javascript', 'core/events'),
    array('javascript', 'spectrum'),
    array('css', 'spectrum')
));

// font awesome
$al->registerGroup('font-awesome', array(
        array('css', 'font-awesome')
    ));

// concrete5 style customizer palettes
$al->registerGroup('core/style-customizer', array(
    array('javascript', 'jquery'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'core/colorpicker'),
    array('javascript', 'core/app'),
    array('javascript', 'core/file-manager'),
    array('javascript', 'core/style-customizer'),
    array('css', 'core/app'),
    array('css', 'core/file-manager'),
    array('css', 'jquery/ui'),
    array('css', 'core/colorpicker'),
    array('css', 'core/style-customizer')
));



// jquery file uploader
$al->registerGroup('jquery/fileupload', array(
    array('javascript', 'jquery/fileupload')
));

// swfobject
$al->registerGroup('swfobject', array(
    'javascript', 'swfobject'
));

// redactor
$al->registerGroup('redactor', array(
    array('javascript', 'redactor'),
    array('javascript', 'redactor_locale'),
    array('css', 'redactor'),
    array('css', 'font-awesome')
));

// ace editor
$al->registerGroup('ace', array(
    array('javascript', 'ace')
));

// dynatree
$al->registerGroup('dynatree', array(
    array('javascript', 'dynatree'),
    array('javascript', 'dynatree_locale'),
    array('css', 'dynatree')
));

// bootstrap

/**
 * ## Core functionality and styles
 */

// JS Events
$al->register('javascript', 'core/events', 'js/events.js', array('minify' => false));

// Core App
$al->registerGroup('core/app', array(
    array('javascript', 'jquery'),
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'bootstrap/dropdown'),
    array('javascript', 'bootstrap/tooltip'),
    array('javascript', 'bootstrap/popover'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'core/app'),
    array('css', 'core/app'),
    array('css', 'font-awesome'),
    array('css', 'jquery/ui')
));

$al->registerGroup('core/app/editable-fields', array(
    array('javascript', 'jquery'),
    array('javascript', 'bootstrap/dropdown'),
    array('javascript', 'bootstrap/tooltip'),
    array('javascript', 'bootstrap/popover'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'core/app'),
    array('javascript', 'bootstrap-editable'),
    array('css', 'core/app/editable-fields'),
    array('javascript', 'jquery/fileupload')
));

$al->registerGroup('core/imageeditor', array(
    array('javascript', 'kinetic'),
    array('javascript', 'core/imageeditor_locale'),
    array('javascript', 'core/imageeditor'),
    array('css', 'core/imageeditor')
));

$al->registerGroup('dashboard', array(
    array('javascript', 'jquery'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'underscore'),
    array('javascript', 'dashboard'),
    array('javascript', 'core/events'),
    array('javascript', 'bootstrap/dropdown'),
    array('javascript', 'bootstrap/tooltip'),
    array('javascript', 'bootstrap/popover'),
    array('javascript', 'bootstrap/transition'),
    array('javascript', 'bootstrap/alert'),
    array('javascript', 'core/app'),
    array('javascript', 'redactor'),
    array('css', 'core/app'),
    array('css', 'redactor'),
    array('css', 'jquery/ui'),
    array('css', 'font-awesome')
));


// Basic styles (used to be in ccm.base.css)
$al->register('css', 'core/frontend/captcha', 'css/frontend/captcha.css');
$al->register('css', 'core/frontend/pagination', 'css/frontend/pagination.css');
$al->register('css', 'core/frontend/errors', 'css/frontend/errors.css');

// File Manager
$al->registerGroup('core/file-manager', array(
    array('css', 'core/app'),
    array('css', 'core/file-manager'),
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'core/app'),
    array('javascript', 'core/file-manager')
));

// Sitemap
$al->registerGroup('core/sitemap', array(
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'dynatree'),
    array('javascript', 'dynatree_locale'),
    array('javascript', 'core/app'),
    array('javascript', 'core/sitemap'),
    array('css', 'dynatree'),
    array('css', 'core/sitemap')
));

// Topics
$al->registerGroup('core/topics', array(
    array('javascript', 'core/events'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'dynatree'),
    array('javascript', 'dynatree_locale'),
    array('javascript', 'core/topics'),
    array('css', 'dynatree'),
    array('css', 'core/topics')
));

// Groups (Group Tree)
$al->registerGroup('core/groups', array(
    array('javascript', 'core/events'),
    array('javascript', 'underscore'),
    array('javascript', 'jquery/ui'),
    array('javascript', 'dynatree'),
    array('javascript', 'dynatree_locale'),
    array('javascript', 'core/groups'),
    array('css', 'dynatree')
));


// Gathering
$al->registerGroup('core/gathering', array(
    array('javascript', 'core/gathering'),
    array('javascript', 'redactor'),
    array('css', 'core/gathering/base'),
    array('css', 'core/conversation'),
    array('css', 'core/gathering/display'),
    array('css', 'redactor')
));

// Conversation
$al->registerGroup('core/conversation', array(
    array('javascript', 'jquery'),
    array('javascript', 'underscore'),
    array('javascript', 'dropzone'),
    array('javascript', 'bootstrap/dropdown'),
    array('javascript', 'core/events'),
    array('javascript', 'core/conversation'),
    array('css', 'core/conversation'),
    array('css', 'font-awesome'),
    array('css', 'bootstrap/dropdown')
), true);

// Lightbox
$al->registerGroup('core/lightbox', array(
    array('javascript', 'jquery'),
    array('javascript', 'core/lightbox'),
    array('javascript', 'core/lightbox/launcher'),
    array('css', 'core/lightbox')
));

// My Account
$al->registerGroup('core/account', array(
    array('javascript', 'core/account'),
    array('javascript', 'bootstrap/dropdown'),
    array('css', 'bootstrap/dropdown'),
    array('css', 'core/account')
));
