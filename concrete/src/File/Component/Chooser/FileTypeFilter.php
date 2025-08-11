<?php
namespace Concrete\Core\File\Component\Chooser;

class FileTypeFilter implements FilterInterface
{

    /**
     * Corresponds to the Concrete\Core\File\Type\Type constants
     * @var int
     */
    protected $type;

    /**
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['filter' => 'type', 'type' => $this->type];
    }



}