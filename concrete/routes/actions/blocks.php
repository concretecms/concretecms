<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/block
 * Namespace: Concrete\Controller\Backend\
 */
$router->all('/preview', 'Block\Preview::render');
$router->all('/render/', 'Block::render');
$router->all('/action/add/{cID}/{arHandle}/{btID}/{action}', 'Block\Action::add')
    ->setRequirements(['action' => '.+']);
$router->all('/action/edit/{cID}/{arHandle}/{bID}/{action}', 'Block\Action::edit')
    ->setRequirements(['action' => '.+']);
$router->all('/action/add_composer/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::add_composer');
$router->all('/action/edit_composer/{cID}/{arHandle}/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::edit_composer');
$router->all('/process/alias/{cID}/{arHandle}/{pcID}/{dragAreaBlockID}', 'Block\Process::alias');
