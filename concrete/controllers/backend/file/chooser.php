<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Navigation\Breadcrumb\FileManagerBreadcrumbFactory;
use Concrete\Core\Navigation\NavigationItemTransformer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Tree\Node\NodeTransformer;
use Doctrine\ORM\EntityManager;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class Chooser extends Controller
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Filesystem $filesystem, Manager $manager)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->manager = $manager->setSerializer(new DataArraySerializer());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::shouldRunControllerTask()
     */
    public function shouldRunControllerTask()
    {
        $folder = $this->filesystem->getRootFolder();
        $fp = new Checker($folder);

        return $fp->canSearchFiles();
    }

    public function getRecent()
    {
        $list = new FileList();
        $list->sortByDateAddedDescending();
        $list->setItemsPerPage(20);

        return $this->buildFileListFractalResponse($list);
    }

    public function getFileSets()
    {
        $mySets = [];
        foreach (FileSet::getMySets() as $set) {
            $mySets[] = ['id' => $set->fsID, 'name' => $set->fsName];
        }

        return new JsonResponse($mySets);
    }

    public function getFileSetFiles($id)
    {
        if ($this->app->make('helper/validation/numbers')->integer($id)) {
            $set = FileSet::getByID($id);
            if (is_object($set)) {
                $list = new FileList();
                $list->filterBySet($set);
                $list->sortBy('f.fDateAdded', 'desc');
                $list->setItemsPerPage(20);

                return $this->buildFileListFractalResponse($list);
            }
        }

        throw new \Exception(t('Access Denied'));
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        $searchRepo = $em->getRepository(SavedFileSearch::class);
        $presets = $searchRepo->findAll();

        $searchPresets = [];
        foreach ($presets as $preset) {
            $searchPresets[] = ['id' => $preset->getId(), 'presetName' => $preset->getPresetName()];
        }

        return new JsonResponse($searchPresets);
    }

    public function getSearchPresetFiles($id)
    {
        $em = $this->app->make(EntityManager::class);
        $preset = $em->find(SavedFileSearch::class, $id);
        if (is_object($preset)) {
            $query = $this->app->make(QueryFactory::class)->createFromSavedSearch($preset);
            $list = new FolderItemList();
            $list->enableAutomaticSorting();
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

            return $this->buildFileListFractalResponse($list);
        }
    }

    public function getFolderFiles($folderId = null)
    {
        if ($this->app->make('helper/validation/numbers')->integer($folderId)) {
            $folder = $this->filesystem->getFolder($folderId);
            if (!is_object($folder)) {
                $error = $this->app->make('error');
                $error->add(t('Unable to find the specified folder.'));

                return new JsonResponse($error);
            }
        } else {
            $folder = $this->filesystem->getRootFolder();
        }

        $permissions = new Checker($folder);
        if ($permissions->canSearchFiles()) {
            $list = new FolderItemList();
            $list->filterByParentFolder($folder);
            $list->getQueryObject()->addOrderBy('fv.fvType');
            $list->sortBy('dateModified', 'desc');
            $list->setItemsPerPage(20);
            $list->enableAutomaticSorting();

            return $this->buildFileListFractalResponse($list);
        }

        throw new \Exception(t('Access Denied'));
    }

    public function searchFiles($keyword)
    {
        $list = new FileList();
        $list->filterByKeywords($keyword);
        $list->sortBy('f.fDateAdded', 'desc');
        $list->setItemsPerPage(20);

        return $this->buildFileListFractalResponse($list);
    }

    public function getBreadcrumb($folderId = null)
    {
        if ($this->app->make('helper/validation/numbers')->integer($folderId)) {
            $folder = $this->filesystem->getFolder($folderId);
            if (!is_object($folder)) {
                $error = $this->app->make('error');
                $error->add(t('Unable to find the specified folder.'));

                return new JsonResponse($error);
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

            return JsonResponse::fromJsonString($response->toJson());
        }

        throw new \Exception(t('Access Denied'));
    }

    /**
     * @param FileList|FolderItemList $list
     *
     * @return JsonResponse
     */
    protected function buildFileListFractalResponse($list): JsonResponse
    {
        $adapter = $list->getPaginationAdapter();
        $paginationFactory = $this->app->make(PaginationFactory::class, [$this->request]);
        $pagination = $paginationFactory->deliverPaginationObject($list, new Pagination($list, $adapter));

        $transformer = ($list instanceof FileList) ? new FileTransformer() : new NodeTransformer();
        $collection = new Collection($pagination->getCurrentPageResults(), $transformer);
        $collection->setPaginator(new PagerfantaPaginatorAdapter($pagination, function ($page) {
            return $page;
        }));

        $collection->setMeta([
            'query_params' => [
                'pagination_page' => $list->getQueryPaginationPageParameter(),
                'sort_column' => $list->getQuerySortColumnParameter(),
                'sort_direction' => $list->getQuerySortDirectionParameter(),
            ],
        ]);

        $response = $this->manager->createData($collection);

        return JsonResponse::fromJsonString($response->toJson());
    }
}
