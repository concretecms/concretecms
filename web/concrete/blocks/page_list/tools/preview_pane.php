<?php
use Concrete\Block\PageList\Controller;

$previewMode = true;
$nh = Loader::helper('navigation');
$controller = new Controller();

$_REQUEST['num'] = ($_REQUEST['num'] > 0) ? $_REQUEST['num'] : 0;
$_REQUEST['cThis'] = ($_REQUEST['cParentID'] == $_REQUEST['current_page']) ? '1' : '0';
$_REQUEST['cParentID'] = ($_REQUEST['cParentID'] == 'OTHER') ? $_REQUEST['cParentIDValue'] : $_REQUEST['cParentID'];

$controller->num = $_REQUEST['num'];
$controller->cParentID = $_REQUEST['cParentID'];
$controller->cThis = $_REQUEST['cThis'];
$controller->orderBy = $_REQUEST['orderBy'];
$controller->ptID = $_REQUEST['ptID'];
$controller->rss = $_REQUEST['rss'];
$controller->displayFeaturedOnly = $_REQUEST['displayFeaturedOnly'];
$controller->displayAliases = $_REQUEST['displayAliases'];
$controller->on_start();
$controller->view();
$pages = $controller->get('pages');

require(dirname(__FILE__) . '/../view.php');
exit;
