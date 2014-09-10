<?php
use Concrete\Block\PageList\Controller;
$request = Request::getInstance();
$request->setCurrentPage(Page::getByID($_REQUEST['cID']));
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
$controller->paginate = !!$_REQUEST['paginate'];
$controller->on_start();
$controller->add();
$controller->view();
$pages = $controller->get('pages');

extract($controller->getSets());

require(dirname(__FILE__) . '/../view.php');
exit;
