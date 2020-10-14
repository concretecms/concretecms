<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Http\Request;
use Concrete\Core\Page\PageList;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
/** @var Token $valt */
$valt = $app->make(Token::class);
/** @var Request $request */
$request = $app->make(Request::class);

$key = $request->request->get("key");
$token = $request->request->get("token");
$term = $request->request->get("term");

$pageNames = [];

if ($valt->validate('quick_page_select_' . $key, $token)) {
    $pageList = new PageList();
    // $pageList->filterByFuzzyUserName($term);
    $pageList->filterByName($term);
    // $pageList->sortByUserName();
    $pageList->setItemsPerPage(7);
    $pagination = $pageList->getPagination();
    $pages = $pagination->getCurrentPageResults();

    foreach ($pages as $page) {
        $pageNames[] = ['text' => $page->getCollectionName(), 'value' => $page->getCollectionID()];
    }
}

echo json_encode($pageNames);
