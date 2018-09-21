<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Form\Control\View\View as ControlView;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Attribute\View as AttributeTypeView;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Doctrine\ORM\EntityManager;
use SimpleXMLElement;

class Controller extends AbstractController implements AttributeInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Concrete\Core\Attribute\Key\Key|null
     */
    protected $attributeKey;

    /**
     * @var \Concrete\Core\Entity\Attribute\Value\AbstractValue
     */
    protected $attributeValue;

    /**
     * @var array|null
     */
    protected $searchIndexFieldDefinition;

    /**
     * @var false|array
     */
    protected $requestArray = false;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->set('controller', $this);
    }

    public function __destruct()
    {
        unset($this->attributeKey);
        unset($this->attributeValue);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getIconFormatter()
     */
    public function getIconFormatter()
    {
        return new FileIconFormatter($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::setAttributeType()
     */
    public function setAttributeType($attributeType)
    {
        $this->attributeType = $attributeType;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getAttributeType()
     */
    public function getAttributeType()
    {
        return isset($this->attributeType) ? $this->attributeType : null;
    }

    /**
     * @param string $_file
     *
     * @return string|null
     */
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

    /**
     * @param array|false $data
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList
     */
    public function validateKey($data = false)
    {
        $e = $this->app->make('error');

        return $e;
    }

    /**
     * @return string
     */
    public function getAttributeKeySettingsClass()
    {
        return EmptySettings::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see AttributeInterface::setAttributeKey()
     */
    public function setAttributeKey($attributeKey)
    {
        $this->attributeKey = $attributeKey;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getAttributeKey()
     */
    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::saveKey()
     */
    public function saveKey($data)
    {
    }

    /**
     * @param mixed $newAK
     */
    public function duplicateKey($newAK)
    {
    }

    public function deleteKey()
    {
        $settings = $this->retrieveAttributeKeySettings();
        if (is_object($settings)) {
            $this->entityManager->remove($settings);
            $this->entityManager->flush();
        }
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\Settings\Settings
     */
    public function createAttributeKeySettings()
    {
        $class = $this->getAttributeKeySettingsClass();

        return new $class();
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\Settings\Settings
     */
    public function getAttributeKeySettings()
    {
        $settings = null;
        if ($this->attributeKey) {
            $settings = $this->retrieveAttributeKeySettings();
        }
        if (!$settings) {
            $settings = $this->createAttributeKeySettings();
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getAttributeValueClass()
     */
    public function getAttributeValueClass()
    {
        return null;
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Value\AbstractValue|null $attributeValue
     */
    public function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getAttributeValue()
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Value\AbstractValue|null
     */
    public function getAttributeValueObject()
    {
        $class = $this->getAttributeValueClass();
        if ($class && $this->attributeValue) {
            $result = $this->entityManager->find($class, $this->attributeValue->getGenericValue());
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Create the default attribute value (if needed).
     *
     * @return \Concrete\Core\Entity\Attribute\Value\AbstractValue|null
     */
    public function createDefaultAttributeValue()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::createAttributeValueFromRequest()
     */
    public function createAttributeValueFromRequest()
    {
        return new EmptyRequestAttributeValue();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::createAttributeValue()
     */
    public function createAttributeValue($mixed)
    {
        return $this->saveValue($mixed);
    }

    public function deleteValue()
    {
    }

    /**
     * @return array|null
     */
    public function getSearchIndexFieldDefinition()
    {
        return $this->searchIndexFieldDefinition;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getSearchIndexValue()
     */
    public function getSearchIndexValue()
    {
        return $this->attributeValue ? $this->attributeValue->getValue() : null;
    }

    /**
     * @param mixed $keywords
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     *
     * @return string|null
     */
    public function searchKeywords($keywords, $queryBuilder)
    {
        if ($this->attributeKey) {
            $result = $queryBuilder->expr()->like('ak_' . $this->attributeKey->getAttributeKeyHandle(), ':keywords');
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param AttributedItemList $list
     * @param mixed $value
     * @param string $comparison
     */
    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        if ($this->attributeKey) {
            $list->filter('ak_' . $this->attributeKey->getAttributeKeyHandle(), $value, $comparison);
        }
    }

    /**
     * @param SimpleXMLElement $element
     */
    public function importKey(SimpleXMLElement $element)
    {
    }

    /**
     * @param mixed $ak
     *
     * @return mixed
     */
    public function exportKey($ak)
    {
        return $ak;
    }

    /**
     * @param SimpleXMLElement $akv
     *
     * @return mixed
     */
    public function importValue(SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            return (string) $akv->value;
        }
    }

    /**
     * @param SimpleXMLElement $akv
     *
     * @return SimpleXMLElement
     */
    public function exportValue(SimpleXMLElement $akv)
    {
        $val = '';
        if ($this->attributeValue) {
            $val = $this->attributeValue->getValue();
            if (is_object($val)) {
                $val = (string) $val;
            }
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getDisplayValue()
     */
    public function getDisplayValue()
    {
        return $this->attributeValue ? (string) $this->attributeValue->getValueObject() : '';
    }

    /**
     * @param ContextInterface $context
     *
     * @return \Concrete\Core\Form\Control\FormViewInterface
     */
    public function getControlView(ContextInterface $context)
    {
        return new ControlView($context, $this->getAttributeKey(), $this->getAttributeValue());
    }

    /**
     * @return AttributeTypeView
     */
    public function getView()
    {
        if ($this->attributeValue) {
            $av = new AttributeTypeView($this->attributeValue);
        } elseif ($this->attributeKey) {
           $av = new AttributeTypeView($this->attributeKey);
        } elseif (isset($this->attributeType)) {
            $av = new AttributeTypeView($this->attributeType);
        } else {
            $av = new AttributeTypeView(null);
        }

        return $av;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function field($fieldName)
    {
        return 'akID[' . $this->attributeKey->getAttributeKeyID() . '][' . $fieldName . ']';
    }

    /**
     * Get the ID to use for label elements. Not applicable in form views that do
     * not contain <label>
     * @return string
     */
    public function getControlID()
    {
        return $this->field('value');
    }

    /**
     * Get the ID to use for label elements.
     * @deprecated
     * @return string
     */
    public function getLabelID()
    {
        return $this->getControlID();
    }

    /**
     * @deprecated . This should be handled by the templates including <label> tags and using
     * getControlID() within them.
     * @param string|bool $customText
     */
    public function label($customText = false)
    {
        if ($customText == false) {
            $text = $this->attributeKey->getAttributeKeyDisplayName();
        } else {
            $text = $customText;
        }
        $form = $this->app->make('helper/form');
        echo $form->label($this->getControlID(), $text);
    }

    /**
     * @param array|false $array
     */
    public function setRequestArray($array)
    {
        $this->requestArray = $array;
    }

    /**
     * @return \Concrete\Core\Attribute\ValidatorInterface
     */
    public function getValidator()
    {
        return $this->app->make(StandardValidator::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::post()
     */
    public function post($field = false, $defaultValue = null)
    {
        // the only post that matters is the one for this attribute's name space
        $req = ($this->requestArray == false) ? $this->request->request->all() : $this->requestArray;
        if ($this->attributeKey && isset($req['akID']) && is_array($req['akID'])) {
            $akID = $this->attributeKey->getAttributeKeyID();
            $p = isset($req['akID'][$akID]) ? $req['akID'][$akID] : null;
            if ($field) {
                return (is_array($p) && isset($p[$field])) ? $p[$field] : null;
            }

            return $p;
        }

        return parent::post($field, $defaultValue);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::request()
     */
    public function request($field = false)
    {
        $request = array_merge($this->request->request->all(), $this->request->query->all());
        $req = ($this->requestArray == false) ? $request : $this->requestArray;
        if ($this->attributeKey && is_array($req['akID'])) {
            $p = $req['akID'][$this->attributeKey->getAttributeKeyID()];
            if ($field) {
                return $p[$field];
            }

            return $p;
        }

        return parent::request($field);
    }

    /**
     * @return bool
     */
    public function requestFieldExists()
    {
        $request = array_merge($this->request->request->all(), $this->request->query->all());
        $req = ($this->requestArray == false) ? $request : $this->requestArray;
        if ($this->attributeKey && is_array($req['akID'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $method
     */
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

    /**
     * @deprecated
     *
     * @param mixed $data
     */
    public function saveForm($data)
    {
    }

    /**
     * @deprecated
     *
     * @param mixed $mixed
     */
    public function saveValue($mixed)
    {
        return false;
    }

    /**
     * @deprecated
     */
    public function getAttributeValueID()
    {
        if (is_object($this->attributeValue)) {
            return $this->attributeValue->getAttributeValueID();
        }
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\Settings\Settings|null
     */
    protected function retrieveAttributeKeySettings()
    {
        return $this->entityManager->find($this->getAttributeKeySettingsClass(), $this->attributeKey);
    }
}
