<?php

declare(strict_types=1);

namespace Concrete\Core\Utility\Service;

use DateTimeInterface;
use SimpleXMLElement;

class Xml
{
    public const FLAG_PRESERVE_CARRIAGE_RETURNS = 0b1;

    /**
     * Extract a boolean from an element or an attribute value.
     *
     * @param bool|null $default what should be returned if the element/attribute does not exist, or if it does not contain a boolean representation ('true', 'yes', 'on', '1', 'false', 'no', '0', '')
     *
     * @return bool|null returns NULL if and only if $default is null and the attribute/element doesn't exist (or it has an invalid value)
     */
    public function getBool(?SimpleXMLElement $elementOrAttribute, ?bool $default = false): ?bool
    {
        /*
         * $element['nonExistingAttribute'] returns null
         * $element->nonExistingChildElement returns a SimpleXMLElement whose name is an empty string
         * (yep! "isset($element->nonExistingChildElement)" return false, but "$element->nonExistingChildElement" is a SimpleXMLElement !) 
         */
        if ($elementOrAttribute === null || $elementOrAttribute->getName() === '') {
            return $default;
        }

        return filter_var((string) $elementOrAttribute, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    /**
     * Create a new element with the specified value.
     *
     * @param scalar|\DateTimeInterface|\Stringable $value
     */
    public function createChildElement(SimpleXMLElement $parentElement, string $childName, $value, int $flags = 0): SimpleXMLElement
    {
        $serialized = $this->serialize($value);
        $hasCarriageReturns = str_contains($serialized, "\r");
        if ($hasCarriageReturns && ($flags & static::FLAG_PRESERVE_CARRIAGE_RETURNS) === static::FLAG_PRESERVE_CARRIAGE_RETURNS) {
            // We can't use CDATA if we want to preserve \r - see https://www.w3.org/TR/2008/REC-xml-20081126/#sec-line-ends
            $newElement = $parentElement->addChild($childName, $serialized);
        } elseif ($this->shouldUseCData($serialized)) {
            $newElement = $parentElement->addChild($childName);
            $this->appendCData($newElement, $serialized);
        } else {
            if ($hasCarriageReturns) {
                $serialized = strtr($serialized, ["\r\n" => "\n", "\r" => "\n"]);
            }
            $newElement = $parentElement->addChild($childName, $serialized);
        }

        return $newElement;
    }

    /**
     * Append a new CDATA section to an existing element.
     * Please remark that \r\n and \r sequences will be converted to \n - see https://www.w3.org/TR/2008/REC-xml-20081126/#sec-line-ends
     *
     * @param scalar|\DateTimeInterface|\Stringable $value
     */
    public function appendCData(SimpleXMLElement $element, string $value): void
    {
        $domElement = dom_import_simplexml($element);
        $domElement->appendChild($domElement->ownerDocument->createCDataSection($this->serialize($value)));
    }

    /**
     * @deprecated use the createChildElement() method
     */
    public function createCDataNode(SimpleXMLElement $x, $nodeName, $content)
    {
        return $this->createChildElement($x, (string) $nodeName, (string) $content);
    }

    /**
     * @param scalar|\DateTimeInterface|\Stringable $value
     */
    protected function serialize($value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }

    /**
     * Using a CDATA section is not mandatory: it'd be enough to call htmlentities(..., ENT_XML1).
     * But CDATA is much more readable, so let's use it when values contains characters that
     * must be escaped).
     *
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#syntax
     */
    protected function shouldUseCData(string $value): bool
    {
        return strpbrk($value, "&<>") !== false; // '>' is not strictly required, only ']]>' is, but libxml2 escapes even '>' alone
    }
}
