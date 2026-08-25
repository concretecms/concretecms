<?php

namespace Concrete\Block\TopicList;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Type\Topic as TopicTree;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * Value of the mode column: the topics are the ones of a topic tree.
     *
     * @var string
     */
    protected const MODE_SEARCH = 'S';

    /**
     * Value of the mode column: the topics are the ones assigned to the page holding the block.
     *
     * @var string
     */
    protected const MODE_PAGE = 'P';

    /**
     * @var string|null
     */
    public $mode;

    /**
     * @var string|null
     */
    public $topicAttributeKeyHandle;

    /**
     * @var int|string|null
     */
    public $topicTreeID;

    /**
     * @var int|string|null
     */
    public $cParentID;

    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $titleFormat;

    public $helpers = ['form', 'form/page_selector'];

    protected $btInterfaceWidth = 400;

    protected $btInterfaceHeight = 400;

    protected $btTable = 'btTopicList';

    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputOnEditMode = false;
    protected $btCacheBlockOutputLifetime = 0;

    /**
     * @var string[]
     */
    protected $btExportPageColumns = ['cParentID'];

    /**
     * @var bool
     */
    protected $btCacheSettingsInitialized = false;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Displays a list of your site's topics, allowing you to click on them to filter a page list.");
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Topic List');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function add()
    {
        $this->edit();
        $this->set('title', t('Topics'));
        $this->set('titleFormat', 'h5');
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::TAXONOMY,
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $defaultTree = TopicTree::getDefault();
        $tree = TopicTree::getByID($this->app->make('helper/security')->sanitizeInt($this->topicTreeID));
        if (!$tree) {
            $tree = $defaultTree;
        }
        $trees = TopicTree::getList();

        $categoryService = $this->app->make('\Concrete\Core\Attribute\Category\PageCategory');
        /** @var \Concrete\Core\Entity\Attribute\Key\PageKey[] */
        $keys = $categoryService->getList();
        $attributeKeys = [];
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'topics') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('attributeKeys', $attributeKeys);
        $this->set('tree', $tree);
        $this->set('trees', $trees);
        $this->set('modes', $this->getAvailableModes());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        if ($this->mode == self::MODE_PAGE) {
            $page = Page::getCurrentPage();
            $topics = $page->getAttribute($this->topicAttributeKeyHandle);
            if (is_array($topics)) {
                $this->set('topics', $topics);
            }
        } else {
            $tree = TopicTree::getByID($this->app->make('helper/security')->sanitizeInt($this->topicTreeID));
            $this->set('tree', $tree);
        }
    }

    /**
     * @param int|false $treeNodeID
     * @param string|false $topic
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function action_topic($treeNodeID = false, $topic = false)
    {
        $this->set('selectedTopicID', (int) $treeNodeID);
        $this->view();
    }

    /**
     * @param \Concrete\Core\Tree\Node\Node|null $topic
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \League\Url\UrlInterface
     */
    public function getTopicLink(?\Concrete\Core\Tree\Node\Node $topic = null)
    {
        if ($this->cParentID) {
            $c = Page::getByID($this->cParentID);
        } else {
            $c = Page::getCurrentPage();
        }
        if ($topic) {
            $nodeName = $topic->getTreeNodeName();
            $nodeName = strtolower($nodeName); // convert to lowercase
            $nodeName = preg_replace('/[[:space:]]+/', '-', $nodeName);
            $nodeName = $this->app->make('helper/text')->encodePath($nodeName); // urlencode

            return Url::to($c, 'topic', $topic->getTreeNodeID(), $nodeName);
        }

            return Url::to($c);
    }

    /**
     * @deprecated
     */
    public static function replaceTreeWithPlaceHolder($treeID)
    {
        if ($treeID > 0) {
            $tree = Tree::getByID($treeID);
            if (is_object($tree)) {
                return '{ccm:export:tree:' . $tree->getTreeName() . '}';
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        $schemaFactory = $this->app->make(ApiValueSchemaFactory::class);

        return [
            'type' => 'object',
            'properties' => [
                'mode' => [
                    'type' => 'string',
                    'enum' => array_keys($this->getAvailableModes()),
                    'default' => self::MODE_SEARCH,
                    'description' => 'Which topics are listed: "' . self::MODE_SEARCH . '" the ones of the topicTreeID tree, "' . self::MODE_PAGE . '" the ones assigned to the page holding the block.',
                ],
                'topicTreeID' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The ID of the topic tree holding the topics to be listed (0 for none): it\'s used only when mode is "' . self::MODE_SEARCH . '".',
                ],
                'topicAttributeKeyHandle' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The handle of the topics attribute of the page holding the topics to be listed (it\'s used only when mode is "' . self::MODE_PAGE . '").',
                ],
                'cParentID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE, [
                    'type' => ['string', 'integer'],
                    'description' => 'The page the topics link to (0 for the page holding the block).',
                ]),
                'title' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text displayed above the topics.',
                ],
                'titleFormat' => [
                    'type' => 'string',
                    'enum' => array_keys(BlockController::$btTitleFormats),
                    'default' => 'h5',
                    'description' => 'The HTML element wrapping the text displayed above the topics.',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        if ($this->bID) {
            // the save() method resets the settings that it doesn't receive: let's keep the current ones
            $value += $this->serializeValueForApi();
        }
        $args = [];
        foreach (['mode', 'title', 'titleFormat', 'topicAttributeKeyHandle'] as $key) {
            $args[$key] = isset($value[$key]) ? (string) $value[$key] : '';
        }
        $tree = empty($value['topicTreeID']) ? null : TopicTree::getByID((int) $value['topicTreeID']);
        $args['topicTreeID'] = $tree === null ? 0 : $tree->getTreeID();
        $cParentID = (int) $this->importReferenceValue((string) ($value['cParentID'] ?? ''), ExportDeclarations::REFERENCE_PAGE);
        // the save() method below keeps the page only when the checkbox of the form is checked
        $args['cParentID'] = $cParentID;
        $args['externalTarget'] = $cParentID === 0 ? 0 : 1;

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below writes the settings as the children of the <data> element, which is not
        // the list of records of a database table that the API knows how to read
        return [
            'mode' => (string) $this->mode,
            'topicTreeID' => (int) $this->topicTreeID,
            'topicAttributeKeyHandle' => (string) $this->topicAttributeKeyHandle,
            'cParentID' => (string) ($this->cParentID ? ContentExporter::replacePageWithPlaceHolder($this->cParentID) : '0'),
            'title' => (string) $this->title,
            'titleFormat' => (string) $this->titleFormat,
        ];
    }

    /**
     * Get the ways the topics to be listed are chosen.
     *
     * @return array<string,string> the keys are the values of the mode column, the values are their names
     */
    protected function getAvailableModes(): array
    {
        return [
            self::MODE_SEARCH => t('Search – Display a list of all topics for use on a search sidebar.'),
            self::MODE_PAGE => t('Page – Display a list of topics for the current page.'),
        ];
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $tree = Tree::getByID($this->topicTreeID);
        $data = $blockNode->addChild('data');
        $data->addChild('mode', $this->mode);
        $data->addChild('title', $this->title);
        $data->addChild('topicAttributeKeyHandle', $this->topicAttributeKeyHandle);
        if (is_object($tree)) {
            $data->addChild('tree', $tree->getTreeName());
        }
        $path = null;
        if ($this->cParentID) {
            $path = ContentExporter::replacePageWithPlaceHolder($this->cParentID);
        }
        $data->addChild('cParentID', $path);
        $data->addChild('titleFormat', $this->titleFormat);
    }

    /**
     * @param \SimpleXMLElement $blockNode The block node to import
     * @param Page|mixed $page This is ignored
     *
     * @return array<string, mixed>
     */
    public function getImportData($blockNode, $page)
    {
        $args = [];
        $treeName = (string) $blockNode->data->tree;
        $page = (string) $blockNode->data->cParentID;
        $tree = TopicTree::getByName($treeName);
        $args['topicTreeID'] = empty($tree) ? null : $tree->getTreeID();
        $args['cParentID'] = 0;
        $args['title'] = (string) $blockNode->data->title;
        $args['mode'] = (string) $blockNode->data->mode;
        $args['titleFormat'] = (string) $blockNode->data->titleFormat;
        if (!$args['mode']) {
            $args['mode'] = self::MODE_SEARCH;
        }
        if (!$args['titleFormat']) {
            $args['titleFormat'] = 'h5';
        }
        $args['topicAttributeKeyHandle'] = (string) $blockNode->data->topicAttributeKeyHandle;
        if ($page) {
            $pageImporterRoutine = new PageRoutine();
            $matchedItems = $pageImporterRoutine->match($page);
            if (count($matchedItems) === 1) {
                $c = $matchedItems[0]->getContentObject();
                if ($c !== null) {
                    $args['externalTarget'] = 1;
                    $args['cParentID'] = $c->getCollectionID();
                }
            }
        }

        return $args;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function save($data)
    {
        $data += [
            'externalTarget' => 0,
        ];
        $externalTarget = (int) ($data['externalTarget']);
        if ($externalTarget === 0) {
            $data['cParentID'] = 0;
        } else {
            $data['cParentID'] = (int) ($data['cParentID']);
        }

        parent::save($data);
    }

    /**
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutput;
    }

    /**
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputOnPost;
    }

    /**
     * @return bool
     */
    public function cacheBlockOutputForRegisteredUsers()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputForRegisteredUsers;
    }

    /**
     * @return void
     */
    protected function setupCacheSettings(): void
    {
        $page = $this->getCollectionObject();
        if ($this->btCacheSettingsInitialized || !is_object($page) || $page->isEditMode()) {
            return;
        }

        $this->btCacheSettingsInitialized = true;

        $btCacheBlockOutput = false;
        $btCacheBlockOutputOnPost = false;
        $btCacheBlockOutputForRegisteredUsers = false;

        // If post result page is another page, we don't need to care about "active" topic
        if ($this->cParentID && $this->cParentID !== $page->getCollectionID()) {
            $btCacheBlockOutput = true;
            $btCacheBlockOutputOnPost = true;
            $btCacheBlockOutputForRegisteredUsers = true;
        }

        $this->btCacheBlockOutput = $btCacheBlockOutput;
        $this->btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost;
        $this->btCacheBlockOutputForRegisteredUsers = $btCacheBlockOutputForRegisteredUsers;
    }
}
