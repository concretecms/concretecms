<?php
namespace Concrete\Attribute\PageSelector;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Page\Page;

class Controller extends AttributeTypeController
{
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('link');
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        if (!$value) {
            if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                $value = $this->createAttributeValue((int) $this->request->query->get($this->attributeKey->getAttributeKeyHandle()));
            }
        }
        $this->set('value', $value);
        $this->set('page_selector', $this->app->make('helper/form/page_selector'));
    }

    public function getDisplayValue()
    {
        $cID = $this->getAttributeValue()->getValue();
        $page = Page::getByID($cID, 'ACTIVE');
        if (is_object($page) && !$page->isError()) {
            return t('<a href="%s">%s</a>', $page->getCollectionLink(), $page->getCollectionName());
        }
    }

    public function getPlainTextValue()
    {
        $cID = $this->getAttributeValue()->getValue();
        $page = Page::getByID($cID, 'ACTIVE');
        if (is_object($page) && !$page->isError()) {
            return $page->getCollectionLink();
        }
    }

    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        if ($value instanceof Page) {
            $value = $value->getCollectionID();
        }
        $av->setValue($value);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (isset($data['value'])) {
            return $this->createAttributeValue((int) $data['value']);
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $c = Page::getByPath((string) $akv->value);
            if (is_object($c) && !$c->isError()) {
                return $c->getCollectionID();
            }
        }
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        if (is_object($this->attributeValue)) {
            $cID = $this->getAttributeValue()->getValue();
            $page = Page::getByID($cID, 'ACTIVE');
            $avn = $akn->addChild('value', $page->getCollectionPath());
        }
    }
}
