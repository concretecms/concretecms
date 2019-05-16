<?php

namespace Concrete\Core\File\Api;

use Concrete\Core\Api\ApiController;
use Concrete\Core\Application\Application;
use Concrete\Core\File\File;
use Concrete\Core\File\FileListTransformer;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;

class FilesController extends ApiController
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

        return $this->transform($file, new FileTransformer());
    }

    public function listFiles()
    {
        $searchProvider = $this->app->make(SearchProvider::class);
        $fileList = $searchProvider->getItemList();
        $keywords = $this->request->get('keywords');
        if (!empty($keywords)) {
            $fileList->filterByKeywords($keywords);
        }
        return $this->transform($fileList, new FileListTransformer());
    }
}