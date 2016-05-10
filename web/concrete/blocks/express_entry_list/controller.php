<?
namespace Concrete\Block\ExpressEntryList;

use Concrete\Controller\Element\Search\CustomizeResults;
use \Concrete\Core\Block\BlockController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{

    protected $btInterfaceWidth = "640";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btExpressEntryList';
    protected $entityAttributes = array();

    public function on_start()
    {
        parent::on_start();
        $this->entityManager = \Core::make('database/orm')->entityManager();
    }

    public function getBlockTypeDescription()
    {
        return t("Add a searchable Express entry list to a page.");
    }

    public function getBlockTypeName()
    {
        return t("Express Entry List");
    }

    public function add()
    {
        $this->loadData();
    }

    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $provider = \Core::make('Concrete\Core\Express\Search\SearchProvider', array($entity));
                $element = new CustomizeResults($provider);
                $this->set('customizeElement', $element);
            }
        }
    }

    public function view()
    {

    }

    public function action_load_entity_data()
    {
        $exEntityID = $this->request->request->get('exEntityID');
        if ($exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $exEntityID);
            if (is_object($entity)) {
                $provider = \Core::make('Concrete\Core\Express\Search\SearchProvider', array($entity));
                $element = new CustomizeResults($provider);
                $r = new \stdClass;
                ob_start();
                $element->getViewObject()->render();
                $r->customize = ob_get_contents();
                ob_end_clean();

                $attributes = $entity->getAttributeKeyCategory()->getList();
                $select = array();
                foreach($attributes as $ak) {
                    $o = new \stdClass;
                    $o->akID = $ak->getAttributeKeyID();
                    $o->akName = $ak->getAttributeKeyDisplayName();
                    $select[] = $o;
                }
                $r->attributes = $select;
                return new JsonResponse($r);
            }
        }

        \Core::make('app')->shutdown();
    }


    public function loadData()
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entityObjects = $r->findAll();
        $entities = array('' => t("** Choose Entity"));
        foreach($entityObjects as $entity) {
            $entities[$entity->getID()] = $entity->getName();
        }
        $this->set('entities', $entities);
    }

    /*
    public function on_start()
    {
        $this->fileAttributes = FileKey::getList();
    }

    public function loadData()
    {
        $fsl = new SetList();
        $fsl->filterByType(Set::TYPE_PUBLIC);
        $r = $fsl->get();
        $sets = array();
        foreach ($r as $fs) {
            $fsp = new \Permissions($fs);
            if ($fsp->canSearchFiles()) {
                $sets[] = $fs;
            }
        }
        $this->set('fileSets', $sets);

        $searchProperties = array(
            'date' => t('Date Posted'),
            'type' => t('File Type'),
            'extension' => t('File Extension')
        );
        foreach($this->fileAttributes as $ak) {
            $searchProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('searchProperties', $searchProperties);

        $orderByOptions = array(
            'title' => t('Title'),
            'set' => t('Set Order'),
            'date' => t('Date Posted'),
            'filename' => t('Filename')
        );
        foreach($this->fileAttributes as $ak) {
            $orderByOptions['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('orderByOptions', $orderByOptions);
        $viewProperties = array(
            'thumbnail' => t('Thumbnail'),
            'filename' => t('Filename'),
            'tags' => t('Tags'),
            'date' => t('Date Posted'),
            'extension' => t('Extension'),
            'size' => t('Size'),
            'description' => t('Description'),
        );
        foreach($this->fileAttributes as $ak) {
            $viewProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('viewProperties', $viewProperties);

        $expandableProperties = array(
            'image' => t('Image'),
            'description' => t('Description'),
            'tags' => t('Tags'),
            'filename' => t('Filename'),
            'date' => t('Date Posted'),
            'extension' => t('Extension'),
            'size' => t('Size'),
        );
        foreach($this->fileAttributes as $ak) {
            $expandableProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('expandableProperties', $expandableProperties);

    }

    public function edit()
    {
        $this->loadData();
        $this->set('selectedSets', (array) json_decode($this->setIds));
        $this->set('searchPropertiesSelected', (array) json_decode($this->searchProperties));
        $viewPropertiesDoNotDisplay = array();
        $viewPropertiesDisplay = array();
        $viewPropertiesDisplaySortable = array();
        $viewProperties = (array) json_decode($this->viewProperties);
        foreach($viewProperties as $key => $type) {
            switch($type) {
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

        $properties = array(
            'title' => 'fv.fvTitle',
            'filename' => 'fv.fvFilename',
            'tags' => 'fv.fvTags',
            'date' => 'fv.fvDateAdded',
            'extension' => 'fv.fvExtension',
            'size' => 'fv.fvSize',
            'description' => 'fv.fvDescription',
        );

        foreach($properties as $block => $filelist) {
            if ($retrieve == 'filelist' && $key == $block) {
                return $filelist;
            } else if ($retrieve == 'block' && $key == $filelist) {
                return $block;
            }
        }
    }
    public function add()
    {
        $this->loadData();
        $this->set('setMode', 'any');
        $this->set('enableSearch', 0);
        $this->set('selectedSets', array());
        $this->set('searchPropertiesSelected', array());
        $this->set('expandablePropertiesSelected', array());
        $viewPropertiesDoNotDisplay = array();
        foreach($this->get('viewProperties') as $key => $name) {
            if (!in_array($key, array('filename','size','date','thumbnail'))) {
                $viewPropertiesDoNotDisplay[] = $key;
            }
        }
        $this->set('viewPropertiesDoNotDisplay', $viewPropertiesDoNotDisplay);
        $this->set('viewPropertiesDisplay', array('thumbnail'));
        $this->set('viewPropertiesDisplaySortable', array('filename','size','date'));
        $this->set('displayLimit', 20);
        $this->set('downloadFileMethod', 'browser');
        $this->set('heightMode', 'auto');
    }

    protected function setupFileSetFilter($list)
    {
        $db = \Database::get();
        $sets = array();
        $ids = (array) json_decode($this->setIds);
        foreach($ids as $fsID) {
            $fs = \FileSet::getByID($fsID);
            if (is_object($fs)) {
                $sets[] = $fs;
            }
        }
        if (count($sets)) {
            if ($this->setMode == 'all') {
                foreach($sets as $fs) {
                    $list->filterBySet($fs);
                }
            } else {
                $list->getQueryObject()->select('distinct f.fID');
                $list->getQueryObject()->innerJoin('f', 'FileSetFiles', 'fsf', 'f.fID = fsf.fID');
                $list->getQueryObject()->andWhere(
                    $list->getQueryObject()->expr()->in('fsf.fsID', array_map(function($fs) use ($db) {
                        return $db->quote($fs->getFileSetID());
                    }, $sets))
                );
            }
        }

        return $list;
    }

    protected function getTableColumns($results)
    {
        $viewProperties = (array) json_decode($this->viewProperties);
        $return = array();
        if (array_key_exists('thumbnail', $viewProperties) && $viewProperties['thumbnail'] > -1) {
            $return[] = 'thumbnail';
        }
        $return[] = 'title';
        foreach($viewProperties as $key => $type) {
            if ($key == 'thumbnail') {
                continue;
            }

            if ($type > 0) {
                $return[] = $key;
            }
        }

        if ($this->allowInPageFileManagement) {
            foreach($results as $file) {
                $fp = new \Permissions($file);
                if ($fp->canEditFileProperties()) {
                    $return[] = 'edit_properties';
                    break;
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
        $return = array();
        foreach($expandableProperties as $key) {
            $return[] = $key;
        }
        return $return;
    }

    protected function getTableSearchProperties()
    {
        $searchProperties = (array) json_decode($this->searchProperties);
        $return = array();
        foreach($searchProperties as $key) {
            $return[] = $key;
        }
        return $return;
    }

    public function getColumnTitle($key)
    {
        switch($key) {
            case 'title':
                return t('Title');
            case 'image':
                return t('Detail Image');
            case 'edit_properties':
            case 'details':
                return "";
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
        $query = $url->getQuery();
        $query['sort'] = $key;
        $query['dir'] = $order;
        $url = $url->setQuery($query);
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
        switch($key) {
            case 'type':
                $form = \Core::make('helper/form');
                $t1 = Type::getTypeList();
                $types = array('' => t('** File type'));
                foreach ($t1 as $value) {
                    $types[$value] = Type::getGenericTypeText($value);
                }
                return $form->select('type', $types, array('style' => 'width: 120px'));
            case 'extension':
                $form = \Core::make('helper/form');
                $ext1 = Type::getUsedExtensionList();
                $extensions = array('' => t('** File Extension'));
                foreach ($ext1 as $value) {
                    $extensions[$value] = $value;
                }
                return $form->select('extension', $extensions, array('style' => 'width: 120px'));
            case 'date':
                $wdt = \Core::make('helper/form/date_time');
                print $wdt->translate($_REQUEST['date_from']);
                return $wdt->datetime('date_from', $wdt->translate('date_from', $_REQUEST), true) . t('to') . $wdt->datetime('date_to', $wdt->translate('date_to', $_REQUEST), true);
            default:
                $akID = substr($key, 3);
                $ak = FileKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->render('search', null, true);
                }
                break;
        }
    }

    protected function setupAdvancedSearch($list)
    {
        $searchProperties = (array) json_decode($this->searchProperties);
        foreach($searchProperties as $column) {
            switch($column) {
                case 'type':
                    if ($_REQUEST['type']) {
                        $list->filterByType($_REQUEST['type']);
                    }
                    break;
                case 'extension':
                    if ($_REQUEST['extension']) {
                        $list->filterByExtension($_REQUEST['extension']);
                    }
                    break;
                case 'date':
                    $wdt = \Core::make('helper/form/date_time');
                    $dateFrom = $wdt->translate('date_from', $_REQUEST);
                    if ($dateFrom) {
                        $list->filterByDateAdded($dateFrom, '>=');
                    }
                    $dateTo = $wdt->translate('date_to', $_REQUEST);
                    if ($dateTo) {
                        if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                            $dateTo = $m[1] . ':59';
                        }
                        $list->filterByDateAdded($dateTo, '<=');
                    }
                    break;
                default:
                    $akID = substr($column, 3);
                    $ak = FileKey::getByID($akID);
                    if (is_object($ak)) {
                        $type = $ak->getAttributeType();
                        $cnt = $type->getController();
                        $cnt->setRequestArray($_REQUEST);
                        $cnt->setAttributeKey($ak);
                        $cnt->searchForm($list);
                    }
                    break;
            }
        }
        return $list;
    }

    public function getColumnValue($key, File $file)
    {
        switch($key) {
            case 'thumbnail':

                $im = Core::make('helper/image');
                if ($file->getTypeObject()->getGenericType() == Type::T_IMAGE && $this->maxThumbWidth && $this->maxThumbHeight) {

                    $thumb = $im->getThumbnail(
                        $file,
                        $this->maxThumbWidth,
                        $this->maxThumbHeight
                    );
                    $thumbnail = new \HtmlObject\Image();
                    $thumbnail->src($thumb->src)->width($thumb->width)->height($thumb->height);
                } else {
                    $thumbnail  = $file->getTypeObject()->getThumbnail();
                }

                return $thumbnail;
                break;
            case 'image':
                if ($file->getTypeObject()->getGenericType() == Type::T_IMAGE) {
                    return sprintf('<img src="%s" class="img-responsive" />', $file->getRelativePath());
                }
                break;
            case 'edit_properties':
                $fp = new \Permissions($file);
                if ($fp->canEditFileProperties()) {
                    return sprintf('<a href="#" data-document-library-edit-properties="%s" class="ccm-block-document-library-icon"><i class="fa fa-pencil"></i></a>', $file->getFileID());
                }
                break;
            case 'details':
                return sprintf('<a href="#" data-document-library-show-details="%s" class="ccm-block-document-library-details">%s</a>', $file->getFileID(), t('Details'));
            case 'title':
                if ($this->downloadFileMethod == 'force') {
                    return sprintf('<a href="%s">%s</a>', $file->getForceDownloadURL(), $file->getTitle());
                } else {
                    return sprintf('<a href="%s">%s</a>', $file->getDownloadURL(), $file->getTitle());
                }
                break;
            case 'filename':
                return $file->getFileName();
                break;
            case 'description':
                return $file->getDescription();
                break;
            case 'tags':
                return $file->getTags();
                break;
            case 'date':
                return Core::make("date")->formatDate($file->getDateAdded(), false);
                break;
            case 'extension':
                return $file->getExtension();
                break;
            case 'size':
                return $file->getSize();
                break;
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
    }

    public function action_upload($bID = false)
    {
        $files = array();
        if ($this->bID == $bID) {
            $fp = \FilePermissions::getGlobal();
            $cf = \Loader::helper('file');
            if (\Core::make('token')->validate()) {
                if (isset($_FILES['file']) && (is_uploaded_file($_FILES['file']['tmp_name']))) {
                    if (!$fp->canAddFileType($cf->getExtension($_FILES['file']['name']))) {
                        throw new \Exception(FileImporter::getErrorMessage(FileImporter::E_FILE_INVALID_EXTENSION));
                    } else {
                        $ih = new Importer();
                        $response = $ih->import($_FILES['file']['tmp_name'], $_FILES['file']['name']);
                        if (!($response instanceof \Concrete\Core\File\Version)) {
                            throw new \Exception(Importer::getErrorMessage($response));
                        } else {
                            $file = $response->getFile();
                            if ($this->addFilesToSetID) {
                                $fs = \FileSet::getByID($this->addFilesToSetID);
                                if (is_object($fs)) {
                                    $fs->addFileToSet($file);
                                }
                            }
                            $files[] = $file;
                        }
                    }
                }
            }
        }

        if (!$this->allowInPageFileManagement) {
            // We're going to set a message to display the next time the page loads.
            Core::make('session')->getFlashBag()->add('document_library_success_message', t2('File added successfully', 'Files added successfully', count($files)));
        }


        $r = new \Concrete\Core\File\EditResponse();
        $r->setFiles($files);
        $r->outputJSON();

    }

    public function view()
    {
        $this->loadData();
        $list = new FileList();
        $list = $this->setupFileSetFilter($list);

        $order = $this->displayOrderDesc ? 'desc' : 'asc';
        $orderBy = $this->getSortColumnKey($this->orderBy, 'filelist');
        if ($orderBy) {
            $list->sortBy($orderBy, $order);
        }

        if (isset($_REQUEST['keywords'])) {
            $list->filterByKeywords($_REQUEST['keywords']);
        }

        if (isset($_REQUEST['sort'])) {
            $getSort = $this->getSortColumnKey($_REQUEST['sort']);
            if ($getSort) {
                if (isset($_REQUEST['dir'])) {
                    $list->sortBy($getSort, $_REQUEST['dir']);
                } else {
                    $list->sortBy($getSort);
                }
            }
        }

        if ($this->tags) {
            $list->filterByTags($this->tags);
        }

        if($this->onlyCurrentUser) {
            $u = new \User();
            if($u->isRegistered()) {
                $uID = $u->getUserID();
                $list->filterByAuthorUserID($uID);
            }
        }
        $list = $this->setupAdvancedSearch($list);
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

        $this->requireAsset('css', 'font-awesome');
        if ($this->enableSearch) {
            $this->requireAsset('jquery/ui');
        }
        $this->set('canAddFiles', false);
        $fp = \FilePermissions::getGlobal();
        if ($this->allowInPageFileManagement) {
            $this->requireAsset('core/file-manager');
        }

        if ($this->allowFileUploading && $fp->canAddFile()) {
            $this->requireAsset('core/file-manager');
            $this->set('canAddFiles', true);
        }

        $bag = \Core::make('session')->getFlashBag();
        if ($bag->has('document_library_success_message')) {
            $success = $bag->get('document_library_success_message');
            $success = $success[0];
            $this->set('success', $success);
        }
    }

    public function save($args)
    {
        $data = array();
        $fsID = array();
        if (isset($args['fsID']) && is_array($args['fsID'])) {
            $fsID = $args['fsID'];
        }
        $viewProperties = array();
        if (isset($args['viewProperties']) && is_array($args['viewProperties'])) {
            $viewProperties = $args['viewProperties'];
        }
        $searchProperties = array();
        if (isset($args['searchProperties']) && is_array($args['searchProperties'])) {
            $searchProperties = $args['searchProperties'];
        }
        $expandableProperties = array();
        if (isset($args['expandableProperties']) && is_array($args['expandableProperties'])) {
            $expandableProperties = $args['expandableProperties'];
        }
        $data['viewProperties'] = json_encode($viewProperties);
        $data['searchProperties'] = json_encode($searchProperties);
        $data['expandableProperties'] = json_encode($expandableProperties);
        $data['setIds'] = json_encode($fsID);
        $data['setMode'] = $args['setMode'] == 'all' ? 'all' : 'any';
        $data['onlyCurrentUser'] = $args['onlyCurrentUser'] == '1' ? 1 : 0;
        $data['allowInPageFileManagement'] = $args['allowInPageFileManagement'] == '1' ? 1 : 0;
        $data['allowFileUploading'] = $args['allowFileUploading'] == '1' ? 1 : 0;
        $data['tags'] = $args['tags'];
        $data['orderBy'] = $args['orderBy'];
        $data['displayLimit'] = $args['displayLimit'];
        $data['displayOrderDesc'] = $args['displayOrderDesc'] == '1' ? 1 : 0;
        $data['maxThumbWidth'] = intval($args['maxThumbWidth']);
        $data['maxThumbHeight'] = intval($args['maxThumbHeight']);
        $data['enableSearch'] = $args['enableSearch'] == '1' ? 1 : 0;
        $data['heightMode'] = $args['heightMode'] == 'fixed' ? 'fixed' : 'auto';
        $data['downloadFileMethod'] = $args['downloadFileMethod'] == 'force' ? 'force' : 'browser';
        $data['fixedHeightSize'] = intval($args['fixedHeightSize']);
        $data['headerBackgroundColor'] = $args['headerBackgroundColor'];
        $data['addFilesToSetID'] = 0;
        if (isset($args['addFilesToSetID']) && $args['addFilesToSetID'] > 0) {
            $fs = \FileSet::getByID($args['addFilesToSetID']);
            if (is_object($fs)) {
                $fsp = new \Permissions($fs);
                if ($fsp->canAddFiles() && $fsp->canSearchFiles()) {
                    $data['addFilesToSetID'] = $fs->getFileSetID();
                }
            }
        }
        $data['addFilesToSetID'] = intval($args['addFilesToSetID']);
        $data['headerBackgroundColorActiveSort'] = $args['headerBackgroundColorActiveSort'];
        $data['headerTextColor'] = $args['headerTextColor'];
        $data['tableName'] = $args['tableName'];
        $data['tableDescription'] = $args['tableDescription'];
        $data['tableStriped'] = $args['tableStriped'] == '1' ? 1 : 0;
        if ($data['tableStriped']) {
            $data['rowBackgroundColorAlternate'] = $args['rowBackgroundColorAlternate'];
        } else {
            $data['rowBackgroundColorAlternate'] = '';
        }
        parent::save($data);
    }

    */


}
