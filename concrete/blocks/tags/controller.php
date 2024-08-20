<?php
namespace Concrete\Block\Tags;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Block\BlockController;
use CollectionAttributeKey;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Page;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $title;

    /**
     * @var int|string|null
     */
    public $targetCID;

    /**
     * @var string|null
     */
    public $displayMode = 'page';

    /**
     * @var int|string|null
     */
    public $cloudCount = 10;

    /**
     * @var string|null
     */
    public $titleFormat;

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
    public $helpers = array('navigation');

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("List pages based on type, area.");
    }

    public function getBlockTypeName()
    {
        return t("Tags");
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::TAXONOMY
        ];
    }

    public function add()
    {
        $ak = $this->loadAttribute();
        $this->set('ak', $ak);
        if ($this->isValidStack(Page::getCurrentPage())) {
            $this->set('inStackDashboardPage', true);
        } else {
            $this->set('inStackDashboardPage', false);
        }
        $this->set('displayMode', 'page');
        $this->set('titleFormat', 'h5');
        $this->set('title', '');
        $this->set('cloudCount', 10);
        $this->set('targetCID', null);
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
        } else {
            $this->set('inStackDashboardPage', false);
        }
    }

    public function view()
    {
        $ak = $this->loadAttribute();
        $options = array();
        if ($this->displayMode == "cloud") {
            $type = $ak->getAttributeType();
            $controller = $type->getController();
            $controller->setAttributeKey($ak);
            $items = $controller->getOptions();
        } else {
            $c = Page::getCurrentPage();
            $av = $c->getAttributeValueObject($ak);
            $controller = $ak->getController();
            $attributeValue = $c->getAttribute($ak->getAttributeKeyHandle());
            $items = is_object($attributeValue) ? $attributeValue->getSelectedOptions() : [];
        }

        if ($this->targetCID > 0) {
            $target = Page::getByID($this->targetCID);
            $this->set('target', $target);
        }

        $options = [];
        if ($this->cloudCount > 0 && count($items) > 0) {
            $i = 1;
            foreach ($items as $item) {
                $options[] = $item;
                if ($i >= $this->cloudCount) {
                    break;
                }
                ++$i;
            }
        } else {
            $options = $items;
        }

        // grab selected tag, if we're linking to a page with a tag block on it.
        if (isset($_REQUEST['akID']) && is_array($_REQUEST['akID'])) {
            $res = $_REQUEST['akID'][$ak->getAttributeKeyID()]['atSelectOptionID'][0];
            if (is_numeric($res) && $res > 0) {
                $selectedOptionID = $res;
            }
        }
        $this->set('selectedOptionID', isset($selectedOptionID) ? $selectedOptionID : '');
        $this->set('options', $options);
        $this->set('akc', $controller);
        $this->set('ak', $ak);
    }

    public function save($args)
    {
        $args += [
            'cloudCount' => 0,
            'targetCID' => 0
        ];

        $ak = $this->loadAttribute();
        $cID = $this->request->request->get('cID');
        if (!$cID) {
            $cID = $this->request->query->get('cID');
        }
        if ($cID) {
            $c = Page::getByID((int) $cID, 'RECENT');
            // We cannot save the attribute in the Stack Dashboard page
            // as there is nothing to attach it to
            if (!$this->isValidStack($c)) {
                $nvc = $c->getVersionToModify();
                $controller = $ak->getController();
                $controller->setAttributeObject($nvc);
                $value = $controller->createAttributeValueFromRequest();
                $nvc->setAttribute($ak, $value);
                $nvc->refreshCache();
            }
        }
        $args['cloudCount'] = (is_numeric($args['cloudCount']) ? $args['cloudCount'] : 0);
        $args['targetCID'] = (is_numeric($args['targetCID']) ? $args['targetCID'] : 0);
        parent::save($args);
    }

    public function getTagLink(?SelectValueOption $option = null)
    {
        $target = $this->get('target');
        if (!is_object($target)) {
            $target = \Page::getCurrentPage();
        }
        if ($option) {
            return \URL::page($target, 'tag', mb_strtolower($option->getSelectAttributeOptionDisplayValue()));
        } else {
            return \URL::page($target);
        }
    }

    protected function isValidStack($stack)
    {
        return $stack->getCollectionParentID() == Page::getByPath(STACKS_PAGE_PATH)->getCollectionID();
    }

    public function action_tag($tag = false)
    {
        if ($tag) {
            $this->set('selectedTag', $tag);
        }
        $this->view();
    }
}
