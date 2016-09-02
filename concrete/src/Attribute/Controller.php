<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Attribute\Key\Type\TextType;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Concrete\Core\Attribute\View as AttributeTypeView;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Doctrine\ORM\EntityManager;

class Controller extends AbstractController
{

    protected $entityManager;

    /** @var \Concrete\Core\Attribute\Key\Key */
    protected $attributeKey;
    /** @var \Concrete\Core\Attribute\Value\Value */
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
        return (string) $this->getValue();
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
    }

    public function deleteValue()
    {
    }

    public function getValue()
    {
        if (is_object($this->attributeValue)) {
            return $this->attributeValue->getValue();
        }
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

    public function label($customText = false)
    {
        if ($customText == false) {
            $text = $this->attributeKey->getAttributeKeyDisplayName();
        } else {
            $text = $customText;
        }
        /** @var \Concrete\Core\Form\Service\Form $form */
        $form = Core::make('helper/form');
        echo $form->label($this->field('value'), $text);
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
            $p = $req['akID'][$this->attributeKey->getAttributeKeyID()];
            if ($field) {
                return $p[$field];
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
            $method = array('composer', 'form');
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
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $this->attributeType->getAttributeTypeHandle() . '/' . $_file)),
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

    public function createAttributeKeyType()
    {
        return new TextType();
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

    public function getAttributeKeyType()
    {
        if ($this->attributeKey && is_object($this->attributeKey->getAttributeKeyType())) {
            return $this->attributeKey->getAttributeKeyType();
        } else {
            $key_type = $this->createAttributeKeyType();
            $key_type->setAttributeTypeHandle($this->getAttributeType()->getAttributeTypeHandle());
            return $key_type;
        }
    }

    public function getIconFormatter()
    {
        return new FileIconFormatter($this);
    }
}
