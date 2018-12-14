<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\File\File;

$router->get('/file/info/{fID}', function($fID) {

    $fID = (int) $fID;

    $file = File::getByID($fID);

    if (is_object($file)) {
        return new \League\Fractal\Resource\Item($file, new \Concrete\Core\File\FileTransformer());
    } else {
        return [];
    }


})->getRoute()->setRequirement('fID' ,'[0-9]+');