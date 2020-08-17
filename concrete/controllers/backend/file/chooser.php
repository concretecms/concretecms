<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Http\Response;
use Concrete\Core\Navigation\Breadcrumb\FileManagerBreadcrumbFactory;
use Concrete\Core\Navigation\NavigationItemTransformer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Tree\Node\NodeTransformer;
use Concrete\Core\User\User as UserObject;
use Doctrine\ORM\EntityManager;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

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
        parent::__construct();

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
                $mySets[] = ['id' => $set->fsID, 'name' => $set->fsName];
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

    public function getSearchPresets()
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {
            $em = $this->app->make(EntityManager::class);
            $searchRepo = $em->getRepository(SavedFileSearch::class);
            $presets = $searchRepo->findAll();

            $searchPresets = [];
            foreach ($presets as $preset) {
                $searchPresets[] = ['id' => $preset->getId(), 'presetName' => $preset->getPresetName()];
            }

            return new Response(json_encode($searchPresets));
        }

        throw new \Exception(t('Access Denied'));
    }

    public function getSearchPresetFiles($id)
    {
        $folder = $this->filesystem->getRootFolder();
        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {
            $em = $this->app->make(EntityManager::class);
            $preset = $em->find(SavedFileSearch::class, $id);
            if ($preset) {
                $query = $this->app->make(QueryFactory::class)->createFromSavedSearch($preset);
                $list = new FolderItemList();
                foreach ($query->getFields() as $field) {
                    $field->filterList($list);
                }

                $columns = $query->getColumns();
                if (is_object($columns)) {
                    $column = $columns->getDefaultSortColumn();
                    $list->sortBySearchColumn($column);
                }

                $list->getQueryObject()->andWhere('nt.treeNodeTypeHandle = \'file\'');
                $list->setItemsPerPage($query->getItemsPerPage());

                $adapter = $list->getPaginationAdapter();
                $paginationFactory = $this->app->make(PaginationFactory::class);
                $pagination = $paginationFactory->deliverPaginationObject($list, new Pagination($list, $adapter));

                $collection = new Collection($pagination->getCurrentPageResults(), new NodeTransformer());
                $response = $this->manager->createData($collection);

                return new Response($response->toJson());
            }

            throw new \Exception(t('Access Denied'));
        }
    }

    public function getFolderFiles($folderId = null)
    {
        if ($folderId > 0) {
            if ($this->app->make('helper/validation/numbers')->integer($folderId)) {
                $folder = $this->filesystem->getFolder($folderId);
                if (!is_object($folder)) {
                    $error = $this->app->make('error');
                    $error->add(t('Unable to find the specified folder.'));

                    return new Response(json_encode($error));
                }
            }
        } else {
            $folder = $this->filesystem->getRootFolder();
        }

        $permissions = new Checker($folder);

        if ($permissions->canSearchFiles()) {
            $list = new FolderItemList();
            $list->filterByParentFolder($folder);
            $list->sortBy('type');

            $adapter = $list->getPaginationAdapter();
            $paginationFactory = $this->app->make(PaginationFactory::class);
            $pagination = $paginationFactory->deliverPaginationObject($list, new Pagination($list, $adapter));

            $collection = new Collection($pagination->getCurrentPageResults(), new NodeTransformer());
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

    public function getBreadcrumb($folderId = null)
    {
        if ($folderId > 0) {
            if ($this->app->make('helper/validation/numbers')->integer($folderId)) {
                $folder = $this->filesystem->getFolder($folderId);
                if (!is_object($folder)) {
                    $error = $this->app->make('error');
                    $error->add(t('Unable to find the specified folder.'));

                    return new Response(json_encode($error));
                }
            }
        } else {
            $folder = $this->filesystem->getRootFolder();
        }

        $permissions = new Checker($folder);
        if ($permissions->canSearchFiles()) {
            $breadcrumbFactory = $this->app->make(FileManagerBreadcrumbFactory::class);
            $breadcrumb = $breadcrumbFactory->getBreadcrumb($folder);

            $collection = new Collection($breadcrumb, new NavigationItemTransformer());
            $response = $this->manager->createData($collection);

            return new Response($response->toJson());
        }

        throw new \Exception(t('Access Denied'));
    }
}
