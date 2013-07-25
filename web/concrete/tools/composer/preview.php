<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('view');

$previewCID=intval($_REQUEST['previewCID']);

$composer = Page::getByPath('/dashboard/composer/write/');
$cp = new Permissions($composer);
if(!$cp->canViewPage()) throw new Exception(t('Access Denied'));


$c = Page::getByID($previewCID, 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if(!$cp->canEditPageContents()) throw new Exception(t('Access Denied'));

$v = View::getInstance(); 
$v->disableEditing();
$v->disableLinks();
$v->enablePreview();
$v->render($c); 

?>
