<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ExpressEntryDataFieldData implements DataFieldDataInterface
{

    /**
     * @var Entry
     */
    protected $entry;
    
    public function __construct(Entry $entry = null)
    {
        if ($entry !== null) {
            $this->entry = $entry;
        }
    }

    public function getEntry()
    {
        return $this->entry;
    }
   
    public function __toString()
    {
        return (string) $this->entry->getLabel();
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'exEntryID' => $this->entry->getID()
        ];
    }

    public function __call($name, $arguments)
    {
        return $this->entry->$name(...$arguments);
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['exEntryID'])) {
            $app = Facade::getFacadeApplication();
            $objectManager = $app->make(ObjectManager::class);
            $this->entry = $objectManager->getEntry($data['exEntryID']);
        }
    }
}
