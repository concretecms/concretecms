<?php
namespace Concrete\Block\DocumentLibrary;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\Set\SetList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\File;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\UrlImmutable;
use Concrete\Core\User\User;
use Doctrine\DBAL\Connection;
use FileAttributeKey;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = '640';
    protected $btInterfaceHeight = '400';
    protected $btTable = 'btDocumentLibrary';
    protected $fileAttributes = [];
    protected $btExportFileFolderColumns = ['folderID'];

    /** @var FileFolder|null */
    protected $rootNode = null;

    /** @var int */
    protected $folderID = 0;

    public function getBlockTypeDescription()
    {
        return t('Add a searchable document library to a page.');
    }

    public function getBlockTypeName()
    {
        return t('Document Library');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::DOCUMENTS
        ];
    }

    public function action_navigate($folderID = 0)
    {
        if (!$this->hideFolders) {
            $parentID = intval($this->folderID);
            /** @var Node $parentFolder */
            if ($parentID && !$parentFolder = FileFolder::getByID($parentID)) {
                return $this->app->make(ResponseFactory::class)->error('Invalid parent folder.');
            }

            /** @var Node $subFolder */
            if (!$subFolder = FileFolder::getByID($folderID)) {
                return $this->app->make(ResponseFactory::class)->error('Invalid folder ID.');
            }

            $breadcrumbs = [$subFolder];

            if ($parentID) {
                // Make sure this folder is a subfolder of the main folder.
                $subsParent = $subFolder->getTreeNodeParentID();
                while ($subsParent && $subsParent != $parentID) {
                    if (!$subsParent = FileFolder::getByID($subsParent)) {
                        break;
                    }
                    $breadcrumbs[] = $subsParent;

                    $subsParent = $subsParent->getTreeNodeParentID();
                }

                if (!$subsParent) {
                    return $this->app->make(ResponseFactory::class)->error('Invalid folder ID.');
                }
            } else {
                $parentFolder = $this->getRootFolder(true);
            }

            $breadcrumbs[] = $parentFolder;

            $this->rootNode = $subFolder;
            $this->view();
            $this->set('breadcrumbs', $this->formatBreadcrumbs(array_reverse($breadcrumbs)));
        } else {
            return $this->view();
        }
    }

    public function on_start()
    {
        $this->fileAttributes = FileKey::getList();
    }

    public function loadData()
    {
        $folderNodes = Node::getNodesOfType('file_folder');
        $folders = [];
        foreach($folderNodes as $folderNode) {
            $folders[$folderNode->getTreeNodeID()] = $folderNode->getTreeNodeDisplayPath();
        }
        $this->set('folders', $folders);

        $fsl = new SetList();
        $fsl->filterByType(Set::TYPE_PUBLIC);
        $r = $fsl->get();
        $sets = [];
        foreach ($r as $fs) {
            $fsp = new \Permissions($fs);
            if ($fsp->canSearchFiles()) {
                $sets[] = $fs;
            }
        }
        $this->set('fileSets', $sets);

        $searchProperties = [
            'date' => t('Date Posted'),
            'type' => t('File Type'),
            'extension' => t('File Extension'),
        ];
        foreach ($this->fileAttributes as $ak) {
            $searchProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('searchProperties', $searchProperties);

        $orderByOptions = [
            'title' => t('Title'),
            'set' => tc('Order of a set', 'Set Order'),
            'date' => t('Date Posted'),
            'filename' => t('Filename'),
        ];
        foreach ($this->fileAttributes as $ak) {
            $orderByOptions['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('orderByOptions', $orderByOptions);
        $viewProperties = [
            'thumbnail' => t('Thumbnail'),
            'filename' => t('Filename'),
            'tags' => t('Tags'),
            'date' => t('Date Posted'),
            'extension' => t('Extension'),
            'size' => t('Size'),
            'description' => t('Description'),
        ];
        foreach ($this->fileAttributes as $ak) {
            $viewProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('viewProperties', $viewProperties);

        $expandableProperties = [
            'image' => t('Image'),
            'description' => t('Description'),
            'tags' => t('Tags'),
            'filename' => t('Filename'),
            'date' => t('Date Posted'),
            'extension' => t('Extension'),
            'size' => t('Size'),
        ];
        foreach ($this->fileAttributes as $ak) {
            $expandableProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('expandableProperties', $expandableProperties);
    }

    public function edit()
    {
        $this->loadData();
        $this->set('selectedSets', (array) json_decode($this->setIds));
        $this->set('searchPropertiesSelected', (array) json_decode($this->searchProperties));
        $viewPropertiesDoNotDisplay = [];
        $viewPropertiesDisplay = [];
        $viewPropertiesDisplaySortable = [];
        $viewProperties = (array) json_decode($this->viewProperties);
        foreach ($viewProperties as $key => $type) {
            switch ($type) {
                case -1:
                    $viewPropertiesDoNotDisplay[] = $key;
                    break;
                case 1:
                    $viewPropertiesDisplay[] = $key;
                    break;
                case 5:
                    $viewPropertiesDisplaySortable[] = $key;
                    break;
            }
        }
        $this->set('viewPropertiesDoNotDisplay', $viewPropertiesDoNotDisplay);
        $this->set('viewPropertiesDisplay', $viewPropertiesDisplay);
        $this->set('viewPropertiesDisplaySortable', $viewPropertiesDisplaySortable);
        $this->set('expandablePropertiesSelected', (array) json_decode($this->expandableProperties));
    }

    public function getSortColumnKey($key, $retrieve = 'filelist')
    {
        if (strpos($key, 'ak_') === 0) {
            if ($retrieve == 'filelist') {
                $akID = substr($key, 3);
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return 'ak_' . $ak->getAttributeKeyHandle();
                }
            } else {
                $akHandle = substr($key, 3);
                $ak = FileKey::getByHandle($akHandle);
                if (is_object($ak)) {
                    return 'ak_' . $ak->getAttributeKeyID();
                }
            }
        }

        $properties = [
            'title' => 'fv.fvTitle',
            'filename' => 'fv.fvFilename',
            'tags' => 'fv.fvTags',
            'date' => 'fv.fvDateAdded',
            'extension' => 'fv.fvExtension',
            'size' => 'fv.fvSize',
            'description' => 'fv.fvDescription',
        ];

        foreach ($properties as $block => $filelist) {
            if ($retrieve == 'filelist' && $key == $block) {
                return $filelist;
            } else {
                if ($retrieve == 'block' && $key == $filelist) {
                    return $block;
                }
            }
        }
    }

    public function add()
    {
        $this->loadData();
        $this->set('setMode', 'any');
        $this->set('enableSearch', 0);
        $this->set('selectedSets', []);
        $this->set('searchPropertiesSelected', []);
        $this->set('expandablePropertiesSelected', []);
        $viewPropertiesDoNotDisplay = [];
        foreach ($this->get('viewProperties') as $key => $name) {
            if (!in_array($key, ['filename', 'size', 'date', 'thumbnail'])) {
                $viewPropertiesDoNotDisplay[] = $key;
            }
        }
        $this->set('viewPropertiesDoNotDisplay', $viewPropertiesDoNotDisplay);
        $this->set('viewPropertiesDisplay', ['thumbnail']);
        $this->set('viewPropertiesDisplaySortable', ['filename', 'size', 'date']);
        $this->set('displayLimit', 20);
        $this->set('downloadFileMethod', 'browser');
        $this->set('heightMode', 'auto');
    }

    protected function setupFolderFileSetFilter(FolderItemList $list)
    {
        $sets = json_decode($this->setIds);

        if (count($sets)) {
            $query = $list->getQueryObject();

            switch ($this->setMode) {
                case 'all':
                    // Show files in ALL sets
                    asort($sets);
                    $sets = array_unique(array_map('intval', $sets));

                    // Set up a subselect that we can join to get file set files
                    $subselect = $query->getConnection()->createQueryBuilder();
                    $subselect
                        ->select('count(distinct fsf.fsID) as sets')
                        ->addSelect('fsf.fID')
                        ->from('FileSetFiles', 'fsf')
                        ->where('fsf.fsID in (:sets)')
                        ->groupBy('fsf.fID');

                    $query
                        ->leftJoin('tf', sprintf('(%s)', $subselect->getSQL()), 'fsf', 'tf.fID = fsf.fID')
                        ->where($query->expr()->andX('fsf.sets=:count', 'fsf.sets > 0'))
                        ->setParameter('sets', $sets, Connection::PARAM_INT_ARRAY)
                        ->setParameter('count', count($sets));

                    break;
                case 'any':
                default:
                    $query->leftJoin('tf', 'FileSetFiles', 'fsf', 'tf.fID = fsf.fID');

                    // Show files in ANY of the sets
                    $expr = $query->expr()->orX($this->hideFolders ? '1=0' : 'nt.treeNodeTypeHandle = "file_folder"');
                    foreach ($sets as $set) {
                        $expr->add($query->expr()->eq('fsf.fsID', $set));
                    }

                    $query->andWhere($expr);
                    break;
            }
        }

        return $list;
    }

    protected function getTableColumns($results)
    {
        $viewProperties = (array) json_decode($this->viewProperties);
        $return = [];
        if (array_key_exists('thumbnail', $viewProperties) && $viewProperties['thumbnail'] > -1) {
            $return[] = 'thumbnail';
        }
        $return[] = 'title';
        foreach ($viewProperties as $key => $type) {
            if ($key == 'thumbnail') {
                continue;
            }

            if ($type > 0) {
                $return[] = $key;
            }
        }

        if ($this->allowInPageFileManagement) {
            foreach ($results as $file) {
                if ($file instanceof File) {
                    $fileObject = $file->getTreeNodeFileObject();
                    $fp = new \Permissions($fileObject);
                    if ($fp->canEditFileProperties()) {
                        $return[] = 'edit_properties';
                        break;
                    }
                }
            }
        }

        $expandableProperties = (array) json_decode($this->expandableProperties);
        if (count($expandableProperties)) {
            $return[] = 'details';
        }

        return $return;
    }

    protected function getTableExpandableProperties()
    {
        $expandableProperties = (array) json_decode($this->expandableProperties);
        $return = [];
        foreach ($expandableProperties as $key) {
            $return[] = $key;
        }

        return $return;
    }

    protected function getTableSearchProperties()
    {
        $searchProperties = (array) json_decode($this->searchProperties);
        $return = [];
        foreach ($searchProperties as $key) {
            $return[] = $key;
        }

        return $return;
    }

    public function getColumnTitle($key)
    {
        switch ($key) {
            case 'title':
                return t('Title');
            case 'image':
                return t('Detail Image');
            case 'edit_properties':
            case 'details':
                return '';
            case 'thumbnail':
                return t('Thumbnail');
            case 'filename':
                return t('Filename');
            case 'description':
                return t('Description');
            case 'tags':
                return t('Tags');
            case 'type':
                return t('Type');
            case 'date':
                return t('Date Posted');
            case 'extension':
                return t('Extension');
            case 'size':
                return t('Size');
            default:
                $akID = substr($key, 3);
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->getAttributeKeyDisplayName();
                }
                break;
        }
    }

    public function getColumnClass($list, $key)
    {
        $class = 'ccm-block-document-library-column-' . $key;
        if ($this->isColumnSortable($key)) {
            $class .= ' ccm-block-document-library-column-sortable';
        }

        $order = ($list->getActiveSortDirection() == 'desc') ? 'desc' : 'asc';
        $orderBy = $this->getSortColumnKey($list->getActiveSortColumn(), 'block');
        if ($orderBy && $orderBy == $key) {
            $class .= ' ccm-block-document-library-active-sort-' . $order;
        }

        return $class;
    }

    public function getSortAction($c, $list, $key)
    {
        $orderBy = $this->getSortColumnKey($list->getActiveSortColumn(), 'block');
        if ($orderBy && $orderBy == $key) {
            $order = ($list->getActiveSortDirection() == 'desc') ? 'asc' : 'desc';
        } else {
            $order = 'asc';
        }

        $url = \URL::to($c);
        if ($query = $url->getQuery()) {
            $query['sort'] = $key;
            $query['dir'] = $order;
            $url = $url->setQuery($query);
        }

        return $url;
    }

    public function isColumnSortable($key)
    {
        if ($key == 'title') {
            return true;
        }
        if ($key == 'details') {
            return false;
        }
        $viewProperties = (array) json_decode($this->viewProperties);

        return isset($viewProperties[$key]) && $viewProperties[$key] == 5;
    }

    public function getSearchValue($key)
    {
        switch ($key) {
            case 'type':
                $form = $this->app->make('helper/form');
                $t1 = Type::getTypeList();
                $types = ['' => t('** File type')];
                foreach ($t1 as $value) {
                    $types[$value] = Type::getGenericTypeText($value);
                }

                return $form->select('type', $types, ['style' => 'width: 120px']);
            case 'extension':
                $form = $this->app->make('helper/form');
                $ext1 = Type::getUsedExtensionList();
                $extensions = ['' => t('** File Extension')];
                foreach ($ext1 as $value) {
                    $extensions[$value] = $value;
                }

                return $form->select('extension', $extensions, ['style' => 'width: 120px']);
            case 'date':
                /** @var \Concrete\Core\Form\Service\Widget\DateTime $wdt */
                $wdt = $this->app->make('helper/form/date_time');
                $allQueries = $this->request->query->all();
                return $wdt->datetime('date_from', $wdt->translate('date_from', $allQueries),
                        true) . t('to') . $wdt->datetime('date_to', $wdt->translate('date_to', $allQueries), true);
            default:
                $akID = substr($key, 3);
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->render('search', null, true);
                }
                break;
        }
    }

    protected function setupFolderAdvancedSearch(FolderItemList $list)
    {
        $query = $list->getQueryObject();

        $searchProperties = (array) json_decode($this->searchProperties);
        $type = $this->request->query->get('type');
        $extension = $this->request->query->get('extension');
        $allQueries = $this->request->query->all();
        foreach ($searchProperties as $column) {
            switch ($column) {
                case 'type':
                    if ($type) {
                        $this->enableSubFolderSearch($list);
                        $list->filterByType($type);
                    }
                    break;
                case 'extension':
                    if ($extension) {
                        $this->enableSubFolderSearch($list);
                        $query->andWhere('fv.fvExtension = :fvExtension');
                        $query->setParameter('fvExtension', $extension);
                    }
                    break;
                case 'date':
                    /** @var \Concrete\Core\Form\Service\Widget\DateTime $wdt */
                    $wdt = $this->app->make('helper/form/date_time');
                    $dateFrom = $wdt->translate('date_from', $allQueries);
                    if ($dateFrom) {
                        $this->enableSubFolderSearch($list);
                        $query->andWhere('fv.fvDateAdded >= :dateFrom');
                        $query->setParameter('dateFrom', $dateFrom);
                    }
                    $dateTo = $wdt->translate('date_to', $allQueries);
                    if ($dateTo) {
                        $this->enableSubFolderSearch($list);
                        if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                            $dateTo = $m[1] . ':59';
                        }
                        $query->andWhere('fv.fvDateAdded >= :dateTo');
                        $query->setParameter('dateTo', $dateTo);
                    }
                    break;
                default:
                    if ($this->request->query->has('search')) {
                        $akID = substr($column, 3);
                        $ak = FileKey::getByID($akID);
                        if (is_object($ak)) {
                            $this->enableSubFolderSearch($list);
                            $type = $ak->getAttributeType();
                            $cnt = $type->getController();
                            $cnt->setRequestArray($allQueries);
                            $cnt->setAttributeKey($ak);
                            $cnt->searchForm($list);
                        }
                    }
                    break;
            }
        }

        return $list;
    }

    public function getColumnValue($key, $file)
    {
        if ($file instanceof \Concrete\Core\Tree\Node\Type\File) {
            $file = $file->getTreeNodeFileObject();
        } elseif ($file instanceof FileFolder) {
            return $this->getFolderColumnValue($key, $file);
        } else {
            return false;
        }

        switch ($key) {
            case 'thumbnail':

                $im = $this->app->make('helper/image');
                if ($file->getTypeObject()->getGenericType() == Type::T_IMAGE && $this->maxThumbWidth && $this->maxThumbHeight) {
                    $thumb = $im->getThumbnail(
                        $file,
                        $this->maxThumbWidth,
                        $this->maxThumbHeight
                    );
                    $thumbnail = new \HtmlObject\Image();
                    $thumbnail->src($thumb->src)->width($thumb->width)->height($thumb->height);
                } else {
                    $thumbnail = $file->getTypeObject()->getThumbnail();
                }

                return $thumbnail;
            case 'image':
                if ($file->getTypeObject()->getGenericType() == Type::T_IMAGE) {
                    return sprintf('<img src="%s" class="img-fluid" />', $file->getRelativePath());
                }
                break;
            case 'edit_properties':
                $fp = new \Permissions($file);
                if ($fp->canEditFileProperties()) {
                    return sprintf('<a href="#" data-document-library-edit-properties="%s" class="ccm-block-document-library-icon"><i class="fas fa-pencil-alt"></i></a>',
                        $file->getFileID());
                }
                break;
            case 'details':
                return sprintf('<a href="#" data-document-library-show-details="%s" class="ccm-block-document-library-details">%s</a>',
                    $file->getFileID(), t('Details'));
            case 'title':
                if ($this->downloadFileMethod == 'force') {
                    return sprintf('<a href="%s">%s</a>', $file->getForceDownloadURL(), $file->getTitle());
                } else {
                    return sprintf('<a href="%s">%s</a>', $file->getDownloadURL(), $file->getTitle());
                }
            case 'filename':
                return $file->getFileName();
            case 'description':
                return $file->getDescription();
            case 'tags':
                return $file->getTags();
            case 'date':
                return $this->app->make('date')->formatDate($file->getDateAdded(), false);
            case 'extension':
                return $file->getExtension();
            case 'size':
                return $file->getSize();
            default:
                $akID = substr($key, 3);
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    $av = $file->getAttributeValueObject($ak);
                    if (is_object($av)) {
                        return $av->getValue('displaySanitized');
                    }
                }
                break;
        }
        return false;
    }

    /**
     * @param int |false $bID BlockID
     * @return Symfony\Component\HttpFoundation\Response | void
     * @throws UserMessageException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function action_upload($bID = false)
    {
        $files = [];
        $r = new \Concrete\Core\File\EditResponse();
        if ($this->bID == $bID) {
            $folder = $this->getRootFolder();
            $fp = new Checker($folder);
            if (!$fp->canAddFiles()) {
                throw new UserMessageException(t("You don't have the permission to upload to %s", $folder->getTreeNodeDisplayName()), 400);
            }

            /** @var \Concrete\Core\File\Service\File $cf */
            $cf = $this->app->make('helper/file');

            if ($this->app->make('token')->validate()) {
                /** @var UploadedFile $file */
                $file = $this->request->files->get('file');
                if ($file && $file->isValid()) {
                    if (!$fp->canAddFileType($cf->getExtension($file->getFilename()))) {
                        throw new UserMessageException(ImportException::describeErrorCode(ImportException::E_FILE_INVALID_EXTENSION));
                    } else {

                        /** @var \Concrete\Core\File\Import\FileImporter $importer */
                        $importer = $this->app->make(FileImporter::class);
                        try {
                            $options = $this->app->make(ImportOptions::class);
                            $options->setImportToFolder($folder);
                            $response = $importer->importUploadedFile($file, '', $options);
                        } catch (ImportException $x) {
                            throw new UserMessageException($x->getMessage());
                        }

                        $file = $response->getFile();
                        if ($this->addFilesToSetID) {
                            $fs = \FileSet::getByID($this->addFilesToSetID);
                            if (is_object($fs)) {
                                $fs->addFileToSet($file);
                            }
                        }
                        /* @var \Concrete\Core\Entity\File\File $file */
                        $files[] = $file;
                        if (!$this->allowInPageFileManagement) {
                            // We're going to set a message to display the next time the page loads.
                            $this->app->make('session')->getFlashBag()->add('document_library_success_message',
                                t2('File added successfully', 'Files added successfully', count($files)));
                        }

                        $r->setFiles($files);
                    }
                } else {
                    throw new UserMessageException(ImportException::describeErrorCode(ImportException::E_PHP_NO_FILE));
                }
            } else {
                $r->setError(new UserMessageException(t('Invalid Token.')));
            }
            $r->setFiles($files);

            return $this->app->make(ResponseFactoryInterface::class)->json($r);
        }





    }

    protected function setupFolderFileFolderFilter(FolderItemList $list)
    {
        if ($this->rootNode) {
            $list->filterByParentFolder($this->rootNode);
        } elseif ((int) $this->folderID !== 0 || !$this->hideFolders) {
            // If we have a subfolder selected, or if hidefolders is disabled
            $list->filterByParentFolder($this->getRootFolder());
        } elseif ((int) $this->folderID === 0 && $this->hideFolders) {
            // If we have the top level folder selected and hidefolders is enabled
            $list->enableSubFolderSearch();
        }

        if ($this->hideFolders) {
            $list->getQueryObject()->andWhere('nt.treeNodeTypeHandle <> "file_folder"');
        }


        return $list;
    }

    public function view()
    {
        $this->loadData();

        $list = new FolderItemList();
        $list = $this->setupFolderFileSetFilter($list);
        $list = $this->setupFolderFileFolderFilter($list);
        $list->ignorePermissions();

        $order = $this->displayOrderDesc ? 'desc' : 'asc';
        $orderBy = $this->getSortColumnKey($this->orderBy, 'filelist');
        if ($orderBy) {
            $list->getQueryObject()->addSelect($orderBy);
            $list->sortBy($orderBy, $order);
        }

        if ($keywords = $this->request('keywords')) {
            $list = $this->setupKeywordSearch($list, $keywords);
        }
        $getSort = $this->request->query->get('sort');
        if ($getSort) {
            $getSort = $this->getSortColumnKey($getSort);
            if ($getSort) {
                $list->getQueryObject()->addSelect($getSort);
                $sortDir = $this->request->query->get('dir');
                if ($sortDir) {
                    $list->sortBy($getSort, $sortDir);
                } else {
                    $list->sortBy($getSort);
                }
            }
        }

        if ($this->tags) {
            $query = $list->getQueryObject();
            $query->andWhere(
                $query->expr()->orX(
                    'nt.treeNodeTypeHandle = "file_folder"',
                    $query->expr()->like('fv.fvTags', ':tags')
                )
            );
            $query->setParameter('tags', '%' . $this->tags . '%');
        }

        if ($this->onlyCurrentUser) {
            $u = $this->app->make(User::class);
            if ($u->isRegistered()) {
                $uID = $u->getUserID();
                $query = $list->getQueryObject();

                $query->andWhere($query->expr()->orX(
                    'nt.treeNodeTypeHandle = "file_folder"',
                    'fv.fvAuthorUID = :fvAuthorUID'));

                $query->setParameter('fvAuthorUID', $uID);
            }
        }

        $list = $this->setupFolderAdvancedSearch($list);
        $list->setItemsPerPage($this->displayLimit);

        $pagination = $list->getPagination();
        $results = $pagination->getCurrentPageResults();

        if ($pagination->getTotalPages() > 1) {
            $pagination = $pagination->renderDefaultView();
            $this->set('pagination', $pagination);
        }

        $this->set('tableColumns', $this->getTableColumns($results));
        $this->set('tableExpandableProperties', $this->getTableExpandableProperties());
        $this->set('tableSearchProperties', $this->getTableSearchProperties());
        $this->set('list', $list);
        $this->set('results', $results);
        $this->set('hideFolders', $this->hideFolders);

        $this->requireAsset('css', 'font-awesome');
        $this->set('canAddFiles', false);
        $fp = \FilePermissions::getGlobal();

        if ($this->allowFileUploading && $fp->canAddFile()) {
            $this->set('canAddFiles', true);
        }

        $bag = $this->app->make('session')->getFlashBag();
        if ($bag->has('document_library_success_message')) {
            $success = $bag->get('document_library_success_message');
            $success = $success[0];
            $this->set('success', $success);
        }
        $this->set('advancedSearchDisplayed', $this->request->query->get('advancedSearchDisplayed') ? true : false);
    }

    public function save($args)
    {
        $args += [
            'folderID' => null,
            'viewProperties' => null,
            'searchProperties' => null,
            'expandableProperties' => null,
            'fsID' => null,
            'setMode' => null,
            'tags' => null,
            'orderBy' => null,
            'displayLimit' => null,
            'maxThumbWidth' => null,
            'maxThumbHeight' => null,
            'heightMode' => null,
            'downloadFileMethod' => null,
            'fixedHeightSize' => null,
            'headerBackgroundColor' => null,
            'addFilesToSetID' => 0,
            'headerBackgroundColorActiveSort' => null,
            'headerTextColor' => null,
            'tableName' => '',
            'tableDescription' => '',
            'rowBackgroundColorAlternate' => null,
        ];

        $data = [
            'folderID' => $args['folderID'],
            'viewProperties' => json_encode(is_array($args['viewProperties']) ? $args['viewProperties'] : []),
            'searchProperties' => json_encode(is_array($args['searchProperties']) ? $args['searchProperties'] : []),
            'expandableProperties' => json_encode(is_array($args['expandableProperties']) ? $args['expandableProperties'] : []),
            'setIds' => json_encode(is_array($args['fsID']) ? $args['fsID'] : []),
            'setMode' => $args['setMode'] == 'all' ? 'all' : 'any',
            'onlyCurrentUser' => empty($args['onlyCurrentUser']) ? 0 : 1,
            'allowInPageFileManagement' => empty($args['allowInPageFileManagement']) ? 0 : 1,
            'allowFileUploading' => empty($args['allowFileUploading']) ? 0 : 1,
            'tags' => $args['tags'],
            'orderBy' => $args['orderBy'],
            'displayLimit' => $args['displayLimit'],
            'displayOrderDesc' => empty($args['displayOrderDesc']) ? 0 : 1,
            'maxThumbWidth' => (int) $args['maxThumbWidth'],
            'maxThumbHeight' => (int) $args['maxThumbHeight'],
            'enableSearch' => empty($args['enableSearch']) ? 0 : 1,
            'heightMode' => $args['heightMode'] == 'fixed' ? 'fixed' : 'auto',
            'downloadFileMethod' => $args['downloadFileMethod'] == 'force' ? 'force' : 'browser',
            'fixedHeightSize' => (int) $args['fixedHeightSize'],
            'headerBackgroundColor' => $args['headerBackgroundColor'],
            'addFilesToSetID' => 0,
            'headerBackgroundColorActiveSort' => $args['headerBackgroundColorActiveSort'],
            'headerTextColor' => $args['headerTextColor'],
            'tableName' => $args['tableName'],
            'tableDescription' => $args['tableDescription'],
            'tableStriped' => empty($args['tableStriped']) ? 0 : 1,
            'rowBackgroundColorAlternate' => empty($args['tableStriped']) ? '' : $args['rowBackgroundColorAlternate'],
            'hideFolders' => (int) !filter_var(array_get($args, 'showFolders'), FILTER_VALIDATE_BOOLEAN),
        ];
        if ((int) $args['addFilesToSetID'] > 0) {
            $fs = \FileSet::getByID($args['addFilesToSetID']);
            if (is_object($fs)) {
                $fsp = new \Permissions($fs);
                if ($fsp->canAddFiles() && $fsp->canSearchFiles()) {
                    $data['addFilesToSetID'] = $fs->getFileSetID();
                }
            }
        }
        parent::save($data);
    }

    /**
     * @param bool $realRoot
     *
     * @return FileFolder
     */
    private function getRootFolder($realRoot = false)
    {
        if (!$realRoot && $folderID = $this->folderID) {
            if ($folder = FileFolder::getByID($folderID)) {
                return $folder;
            } else {
                return new FileFolder();
            }
        }

        $filesystem = $this->app->make(Filesystem::class);

        return $filesystem->getRootFolder();
    }

    private function getFolderColumnValue($key, FileFolder $folder)
    {
        switch ($key) {
            case 'thumbnail':
            case 'image':
                return sprintf(
                    '<i class="fas fa-folder ccm-block-document-library-icon-folder" aria-hidden="true"></i><span class="sr-only">%s</span>',
                    t('folder icon')
                );
            case 'title':
                $view = new BlockView($this->getBlockObject());
                /** @var UrlImmutable $action */
                $action = $this->getActionURL('navigate', $folder->getTreeNodeID());

                return sprintf('<a href="%s">%s</a>', h($action), $folder->getTreeNodeDisplayName());
            case 'filename':
                return $folder->getTreeNodeDisplayName();
            case 'tags':
                return [];
                break;
            case 'date':
                return $this->app->make('date')->formatDate($folder->getDateCreated(), false);
            case 'extension':
            case 'size':
            case 'description':
            case 'details':
            case 'edit_properties':
            default:
                return '';
        }
    }

    private function formatBreadcrumbs($breadcrumbs)
    {
        $view = new BlockView($this->getBlockObject());

        /** @var FileFolder $crumb */
        $return = [];
        foreach ($breadcrumbs as $crumb) {
            if ($crumb->getTreeNodeID() == $this->getRootFolder()->getTreeNodeID()) {
                $action = $this->getBlockObject()->getBlockCollectionObject()->getCollectionLink();
            } else {
                $action = $this->getActionURL('navigate', $crumb->getTreeNodeID());
            }

            $return[] = [
                'url' => $action,
                'name' => $crumb->getTreeNodeDisplayName()
            ];
        }
        return $return;
    }

    protected function enableSubFolderSearch(FolderItemList $list)
    {
        $version = $this->app->make('config')->get('concrete.version');
        if (version_compare($version, '8.3.0a1', '>=')) {
            // This is only possible in 8.3.0 or greater.
            $list->enableSubFolderSearch();
        }
    }

    /**
     * @param FolderItemList $list
     * @param string $keywords
     *
     * @return \Concrete\Core\File\FolderItemList
     */
    private function setupKeywordSearch(FolderItemList $list, $keywords)
    {
        $this->enableSubFolderSearch($list);
        $query = $list->getQueryObject();
        $expressions = [
            $query->expr()->like('fv.fvFilename', ':keywords'),
            $query->expr()->like('fv.fvDescription', ':keywords'),
            $query->expr()->like('fv.fvTitle', ':keywords'),
            $query->expr()->like('fv.fvTags', ':keywords'),
            $query->expr()->like('fv.fvTags', ':keywords'),
            $query->expr()->like('n.treeNodeName', ':keywords'),
        ];

        $keys = FileAttributeKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $query);
        }

        $query->andWhere($query->expr()->orX()->addMultiple($expressions));
        $query->setParameter('keywords', '%' . $keywords . '%');

        return $list;
    }
}
