<?
defined('C5_EXECUTE') or die("Access Denied.");

$al = AssetList::getInstance();

$al->register('javascript', 'jquery', false, 100, Asset::ASSET_POSITION_HEADER);
$al->register('javascript', 'redactor');
$al->register('css', 'redactor');
$al->register('javascript', 'gathering', 'ccm.gathering.js');
$al->register('css', 'gathering', 'ccm.gathering.css');
$al->register('javascript', 'conversation', 'ccm.conversations.js');
$al->register('javascript', 'dropzone', 'dropzone.js');
$al->register('css', 'conversation', 'ccm.conversations.css');

/** 
 * Bootstrap defaults */
$al->register('javascript', 'bootstrap/dropdown', 'bootstrap/dropdown.js');
$al->register('javascript', 'bootstrap/tooltip', 'bootstrap/tooltip.js');
$al->register('javascript', 'bootstrap/popover', 'bootstrap/popover.js');
$al->register('css', 'bootstrap/dropdown', 'ccm.app.css');
$al->register('css', 'bootstrap/tooltip', 'ccm.app.css');
$al->register('css', 'bootstrap/popover', 'ccm.app.css');
$al->register('css', 'bootstrap', 'ccm.app.css');


$al->registerGroup('redactor', array(
	array('javascript', 'redactor'),
	array('css', 'redactor')
));
$al->registerGroup('gathering', array(
	array('javascript', 'gathering'),
	array('css', 'gathering')
));

$al->registerGroup('conversation', array(
	array('javascript', 'conversation'),
	array('javascript', 'dropzone'),
	array('javascript', 'bootstrap/dropdown'),
	array('css', 'bootstrap/dropdown'),
	array('css', 'conversation')
), true);