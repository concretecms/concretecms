<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
Loader::library('view');

if (isset($_POST['ttask']) && $_POST['ttask'] == 'preview_theme_customization') {
 	Cache::set('preview_theme_style', $_REQUEST['themeID'], $_POST, 30);
}

$previewCID=intval($_REQUEST['previewCID']);
$themeID=intval($_REQUEST['themeID']);
$ctID=intval($_REQUEST['ctID']);
$collectionType=CollectionType::getByID($ctID);

$c = Page::getByID($previewCID, 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if(!$cp->canEditPageTheme()) throw new Exception(t('Access Denied'));

$v = View::getInstance(); 
if ($themeID > 0) { 
	$th = PageTheme::getByID($themeID);
	if(!file_exists($th->getThemeDirectory()))
		throw new Exception(t('Theme not found in %s', $th->getThemeDirectory()));
	$v->setTheme($th);
}
$v->disableEditing();
$v->disableLinks();
$v->enablePreview();
$v->render($c); 

?>