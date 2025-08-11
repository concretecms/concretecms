<?php
namespace Concrete\Core\File\Component\Chooser;

class FileExtensionFilter implements FilterInterface
{

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * FileExtensionFilter constructor.
     * @param array $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['filter' => 'extension', 'extensions' => $this->extensions];
    }



}