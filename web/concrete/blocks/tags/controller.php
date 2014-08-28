<?php
namespace Concrete\Block\Tags;

use Concrete\Attribute\Select\Option;
use Loader;
use \Concrete\Core\Block\BlockController;
use CollectionAttributeKey;
use Page;

class Controller extends BlockController
{

    protected $btTable = 'btTags';
    protected $btInterfaceWidth = "450";
    protected $btInterfaceHeight = "439";

    protected $btExportPageColumns = array('targetCID');

    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btWrapperClass = 'ccm-ui';

    public $attributeHandle = 'tags';
    public $displayMode = 'page';
    public $cloudCount = 10;
    public $helpers = array('navigation');

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription()
    {
        return t("List pages based on type, area.");
    }

    public function getBlockTypeName()
    {
        return t("Tags");
    }

    public function add()
    {
        $ak = $this->loadAttribute();
        $this->set('ak', $ak);
        if ($this->isValidStack(Page::getCurrentPage())) {
            $this->set('inStackDashboardPage', true);
        }
        $this->set('displayMode', 'page');
    }

    protected function loadAttribute()
    {
        $ak = CollectionAttributeKey::getByHandle($this->attributeHandle);
        return $ak;
    }

    public function edit()
    {
        $ak = $this->loadAttribute();
        $this->set('ak', $ak);
        if ($this->isValidStack(Page::getCurrentPage())) {
            $this->set('inStackDashboardPage', true);
        }
    }

    public function view()
    {
        $ak = $this->loadAttribute();
        if ($this->displayMode == "cloud") {
            $type = $ak->getAttributeType();
            $controller = $type->getController();
            $controller->setAttributeKey($ak);
            $items = $controller->getOptions();
            $options = new \Concrete\Attribute\Select\OptionList();
            if ($this->cloudCount > 0 && $items instanceof \Concrete\Attribute\Select\OptionList && $items->count()) {
                $i = 1;
                foreach ($items as $item) {
                    $options->add($item);
                    if ($i >= $this->cloudCount) {
                        break;
                    }
                    $i++;
                }
            } else {
                $options = $items;
            }
        } else {
            $c = Page::getCurrentPage();
            $av = $c->getAttributeValueObject($ak);
            $controller = $ak->getController();
            $options = $c->getAttribute($ak->getAttributeKeyHandle());
        }

        if ($this->targetCID > 0) {
            $target = Page::getByID($this->targetCID);
            $this->set('target', $target);
        }

        // grab selected tag, if we're linking to a page with a tag block on it.
        if (is_array($_REQUEST['akID'])) {
            $res = $_REQUEST['akID'][$ak->getAttributeKeyID()]['atSelectOptionID'][0];
            if (is_numeric($res) && $res > 0) {
                $selectedOptionID = $res;
            }
        }
        $this->set('selectedOptionID', $selectedOptionID);
        $this->set('options', $options);
        $this->set('akc', $controller);
        $this->set('ak', $ak);
    }

    public function save($args)
    {
        $ak = $this->loadAttribute();
        if ($_REQUEST['cID']) {
            $c = Page::getByID($_REQUEST['cID'], 'RECENT');
            // We cannot save the attribute in the Stack Dashboard page
            // as there is nothing to attach it to
            if (!$this->isValidStack($c)) {
                $nvc = $c->getVersionToModify();
                $ak->saveAttributeForm($nvc);
                $nvc->refreshCache();
            }
        }
        $args['cloudCount'] = (is_numeric($args['cloudCount']) ? $args['cloudCount'] : 0);
        $args['targetCID'] = (is_numeric($args['targetCID']) ? $args['targetCID'] : 0);
        parent::save($args);
    }

    public function getTagLink(Option $option = null)
    {
        $target = $this->get('target');
        if (!is_object($target)) {
            $target = \Page::getCurrentPage();
        }
        if ($option) {
            return \URL::page($target, 'tag', strtolower($option->getSelectAttributeOptionDisplayValue()));
        } else {
            return \URL::page($target);
        }
    }

    protected function isValidStack($stack)
    {
        return $stack->getCollectionParentID() == Page::getByPath(STACKS_PAGE_PATH)->getCollectionID();
    }
}