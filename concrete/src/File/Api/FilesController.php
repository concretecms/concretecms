<?php

namespace Concrete\Core\File\Api;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\FileListTransformer;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class FilesController
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * @param $message
     * @param int $code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function error($message, $code = 400)
    {
        $list = new ErrorList();
        $list->add($message);
        return $list->createResponse($code);
    }

    /**
     * @param $object
     * @param TransformerAbstract $transformer
     * @return Item
     */
    public function response($object, TransformerAbstract $transformer)
    {
        return new Item($object, $transformer);
    }

    public function read($fID)
    {
        $fID = (int) $fID;
        $file = File::getByID($fID);
        if (!$file) {
            return $this->error(t('File not found.'), 404);
        } else {
            $permissions = new Checker($file);
            if (!$permissions->canViewFileInFileManager()) {
                return $this->error(t('You do not have access to read properties about this file.'), 401);
            }
        }

        return $this->response($file, new FileTransformer());
    }

    public function listFiles()
    {
        $searchProvider = $this->app->make(SearchProvider::class);
        $fileList = $searchProvider->getItemList();
        $keywords = $this->request->get('keywords');
        if (!empty($keywords)) {
            $fileList->filterByKeywords($keywords);
        }
        return $this->response($fileList, new FileListTransformer());
    }
}