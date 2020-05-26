<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\User\User as UserObject;
use League\Fractal\Resource\Collection;

class Files
{

    /**
     * @var UserObject
     */
    protected $user;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, UserObject $user)
    {
        $this->filesystem = $filesystem;
        $this->user = $user;
    }

    public function getMine()
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);
        if ($permissions->canViewFileInFileManager()) {
            $list = new FileList();
            $list->filterByAuthorUserID($this->user->getUserID());
            $adapter = $list->getPaginationAdapter();
            $pagination = new Pagination($list, $adapter);
            $pagination->setMaxPerPage(20);
            $collection = new Collection($pagination->getCurrentPageResults(), new FileTransformer());
            return $collection;
        }
        throw new \Exception(t('Access Denied'));
    }

}
