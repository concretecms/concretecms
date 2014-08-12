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
$al->register('javascript', 'jquery/ui', 'js/jquery-ui.js', array('minify' => false, 'combine' => false));
$al->register('css', 'jquery/ui', 'css/jquery-ui.css', array('minify' => false));

$al->registerGroup('jquery/ui', array(
    array('javascript', 'jquery/ui'),
    array('css', 'jquery/ui')
));

// Underscore
$al->register('javascript', 'underscore', 'js/underscore.js', array('minify' => false));

// dropzone
$al->register('javascript', 'dropzone', 'js/dropzone.js');

// jquery form
$al->register('javascript', 'jquery/form', 'js/jquery-form.js');

// picturefill
$al->register('javascript', 'picturefill', 'js/picturefill.js', array('minify' => false));

// jquery rating
$al->register('javascript', 'jquery/rating', 'js/jquery.rating.js');
$al->register('css', 'jquery/rating', 'css/jquery-rating.css', array('minify' => false));
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
    array('javascript', 'core/events'),
    array('javascript', 'spectrum'),
    array('css', 'spectrum')
));

// font awesome
$al->register('css', 'font-awesome', 'css/font-awesome.css', array('minify' => false));
$al->registerGroup('font-awesome', array(
        array('css', 'font-awesome')
    ));

// concrete5 style customizer palettes
$al->register('javascript', 'core/style-customizer', 'js/style-customizer.js', array('minify' => false));
$al->register('css', 'core/style-customizer', 'css/style-customizer.css', array('minify' => false));
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
$al->register('javascript', 'jquery/fileupload', 'js/jquery-fileupload.js');
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
$al->register(
    'javascript',
    'redactor_locale',
    REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_redactor_js',
    array(
        'combine' => false,
        'minify' => false,
        'local' => false
    )
);
$al->register('css', 'redactor', 'css/redactor.css');
$al->registerGroup('redactor', array(
    array('javascript', 'redactor'),
    array('javascript', 'redactor_locale'),
    array('css', 'redactor'),
    array('css', 'font-awesome')
));

// ace editor
$al->register('javascript', 'ace', 'js/ace/ace.js', array('minify' => false));
$al->registerGroup('ace', array(
    array('javascript', 'ace')
));

// backstretch
$al->register('javascript', 'backstretch', 'js/backstretch.js');

// dynatree
$al->register('javascript', 'dynatree', 'js/dynatree.js', array('minify' => false));
$al->register(
    'javascript',
    'dynatree_locale',
    REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_dynatree_js',
    array(
        'combine' => false,
        'minify' => false,
        'local' => false
    )
);
$al->register('css', 'dynatree', 'css/dynatree.css', array('minify' => false));
$al->registerGroup('dynatree', array(
    array('javascript', 'dynatree'),
    array('javascript', 'dynatree_locale'),
    array('css', 'dynatree')
));

// bootstrap
$al->register('javascript', 'bootstrap/dropdown', 'js/bootstrap/dropdown.js');
$al->register('javascript', 'bootstrap/tooltip', 'js/bootstrap/tooltip.js'); // has to come before popover
$al->register('javascript', 'bootstrap/popover', 'js/bootstrap/popover.js');
$al->register('javascript', 'bootstrap/alert', 'js/bootstrap/alert.js');
$al->register('javascript', 'bootstrap/button', 'js/bootstrap/button.js');
$al->register('javascript', 'bootstrap/transition', 'js/bootstrap/transition.js');
$al->register('css', 'bootstrap/dropdown', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap/tooltip', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap/popover', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap/alert', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap/transition', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap/button', 'css/app.css', array('minify' => false));
$al->register('css', 'bootstrap', 'css/app.css', array('minify' => false));

/**
 * ## Core functionality and styles
 */

// JS Events
$al->register('javascript', 'core/events', 'js/events.js', array('minify' => false));

// Core App
$al->register('css', 'core/app', 'css/app.css', array('minify' => false));
$al->register('javascript', 'core/app', 'js/app.js', array('minify' => false, 'combine' => false));
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

$al->register('javascript', 'bootstrap-editable', 'js/bootstrap-editable.js', array('minify' => false));
$al->register('css', 'core/app/editable-fields', 'css/editable-fields.css', array('minify' => false));
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

// Image Editor
$al->register('javascript', 'kinetic', 'js/kinetic.js');
$al->register('css', 'core/imageeditor', 'css/image-editor.css');
$al->register('javascript', 'core/imageeditor', 'js/image-editor.js');
$al->registerGroup('core/imageeditor', array(
    array('javascript', 'kinetic'),
    array('javascript', 'core/imageeditor'),
    array('css', 'core/imageeditor')
));


// Dashboard
$al->register('javascript', 'dashboard', 'js/dashboard.js');
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
$al->register('javascript', 'core/file-manager', 'js/file-manager.js', array('minify' => false));
$al->register('css', 'core/file-manager', 'css/file-manager.css', array('minify' => false));
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
$al->register('javascript', 'core/sitemap', 'js/sitemap.js', array('minify' => false));
$al->register('css', 'core/sitemap', 'css/sitemap.css', array('minify' => false));
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
$al->register('javascript', 'core/topics', 'js/topics.js', array('minify' => false));
$al->register('css', 'core/topics', 'css/topics.css', array('minify' => false));
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
$al->register('javascript', 'core/groups', 'js/groups.js', array('minify' => false));
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
$al->register('javascript', 'core/gathering', 'js/gathering.js');
$al->register('css', 'core/gathering/display', 'css/gathering/display.css');
$al->register('css', 'core/gathering/base', 'css/gathering/base.css');
$al->registerGroup('core/gathering', array(
    array('javascript', 'core/gathering'),
    array('javascript', 'redactor'),
    array('css', 'core/gathering/base'),
    array('css', 'core/conversation'),
    array('css', 'core/gathering/display'),
    array('css', 'redactor')
));

// Conversation
$al->register('javascript', 'core/conversation', 'js/conversations.js');
$al->register('css', 'core/conversation', 'css/conversations.css');
$al->registerGroup('core/conversation', array(
    array('javascript', 'jquery'),
    array('javascript', 'dropzone'),
    array('javascript', 'bootstrap/dropdown'),
    array('javascript', 'core/events'),
    array('javascript', 'core/conversation'),
    array('css', 'core/conversation'),
    array('css', 'font-awesome'),
    array('css', 'bootstrap/dropdown')
), true);

// Lightbox
$al->register('javascript', 'core/lightbox', 'js/jquery-magnific-popup.js');
$al->register('javascript', 'core/lightbox/launcher', 'js/lightbox.js');
$al->register('css', 'core/lightbox', 'css/jquery-magnific-popup.css');
$al->registerGroup('core/lightbox', array(
    array('javascript', 'jquery'),
    array('javascript', 'core/lightbox'),
    array('javascript', 'core/lightbox/launcher'),
    array('css', 'core/lightbox')
));

// My Account
$al->register('javascript', 'core/account', 'js/account.js');
$al->register('css', 'core/account', 'css/account.css');
$al->registerGroup('core/account', array(
    array('javascript', 'core/account'),
    array('javascript', 'bootstrap/dropdown'),
    array('css', 'bootstrap/dropdown'),
    array('css', 'core/account')
));
