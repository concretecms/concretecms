<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/frontend/conversations
 * Namespace: Concrete\Controller\Frontend\Conversations
 */
$router->post('/add_file', 'AddFile::handle');
$router->post('/add_message', 'AddMessage::handle');
$router->post('/count_header', 'CountHeader::view');
$router->post('/delete_file', 'DeleteFile::handle');
$router->post('/delete_message', 'DeleteMessage::handle');
$router->post('/edit_message', 'EditMessage::view');
