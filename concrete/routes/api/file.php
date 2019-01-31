<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\File\FileList;

$router->get('/file/info/{fID}', function($fID) {

    $fID = (int) $fID;

    $file = File::getByID($fID);

    if (is_object($file)) {
        return new \League\Fractal\Resource\Item($file, new \Concrete\Core\File\FileTransformer());
    } else {
        return [];
    }


})->getRoute()->setRequirement('fID' ,'[0-9]+');


$router->get('/file/list', function() use ($app) {


    $fileList =  new FileList();
    $fileList->ignorePermissions();

    /** @var $request \Concrete\Core\Http\Request */
    $request = $app->make(\Concrete\Core\Http\Request::class);

    $keywords = $request->get('keywords');
    $page =  (int) $request->get('page');
    $maxPerPage = (int) $request->get('items_per_page');
    if (!empty($keywords)) {
        $fileList->filterByKeywords($keywords);
    }
    if (!empty($maxPerPage) && $maxPerPage > 1) {
        $fileList->setItemsPerPage( $maxPerPage);
    }
    if (!empty($page) && $page > 1) {
        $request->query->set('ccm_paging_fl', $page);
    }

        return new \League\Fractal\Resource\Item($fileList, new \Concrete\Core\File\FileListTransformer());

});