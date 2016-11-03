<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(
 *     name="AttributeValues"
 * )
 */
abstract class Value implements AttributeValueInterface
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\Value", cascade={"persist"}, inversedBy="attribute_values")
     * @ORM\JoinColumn(name="avValueID", referencedColumnName="avValueID")
     **/
    protected $value;

    /**
     * @return Key
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    public function getAttributeValueID()
    {
        return $this->avID;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }

    public function getAttributeTypeObject()
    {
        return $this->getAttributeKey()->getAttributeType();
    }

    public function getController()
    {
        $controller = $this->getAttributeKey()->getController();
        $controller->setAttributeValue($this);

        return $controller;
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Value\Value\Value
     */
    final public function getValueObject()
    {
        return $this->value;
    }

    public function getValue($mode = false)
    {
        $value = $this->value;

        // legacy
        if ($mode == 'displaySanitized' || $mode == 'display') {
            return $this->getDisplayValue();
        }

        // Otherwise, we get the default "value" response for the attribute value type, which could be text, could be true/false, could be a
        // file object.
        if (is_object($value)) {
            return $value->getValue();
        }

        $controller = $this->getController();
        return $controller->getValue();
    }

    /**
     * @deprecated
     */
    public function getDisplaySanitizedValue()
    {
        return $this->getDisplayValue();
    }

    /**
     * Returns content that can be displayed on profile pages, elsewhere. Filters
     * problematic content (sanitizes)
     * @return mixed
     */
    public function getDisplayValue()
    {
        $controller = $this->getController();
        if (method_exists($controller, 'getDisplayValue')) {
            return $controller->getDisplayValue();
        }
        return $this->getValue();
    }

    /**
     * Returns content that is useful in plain text contexts.
     * @return string
     */
    public function getPlainTextValue()
    {

        $controller = $this->getController();
        if (method_exists($controller, 'getPlainTextValue')) {
            return $controller->getPlainTextValue();
        }

        if ($this->getValueObject()) {
            return (string) $this->getValueObject();
        }

        // Legacy support.
        return $controller->getValue();
    }

    /**
     * Returns the attribute in the context of search indexing (for search index
     * database tables)
     * @return $this
     */
    public function getSearchIndexValue()
    {
        $controller = $this->getController();
        if (method_exists($controller, 'getSearchIndexValue')) {
            return $controller->getSearchIndexValue();
        }

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string) $this->getDisplayValue();
    }
}
