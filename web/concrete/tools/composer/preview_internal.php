<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
Loader::library('view');

$previewCID=intval($_REQUEST['previewCID']);
$themeID=intval($_REQUEST['themeID']);
$ctID=intval($_REQUEST['ctID']);
$collectionType=CollectionType::getByID($ctID);

$composer = Page::getByPath('/dashboard/composer/write/');
$cp = new Permissions($composer);
if(!$cp->canViewPage()) throw new Exception(t('Access Denied'));

$c = Page::getByID($previewCID, 'RECENT'); //,"ACTIVE"

$v = View::getInstance(); 
$v->disableEditing();
$v->disableLinks();
$v->enablePreview();
$v->render($c); 

?>
