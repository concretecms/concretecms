<?php
namespace Concrete\Core\Command\Task\Input;

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
    protected $value;

    /**
     * AbstractField constructor.
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
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
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function jsonSerialize()
    {
        return [
            'key' => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }



}
