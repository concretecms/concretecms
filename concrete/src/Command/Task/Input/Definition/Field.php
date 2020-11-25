<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Command\Task\Input\Field as LoadedField;

defined('C5_EXECUTE') or die("Access Denied.");

class Field implements FieldInterface
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $description;

    /**
     * AbstractField constructor.
     * @param string $key
     * @param string $label
     * @param string $description
     */
    public function __construct(string $key, string $label, string $description)
    {
        $this->key = $key;
        $this->label = $label;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function loadFieldFromRequest(array $data): LoadedFieldInterface
    {
        $field = new LoadedField($this->getKey(), $data[$this->getKey()]);
        return $field;
    }

    public function jsonSerialize()
    {
        return [
            'label' => $this->getLabel(),
            'key' => $this->getKey(),
            'description' => $this->getDescription(),
        ];
    }


}
