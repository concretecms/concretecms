<?php

namespace Concrete\Block\RelatedPages;
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Type;
use Core;
use Loader;

class Controller extends BlockController
{

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 300;
    protected $btExportPageColumns = array('cParentID');
    protected $btExportPageTypeColumns = array('ptID');
    protected $btTable = 'btRelatedPages';

    public function getBlockTypeDescription()
    {
        return t("Displays a list of pages related to this page. Uses the topic attribute for relation.");
    }

    public function getBlockTypeName()
    {
        return t("Related Pages");
    }

    public function add()
    {
        $this->edit();
        $this->set('maxResults', 3);
        $this->set('title', t('Related Pages'));
    }

    public function edit()
    {
        $keys = CollectionKey::getList();
        foreach($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'topics') {
                $attributeKeys[] = $ak;
            }
        }
        $types = Type::getList();
        $this->set('pagetypes', $types);
        $this->set('attributeKeys', $attributeKeys);
    }

    public function view()
    {
        $ak = CollectionKey::getByHandle($this->topicAttributeKeyHandle);
        $pages = array();
        $c = \Page::getCurrentPage();
        if (is_object($ak)) {
            $topics = $c->getAttribute($ak->getAttributeKeyHandle());
            if (count($topics) > 0 && is_array($topics)) {
                $pl = new PageList();
                $pl->setItemsPerPage($this->maxResults);
                if ($this->ptID) {
                    $pl->filterByPageTypeID($this->ptID);
                }
                if ($this->cParentID) {
                    $pl->filterByParentID($this->cParentID);
                }
                $pl->filter('p.cID', $c->getCollectionID(), '<>');
                $topic = $topics[array_rand($topics)];
                $pl->filterByTopic($topic);
                $pl->sortBy('rand()');
                $pages = $pl->getPagination()->getCurrentPageResults();
            }
        }
        $this->set('pages', $pages);
    }

    public function save($data)
    {
        $data['maxResults'] = intval($data['maxResults']);
        parent::save($data);
    }
}