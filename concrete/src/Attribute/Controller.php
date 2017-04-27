<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Form\Control\View\View as ControlView;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Concrete\Core\Attribute\View as AttributeTypeView;
use Doctrine\ORM\EntityManager;

class Controller extends AbstractController implements AttributeInterface
{
    protected $entityManager;

    /** @var \Concrete\Core\Attribute\Key\Key */
    protected $attributeKey;
    /** @var \Concrete\Core\Entity\Attribute\Value\AbstractValue */
    protected $attributeValue;
    protected $searchIndexFieldDefinition;
    protected $requestArray = false;

    public function setRequestArray($array)
    {
        $this->requestArray = $array;
    }

    public function setAttributeKey($attributeKey)
    {
        $this->attributeKey = $attributeKey;
    }

    public function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }

    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    public function getDisplayValue()
    {
        if (is_object($this->attributeValue)) {
            return (string) $this->attributeValue->getValueObject();
        }
    }

    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    public function getAttributeType()
    {
        return $this->attributeType;
    }

    public function exportKey($ak)
    {
        return $ak;
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            return (string) $akv->value;
        }
    }

    public function importKey(\SimpleXMLElement $element)
    {
    }

    public function getValidator()
    {
        return \Core::make('Concrete\Core\Attribute\StandardValidator');
    }

    public function deleteKey()
    {
        $settings = $this->retrieveAttributeKeySettings();
        if (is_object($settings)) {
            $this->entityManager->remove($settings);
            $this->entityManager->flush();
        }
    }

    public function deleteValue()
    {
    }

    public function exportValue(\SimpleXMLElement $akv)
    {
        $val = $this->attributeValue->getValue();
        if (is_object($val)) {
            $val = (string) $val;
        }

        if (is_array($val)) {
            $val = json_encode($val);
        }

        $cnode = $akv->addChild('value');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDataSection($val));

        return $cnode;
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        $list->filter('ak_' . $this->attributeKey->getAttributeKeyHandle(), $value, $comparison);
    }

    public function field($fieldName)
    {
        return 'akID[' . $this->attributeKey->getAttributeKeyID() . '][' . $fieldName . ']';
    }

    public function getControlView(ContextInterface $context)
    {
        return new ControlView($context, $this->getAttributeKey(), $this->getAttributeValue());
    }

    public function label($customText = false)
    {
        if ($customText == false) {
            $text = $this->attributeKey->getAttributeKeyDisplayName();
        } else {
            $text = $customText;
        }
        /** @var \Concrete\Core\Form\Service\Form $form */
        $form = Core::make('helper/form');
        echo $form->label($this->getLabelID(), $text);
    }

    /**
     * Get the ID to use for label elements
     */
    public function getLabelID()
    {
        return $this->field('value');
    }

    /**
     * @param \Concrete\Core\Attribute\Type $attributeType
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->set('controller', $this);
    }

    public function setAttributeType($attributeType)
    {
        $this->attributeType = $attributeType;
    }
    public function post($field = false, $defaultValue = null)
    {
        // the only post that matters is the one for this attribute's name space
        $req = ($this->requestArray == false) ? $this->request->request->all() : $this->requestArray;
        if (is_object($this->attributeKey) && isset($req['akID']) && is_array($req['akID'])) {
            $akID = $this->attributeKey->getAttributeKeyID();
            $p = isset($req['akID'][$akID]) ? $req['akID'][$akID] : null;
            if ($field) {
                return (is_array($p) && isset($p[$field])) ? $p[$field] : null;
            }

            return $p;
        }

        return parent::post($field, $defaultValue);
    }

    public function requestFieldExists()
    {
        $request = array_merge($this->request->request->all(), $this->request->query->all());
        $req = ($this->requestArray == false) ? $request : $this->requestArray;
        if (is_object($this->attributeKey) && is_array($req['akID'])) {
            return true;
        }

        return false;
    }

    public function request($field = false)
    {
        $request = array_merge($this->request->request->all(), $this->request->query->all());
        $req = ($this->requestArray == false) ? $request : $this->requestArray;
        if (is_object($this->attributeKey) && is_array($req['akID'])) {
            $p = $req['akID'][$this->attributeKey->getAttributeKeyID()];
            if ($field) {
                return $p[$field];
            }

            return $p;
        }

        return parent::request($field);
    }

    public function getView()
    {
        if ($this->attributeValue) {
            $av = new AttributeTypeView($this->attributeValue);
        } else {
            if ($this->attributeKey) {
                $av = new AttributeTypeView($this->attributeKey);
            } else {
                $av = new AttributeTypeView($this->attributeType);
            }
        }

        return $av;
    }

    public function getSearchIndexValue()
    {
        return $this->attributeValue->getValue();
    }

    public function getSearchIndexFieldDefinition()
    {
        return $this->searchIndexFieldDefinition;
    }

    public function setupAndRun($method)
    {
        $args = func_get_args();
        $args = array_slice($args, 1);
        if ($method) {
            $this->task = $method;
        }
        if (method_exists($this, 'on_start')) {
            $this->on_start($method);
        }
        if ($method == 'composer') {
            $method = ['composer', 'form'];
        }

        if ($method) {
            $this->runTask($method, $args);
        }

        if (method_exists($this, 'on_before_render')) {
            $this->on_before_render($method);
        }
    }

    public function getAttributeTypeFileURL($_file)
    {
        $env = \Environment::get();
        $r = $env->getRecord(
            implode('/', [DIRNAME_ATTRIBUTES . '/' . $this->attributeType->getAttributeTypeHandle() . '/' . $_file]),
            $this->attributeType->getPackageHandle()
        );
        if ($r->exists()) {
            return $r->url;
        }
    }

    public function saveKey($data)
    {
    }

    public function duplicateKey($newAK)
    {
    }

    public function createAttributeValueFromRequest()
    {
        return new EmptyRequestAttributeValue();
    }

    /**
     * Create the default attribute value (if needed).
     *
     * @return \Concrete\Core\Entity\Attribute\Value\Value|null
     */
    public function createDefaultAttributeValue()
    {
        return null;
    }

    public function createAttributeValue($mixed)
    {
        return $this->saveValue($mixed);
    }

    /**
     * @deprecated
     */
    public function saveForm($data)
    {
    }

    /**
     * @deprecated
     */
    public function saveValue($mixed)
    {
        return false;
    }

    public function searchKeywords($keywords, $queryBuilder)
    {
        return $queryBuilder->expr()->like('ak_' . $this->attributeKey->getAttributeKeyHandle(), ':keywords');
    }

    public function validateKey($data = false)
    {
        $e = $this->app->make('error');

        return $e;
    }

    public function getAttributeKeySettingsClass()
    {
        return EmptySettings::class;
    }

    public function createAttributeKeySettings()
    {
        $class = $this->getAttributeKeySettingsClass();
        return new $class();
    }

    protected function retrieveAttributeKeySettings()
    {
        return $this->entityManager->find($this->getAttributeKeySettingsClass(), $this->attributeKey);
    }

    /*
     * @deprecated
     */
    public function getAttributeValueID()
    {
        if (is_object($this->attributeValue)) {
            return $this->attributeValue->getAttributeValueID();
        }
    }

    public function getAttributeValueClass()
    {
        return null;
    }

    public function getAttributeValueObject()
    {
        $class = $this->getAttributeValueClass();
        if ($class) {
            return $this->entityManager->find($class, $this->attributeValue->getGenericValue());
        }
    }

    public function getAttributeKeySettings()
    {
        $settings = null;
        if ($this->attributeKey) {
            $settings = $this->retrieveAttributeKeySettings();
        }
        if (!is_object($settings)) {
            $settings = $this->createAttributeKeySettings();
        }
        return $settings;
    }

    public function getIconFormatter()
    {
        return new FileIconFormatter($this);
    }
}
