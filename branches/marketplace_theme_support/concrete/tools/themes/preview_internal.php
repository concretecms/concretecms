<?

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('collection_types');
Loader::library('view');

$previewCID=intval($_REQUEST['previewCID']);
$themeID=intval($_REQUEST['themeID']);
$ctID=intval($_REQUEST['ctID']);
$collectionType=CollectionType::getByID($ctID);

$c = Page::getByID($previewCID, 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if(!$cp->canWrite()) throw new Exception('Access Denied');

$v = View::getInstance(); 
$th = PageTheme::getByID($themeID);
if(!file_exists($th->getThemeDirectory()))
	throw new Exception('Theme not found in '.$th->getThemeDirectory());
$v->setTheme($th);
$v->disableEditing();
$v->render($c); 

?>