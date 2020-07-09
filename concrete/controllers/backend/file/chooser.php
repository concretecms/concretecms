<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Http\Response;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\User\User as UserObject;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Chooser extends \Concrete\Core\Controller\Controller
{
    /**
     * @var UserObject
     */
    protected $user;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Filesystem $filesystem, UserObject $user, Manager $manager)
    {
        $this->filesystem = $filesystem;
        $this->manager = $manager;
        $this->user = $user;
    }

    public function getRecent()
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);
        if ($permissions->canSearchFiles()) {
            $list = new FileList();
            $list->sortByDateAddedDescending();
            $adapter = $list->getPaginationAdapter();
            $pagination = new Pagination($list, $adapter);
            $pagination->setMaxPerPage(20);
            $collection = new Collection($pagination->getCurrentPageResults(), new FileTransformer());
            $response = $this->manager->createData($collection);
            return new Response($response->toJson());
        }
        throw new \Exception(t('Access Denied'));
    }

    public function getFileSets()
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {

            $sets = $this->app->make(Set::class);
            $mySets = [];
            foreach ($sets->getMySets() as $set) {
                $mySets[] = ["id" => $set->fsID, 'name' => $set->fsName];
            }

            return new Response(json_encode($mySets));
        }

        throw new \Exception(t('Access Denied'));
    }

    public function getFileSetFiles($id)
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {
            $list = new FileList();
            $set = $this->app->make(Set::class);
            $list->filterBySet($set->getByID($id));

            $list->sortByDateAddedDescending();
            $adapter = $list->getPaginationAdapter();
            $pagination = new Pagination($list, $adapter);
            $pagination->setMaxPerPage(20);
            $collection = new Collection($pagination->getCurrentPageResults(), new FileTransformer());
            $response = $this->manager->createData($collection);

            return new Response($response->toJson());
        }

        throw new \Exception(t('Access Denied'));
    }

    public function searchFiles($keyword)
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {
            $list = new FileList();
            $list->filterByKeywords($keyword);
            $list->sortByDateAddedDescending();
            $adapter = $list->getPaginationAdapter();
            $pagination = new Pagination($list, $adapter);
            $pagination->setMaxPerPage(20);
            $collection = new Collection($pagination->getCurrentPageResults(), new FileTransformer());
            $response = $this->manager->createData($collection);

            return new Response($response->toJson());
        }

        throw new \Exception(t('Access Denied'));
    }
}
