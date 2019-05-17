<?php

namespace Concrete\Core\File\Api;

use Concrete\Core\Api\ApiController;
use Concrete\Core\Application\Application;
use Concrete\Core\File\File;
use Concrete\Core\File\FileTransformer;
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

    /**
     * Return detailed information about a file.
     * 
     * @param $fID
     * 
     * @return \League\Fractal\Resource\Item|\Symfony\Component\HttpFoundation\JsonResponse
     */
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
}
