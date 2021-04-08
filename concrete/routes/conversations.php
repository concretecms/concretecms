<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/frontend/conversations
 * Namespace: Concrete\Controller\Frontend\Conversations
 */

$router->post('/add_file', 'AddFile::view');
$router->post('/add_message', 'AddMessage::view');
$router->post('/count_header', 'CountHeader::view');
$router->post('/delete_file', 'DeleteFile::view');
$router->post('/delete_message', 'DeleteMessage::view');
$router->post('/edit_message', 'EditMessage::view');
$router->post('/flag_message/{asJSON}', 'FlagMessage::view')
    ->setRequirements([
        'asJSON' => '^(?:0|1)$',
    ])
;
$router->post('/get_rating', 'GetRating::view');
$router->post('/message_detail', 'MessageDetail::view');
$router->post('/message_page', 'MessagePage::view');
$router->post('/rate', 'Rate::view');
$router->post('/update_message', 'UpdateMessage::view');
$router->post('/view_ajax', 'ViewAjax::view');
