<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/block
 * Namespace: Concrete\Controller\Backend
 */

$router->all('/preview', 'Block\Preview::render');
$router->all('/render/', 'Block::render');
$router->all('/action/add/{cID}/{arHandle}/{btID}/{action}', 'Block\Action::add')
    ->setRequirements(['action' => '.+'])
;
$router->all('/action/edit/{cID}/{arHandle}/{bID}/{action}', 'Block\Action::edit')
    ->setRequirements(['action' => '.+'])
;
$router->all('/action/add_composer/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::add_composer');
$router->all('/action/edit_composer/{cID}/{arHandle}/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::edit_composer');
$router->all('/process/alias/{cID}/{arHandle}/{pcID}/{dragAreaBlockID}/{orphanedBlockID}/{stackBlockID}', 'Block\Process::alias');
$router
    ->all('/process/copy/{cID}/{arHandle}/{bID}', 'Block\Process::copy')
    ->setRequirements([
        'cID' => '[1-9]\d*',
        'arHandle' => '.+',
        'bID' => '[1-9]\d*',
    ])
;
$router
    ->all('/process/remove_from_clipboard/{pcID}/{cID}', 'Block\Process::removeFromClipboard')
    ->setRequirements([
        'pcID' => '[1-9]\d*',
        'cID' => '[1-9]\d*',
    ])
;
