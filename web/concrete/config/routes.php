<?
defined('C5_EXECUTE') or die("Access Denied.");

$rl = Router::getInstance();

/** 
 * Install
 */
$rl->register('installer', '/install', 'InstallController');
//$rl->register('tools', '/tools/*', 'ToolController');