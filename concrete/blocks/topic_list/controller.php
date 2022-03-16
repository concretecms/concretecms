<?php

namespace Concrete\Block\TopicList;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Type\Topic as TopicTree;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    public $helpers = ['form', 'form/page_selector'];

    /**
     * @var int|null
     */
    public $topicTreeID;

    /**
     * @var string|null
     */
    public $mode;

    /**
     * @var string|null
     */
    public $topicAttributeKeyHandle;

    /**
     * @var int|null
     */
    public $cParentID;

    /**
     * @var string|null
     */
    public $title;

    protected $btInterfaceWidth = 400;

    protected $btInterfaceHeight = 400;

    protected $btTable = 'btTopicList';

    /**
     * @var string[]
     */
    protected $btExportPageColumns = ['cParentID'];

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
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        if ($this->mode == 'P') {
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
     * @param int $treeID
     *
     * @return string|null
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
            $parent = Page::getByID($this->cParentID);
            $path = '{ccm:export:page:' . $parent->getCollectionPath() . '}';
        }
        $data->addChild('cParentID', $path);
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
        $args['topicTreeID'] = $tree->getTreeID();
        $args['cParentID'] = 0;
        $args['title'] = (string) $blockNode->data->title;
        $args['mode'] = (string) $blockNode->data->mode;
        $args['titleFormat'] = (string) $blockNode->data->titleFormat;
        if (!$args['mode']) {
            $args['mode'] = 'S';
        }
        if (!$args['titleFormat']) {
            $args['titleFormat'] = 'h5';
        }
        $args['topicAttributeKeyHandle'] = (string) $blockNode->data->topicAttributeKeyHandle;
        if ($page) {
            if (preg_match('/\{ccm:export:page:(.*?)\}/i', $page, $matches)) {
                $c = Page::getByPath($matches[1]);
                $args['externalTarget'] = 1;
                $args['cParentID'] = $c->getCollectionID();
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
}
