<?php

namespace Concrete\Block\DocumentLibrary;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\Set\SetList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\File;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Url\UrlImmutable;
use Concrete\Core\User\User;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string
     */
    protected $btInterfaceWidth = '640';

    /**
     * @var string
     */
    protected $btInterfaceHeight = '400';

    /**
     * @var string
     */
    protected $btTable = 'btDocumentLibrary';

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\FileKey[]
     */
    protected $fileAttributes = [];

    /**
     * @var string[]
     */
    protected $btExportFileFolderColumns = ['folderID'];

    /**
     * @var FileFolder|null
     */
    protected $rootNode;

    /**
     * @var int
     */
    protected $folderID = 0;

    /**
     * @var bool|null
     */
    protected $allowInPageFileManagement;

    /**
     * @var bool|null
     */
    protected $hideFolders;

    /**
     * @var string
     */
    protected $setIds = '';

    /**
     * @var string
     */
    protected $searchProperties;

    /**
     * @var string
     */
    protected $viewProperties;

    /**
     * @var string
     */
    protected $expandableProperties;

    /**
     * @var string
     */
    protected $setMode;

    /**
     * @var int|null
     */
    protected $maxThumbWidth;

    /**
     * @var int|null
     */
    protected $maxThumbHeight;

    /**
     * @var string|null
     */
    protected $downloadFileMethod;

    /**
     * @var int|null
     */
    protected $addFilesToSetID;

    /**
     * @var bool
     */
    protected $displayOrderDesc = false;

    /**
     * @var string|null
     */
    protected $orderBy;

    /**
     * @var string|null
     */
    protected $tags;

    /**
     * @var int|null
     */
    protected $onlyCurrentUser;

    /**
     * @var int|null
     */
    protected $displayLimit;

    /**
     * @var int|null
     */
    protected $allowFileUploading;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Add a searchable document library to a page.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Document Library');
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::DOCUMENTS,
        ];
    }

    /**
     * @param int|string $folderID
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function action_navigate($folderID = 0)
    {
        if (!$this->hideFolders) {
            $parentID = (int) $this->folderID;
            $parentFolder = null;

            if ($parentID > 0) {
                /** @var FileFolder|null $parentFolder */
                $parentFolder = FileFolder::getByID($parentID);
                if (!$parentFolder) {
                    return $this->app->make(ResponseFactory::class)->error('Invalid parent folder.');
                }
            }

            /** @var FileFolder|null $subFolder */
            $subFolder = FileFolder::getByID($folderID);
            if (!$subFolder) {
                return $this->app->make(ResponseFactory::class)->error('Invalid folder ID.');
            }

            $breadcrumbs = [$subFolder];

            if ($parentID) {
                // Make sure this folder is a subfolder of the main folder.
                $subsParent = $subFolder->getTreeNodeParentID();
                while ($subsParent && $subsParent != $parentID) {
                    $subsParent = FileFolder::getByID($subsParent);
                    if (!$subsParent) {
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
            $this->view();
        }
    }

    /**
     * @return void
     */
    public function on_start()
    {
        /** @phpstan-ignore-next-line */
        $this->fileAttributes = FileKey::getList();
    }

    /**
     * @return void
     */
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
            $fsp = new Checker($fs);
            /** @phpstan-ignore-next-line */
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

    /**
     * @return void
     */
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

    /**
     * @param string $key
     * @param string $retrieve 'block' or 'filelist'
     *
     * @return string|null
     */
    public function getSortColumnKey($key, $retrieve = 'filelist')
    {
        if (strpos($key, 'ak_') === 0) {
            if ($retrieve === 'filelist') {
                $akID = substr($key, 3);
                /** @phpstan-ignore-next-line */
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return 'fsi.ak_' . $ak->getAttributeKeyHandle();
                }
            } else {
                $akHandle = substr($key, 3);
                $ak = FileKey::getByHandle($akHandle);
                if (is_object($ak)) {
                    return 'fsi.ak_' . $ak->getAttributeKeyID();
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
            if ($retrieve === 'filelist' && $key === $block) {
                return $filelist;
            }

            if ($retrieve === 'block' && $key === $filelist) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @return void
     */
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

    /**
     * @param string $key
     *
     * @return string|void
     */
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
                /** @phpstan-ignore-next-line */
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->getAttributeKeyDisplayName();
                }
                break;
        }
    }

    /**
     * @param FolderItemList $list
     * @param string $key
     *
     * @return string
     */
    public function getColumnClass($list, $key)
    {
        $class = 'ccm-block-document-library-column-' . $key;
        if ($this->isColumnSortable($key)) {
            $class .= ' ccm-block-document-library-column-sortable';
        }

        $order = ($list->getActiveSortDirection() === 'desc') ? 'desc' : 'asc';
        $orderBy = $this->getSortColumnKey($list->getActiveSortColumn(), 'block');
        if ($orderBy && $orderBy === $key) {
            $class .= ' ccm-block-document-library-active-sort-' . $order;
        }

        return $class;
    }

    /**
     * @param Page $c
     * @param FolderItemList $list
     * @param string $key
     *
     * @return mixed
     */
    public function getSortAction($c, $list, $key)
    {
        $orderBy = $this->getSortColumnKey($list->getActiveSortColumn(), 'block');
        if ($orderBy && $orderBy === $key) {
            $order = ($list->getActiveSortDirection() === 'desc') ? 'asc' : 'desc';
        } else {
            $order = 'asc';
        }

        $url = app(ResolverManagerInterface::class)->resolve([$c]);
        $query = $url->getQuery();
        if ($query) {
            $query['sort'] = $key;
            $query['dir'] = $order;
            $url = $url->setQuery($query);
        }

        return $url;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isColumnSortable($key)
    {
        if ($key === 'title') {
            return true;
        }
        if ($key === 'details') {
            return false;
        }
        $viewProperties = (array) json_decode($this->viewProperties);

        return isset($viewProperties[$key]) && $viewProperties[$key] == 5;
    }

    /**
     * @param string $key
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string|void
     */
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

                return $wdt->datetime(
                    'date_from',
                    $wdt->translate('date_from', $allQueries),
                    true
                ) . t('to') . $wdt->datetime('date_to', $wdt->translate('date_to', $allQueries), true);
            default:
                $akID = substr($key, 3);
                /** @phpstan-ignore-next-line */
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->render('search', null, true);
                }
                break;
        }
    }

    /**
     * @param string $key
     * @param FileFolder|File $file
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return false|\HtmlObject\Image|string|null false if nothing is returned, string|null for normal columns and Image object for thumbnails
     */
    public function getColumnValue($key, $file)
    {
        if ($file instanceof \Concrete\Core\Tree\Node\Type\File) {
            $fileObject = $file->getTreeNodeFileObject();
            if (!$fileObject) {
                return false;
            }
        } elseif ($file instanceof FileFolder) {
            return $this->getFolderColumnValue($key, $file);
        } else {
            return false;
        }

        switch ($key) {
            case 'thumbnail':

                $im = $this->app->make('helper/image');
                /** @php-stan-ignore-next-line */
                if ($fileObject->getTypeObject()->getGenericType() === Type::T_IMAGE && $this->maxThumbWidth && $this->maxThumbHeight) {
                    $thumb = $im->getThumbnail(
                        $fileObject,
                        $this->maxThumbWidth,
                        $this->maxThumbHeight
                    );
                    $thumbnail = new \HtmlObject\Image();
                    /** @phpstan-ignore-next-line */
                    $thumbnail->src($thumb->src)->width($thumb->width)->height($thumb->height);
                } else {
                    /** @php-stan-ignore-next-line */
                    $thumbnail = $fileObject->getTypeObject()->getThumbnail();
                }

                return $thumbnail;
            case 'image':
                if ($fileObject->getTypeObject()->getGenericType() === Type::T_IMAGE) {
                    return sprintf('<img src="%s" class="img-fluid" />', $fileObject->getRelativePath());
                }
                break;
            case 'edit_properties':
                $fp = new Checker($fileObject);
                /** @phpstan-ignore-next-line */
                if ($fp->canEditFileProperties()) {
                    return sprintf(
                        '<a href="#" data-document-library-edit-properties="%s" class="ccm-block-document-library-icon"><i class="fas fa-pencil-alt"></i></a>',
                        $fileObject->getFileID()
                    );
                }
                break;
            case 'details':
                return sprintf(
                    '<a href="#" data-document-library-show-details="%s" class="ccm-block-document-library-details">%s</a>',
                    $fileObject->getFileID(),
                    t('Details')
                );
            case 'title':
                if ($this->downloadFileMethod === 'force') {
                    return sprintf('<a href="%s">%s</a>', $fileObject->getForceDownloadURL(), $fileObject->getTitle());
                }

                    return sprintf('<a href="%s">%s</a>', $fileObject->getDownloadURL(), $fileObject->getTitle());
            case 'filename':
                return $fileObject->getFileName();
            case 'description':
                return $fileObject->getDescription();
            case 'tags':
                return $fileObject->getTags();
            case 'date':
                return $this->app->make('date')->formatDate($fileObject->getDateAdded(), false);
            case 'extension':
                return $fileObject->getExtension();
            case 'size':
                return $fileObject->getSize();
            default:
                $akID = substr($key, 3);
                /** @phpstan-ignore-next-line */
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    $av = $fileObject->getAttributeValueObject($ak);
                    if (is_object($av)) {
                        return $av->getValue('displaySanitized');
                    }
                }
                break;
        }

        return false;
    }

    /**
     * @param int|false $bID BlockID
     *
     * @throws UserMessageException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|void
     */
    public function action_upload($bID = false)
    {
        $files = [];
        $r = new \Concrete\Core\File\EditResponse();
        if ($this->bID == $bID) {
            $fp = FilePermissions::getGlobal();
            /** @var \Concrete\Core\File\Service\File $cf */
            $cf = $this->app->make('helper/file');

            if ($this->app->make('token')->validate()) {
                /** @var UploadedFile|null $file */
                $file = $this->request->files->get('file');
                if ($file && $file->isValid()) {
                    if (!$fp->canAddFileType($cf->getExtension($file->getFilename()))) {
                        throw new UserMessageException(ImportException::describeErrorCode(ImportException::E_FILE_INVALID_EXTENSION));
                    }

                    /** @var \Concrete\Core\File\Import\FileImporter $importer */
                    $importer = $this->app->make(FileImporter::class);
                    try {
                            $response = $importer->importUploadedFile($file);
                        } catch (ImportException $x) {
                            throw new UserMessageException($x->getMessage());
                        }
                    $file = $response->getFile();
                    if ($this->addFilesToSetID) {
                        $fs = Set::getByID($this->addFilesToSetID);
                        if (is_object($fs)) {
                            $fs->addFileToSet($file);
                        }
                    }
                    // @var \Concrete\Core\Entity\File\File $file
                    $files[] = $file;
                    if (!$this->allowInPageFileManagement) {
                        // We're going to set a message to display the next time the page loads.
                        $this->app->make('session')->getFlashBag()->add(
                            'document_library_success_message',
                            t2('File added successfully', 'Files added successfully', count($files))
                        );
                    }

                    $r->setFiles($files);
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

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $this->loadData();

        $list = new FolderItemList();
        $list = $this->setupFolderFileSetFilter($list);
        $list = $this->setupFolderFileFolderFilter($list);
        $list->ignorePermissions();

        $order = $this->displayOrderDesc ? 'desc' : 'asc';
        $orderBy = $this->getSortColumnKey($this->orderBy);
        if ($orderBy) {
            $list->getQueryObject()->addSelect($orderBy);
            $list->sortBy($orderBy, $order);
        }

        $keywords = $this->request('keywords');
        if ($keywords) {
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
                    'fv.fvAuthorUID = :fvAuthorUID'
                ));

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
        $fp = FilePermissions::getGlobal();

        if ($this->allowFileUploading && $fp->canAddFile()) {
            $this->set('canAddFiles', true);
        }

        $bag = $this->app->make('session')->getFlashBag();
        if ($bag->has('document_library_success_message')) {
            $success = $bag->get('document_library_success_message');
            $success = $success[0];
            $this->set('success', $success);
        }
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return void
     */
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
            $fs = Set::getByID($args['addFilesToSetID']);
            if (is_object($fs)) {
                /** @phpstan-ignore-next-line */
                $fsp = new Checker($fs);
                /** @phpstan-ignore-next-line */
                if ($fsp->canAddFiles() && $fsp->canSearchFiles()) {
                    $data['addFilesToSetID'] = $fs->getFileSetID();
                }
            }
        }
        parent::save($data);
    }

    /**
     * @param FolderItemList $list
     *
     * @return FolderItemList
     */
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
                        ->groupBy('fsf.fID')
                    ;

                    $query
                        ->leftJoin('tf', sprintf('(%s)', $subselect->getSQL()), 'fsf', 'tf.fID = fsf.fID')
                        ->where($query->expr()->andX('fsf.sets=:count', 'fsf.sets > 0'))
                        ->setParameter('sets', $sets, Connection::PARAM_INT_ARRAY)
                        ->setParameter('count', count($sets))
                    ;

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

    /**
     * @param array<File|FileFolder> $results
     *
     * @return string[]
     */
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
                    if ($fileObject) {
                        $fp = new Checker($fileObject);
                        /** @phpstan-ignore-next-line */
                        if ($fp->canEditFileProperties()) {
                            $return[] = 'edit_properties';
                            break;
                        }
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

    /**
     * @return string[]
     */
    protected function getTableExpandableProperties()
    {
        $expandableProperties = (array) json_decode($this->expandableProperties);
        $return = [];
        foreach ($expandableProperties as $key) {
            $return[] = $key;
        }

        return $return;
    }

    /**
     * @return string[]
     */
    protected function getTableSearchProperties()
    {
        $searchProperties = (array) json_decode($this->searchProperties);
        $return = [];
        foreach ($searchProperties as $key) {
            $return[] = $key;
        }

        return $return;
    }

    /**
     * @param FolderItemList $list
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return FolderItemList
     */
    protected function setupFolderAdvancedSearch(FolderItemList $list)
    {
        $query = $list->getQueryObject();

        $category = $this->app->make(FileCategory::class);
        $table = $category->getIndexedSearchTable();
        $query->leftJoin('fv', $table, 'fis', 'fv.fID = fis.fID');

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
                    $akID = substr($column, 3);
                    /** @phpstan-ignore-next-line */
                    $ak = FileKey::getByID($akID);
                    if (is_object($ak)) {
                        $this->enableSubFolderSearch($list);
                        $type = $ak->getAttributeType();
                        $cnt = $type->getController();
                        $cnt->setRequestArray($allQueries);
                        $cnt->setAttributeKey($ak);
                        $cnt->searchForm($list);
                    }
                    break;
            }
        }

        return $list;
    }

    /**
     * @param FolderItemList $list
     *
     * @return FolderItemList
     */
    protected function setupFolderFileFolderFilter(FolderItemList $list)
    {
        if ($this->rootNode) {
            $list->filterByParentFolder($this->rootNode);
        } elseif ((int) $this->folderID !== 0 || !$this->hideFolders) {
            // If we have a subfolder selected, or if hidefolders is disabled
            $list->filterByParentFolder($this->getRootFolder());
        } else {
            // If we have the top level folder selected and hidefolders is enabled
            $list->enableSubFolderSearch();
        }

        if ($this->hideFolders) {
            $list->getQueryObject()->andWhere('nt.treeNodeTypeHandle <> "file_folder"');
        }

        return $list;
    }

    /**
     * @param FolderItemList $list
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    protected function enableSubFolderSearch(FolderItemList $list)
    {
        $version = $this->app->make('config')->get('concrete.version');
        if (version_compare($version, '8.3.0a1', '>=')) {
            // This is only possible in 8.3.0 or greater.
            $list->enableSubFolderSearch();
        }
    }

    /**
     * @param bool $realRoot
     *
     * @return FileFolder
     */
    private function getRootFolder($realRoot = false)
    {
        if (!$realRoot && $this->folderID) {
            return FileFolder::getByID($this->folderID) ?? new FileFolder();
        }

        $filesystem = $this->app->make(Filesystem::class);

        return $filesystem->getRootFolder();
    }

    /**
     * @param string $key
     * @param FileFolder $folder
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
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
                /** @var UrlImmutable $action */
                $action = $this->getActionURL('navigate', $folder->getTreeNodeID());

                return sprintf('<a href="%s">%s</a>', h($action), $folder->getTreeNodeDisplayName());
            case 'filename':
                return $folder->getTreeNodeDisplayName();
            case 'date':
                return $this->app->make('date')->formatDate($folder->getDateCreated(), false);
            case 'extension':
            case 'size':
            case 'description':
            case 'details':
            case 'edit_properties':
            case 'tags':
            default:
                return '';
        }
    }

    /**
     * @param FileFolder[] $breadcrumbs
     *
     * @return array<int,array<string,string|mixed>>
     */
    private function formatBreadcrumbs($breadcrumbs)
    {
        $return = [];
        foreach ($breadcrumbs as $crumb) {
            if ($crumb->getTreeNodeID() == $this->getRootFolder()->getTreeNodeID()) {
                /** @phpstan-ignore-next-line */
                $action = $this->getBlockObject()->getBlockCollectionObject()->getCollectionLink();
            } else {
                $action = $this->getActionURL('navigate', $crumb->getTreeNodeID());
            }

            $return[] = [
                'url' => $action,
                'name' => $crumb->getTreeNodeDisplayName(),
            ];
        }

        return $return;
    }

    /**
     * @param FolderItemList $list
     * @param string $keywords
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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

        /** @var \Concrete\Core\Entity\Attribute\Key\FileKey[] $keys */
        /** @phpstan-ignore-next-line */
        $keys = FileKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $query);
        }

        $query->andWhere($query->expr()->orX()->addMultiple($expressions));
        $query->setParameter('keywords', '%' . $keywords . '%');

        return $list;
    }
}
