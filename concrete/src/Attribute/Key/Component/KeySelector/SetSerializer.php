<?php
namespace Concrete\Core\Attribute\Key\Component\KeySelector;

use Concrete\Core\Attribute\SetManagerInterface;

/**
 * Responsible for serializing attribute sets for use in the key selector component.
 */
class SetSerializer implements \JsonSerializable
{
    /**
     * @var SetManagerInterface
     */
    protected $sets;

    public function __construct(SetManagerInterface $sets)
    {
        $this->sets = $sets;
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'sets' => [],
            'unassigned' => []
        ];
        foreach($this->sets->getAttributeSets() as $set) {
            $data['sets'][] = $set;
        }
        foreach($this->sets->getUnassignedAttributeKeys() as $key) {
            $data['unassigned'][] = $key;
        }
        return $data;
    }
}
