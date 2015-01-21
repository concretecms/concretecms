<?
defined('C5_EXECUTE') or die("Access Denied.");

Log::warning( t("Using legacy download tool, please update your code, this tools (as all tools) will be removed in future versions!") );

// Legacy usage throw the fIDs in item
if (isset( $_REQUEST['item'] ) && !isset( $_REQUEST['fID'] ) ) $_REQUEST['fID'] = $_REQUEST['item'];

$controller = \Core::make( '\Concrete\Controller\Backend\File' );
$controller->download();

