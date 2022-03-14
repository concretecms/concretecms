<?php
namespace Concrete\Core\Command\Task\Input;

use Concrete\Core\Command\Task\Input\FieldInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Responsible for presenting all loaded Concrete\Core\Command\Task\Input\FieldInterface fields to the actual
 * task system.
 *
 * @package Concrete\Core\Command\Task\Input
 */
class Input implements InputInterface, DenormalizableInterface
{

    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }

    public function getField(string $key): ?FieldInterface
    {
        foreach($this->fields as $field) {
            if ($field->getKey() == $key) {
                return $field;
            }
        }
        return null;
    }

    public function hasField(string $key): bool
    {
        foreach($this->fields as $field) {
            if ($field->getKey() == $key) {
                return true;
            }
        }
        return false;
    }

    public function getFields()
    {
        return $this->fields;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fields' => $this->fields,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        foreach ($data['fields'] as $field) {
            $field = $denormalizer->denormalize($field, Field::class, 'json', $context);
            $this->addField($field);
        }
    }


}
