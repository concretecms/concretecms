<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserList;
use Concrete\Core\Validation\CSRF\Token;

$app = Application::getFacadeApplication();
/** @var Token $valt */
$valt = $app->make(Token::class);
/** @var Request $request */
$request = $app->make(Request::class);

$key = $request->request->get("key");
$token = $request->request->get("token");
$term = $request->request->get("term");

$userNames = [];

if ($valt->validate('quick_user_select_' . $key, $token)) {
    $userList = new UserList();
    $userList->filterByFuzzyUserName($term);
    $userList->sortByUserName();
    $userList->setItemsPerPage(7);
    $pagination = $userList->getPagination();
    $users = $pagination->getCurrentPageResults();

    foreach ($users as $ui) {
        $userNames[] = array('text' => $ui->getUserDisplayName(), 'value' => $ui->getUserID());
    }
}

echo json_encode($userNames);