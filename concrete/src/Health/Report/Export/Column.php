<?php
namespace Concrete\Core\Health\Report\Export;

class Column implements ColumnInterface
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $displayName;

    public function __construct(string $key, string $displayName)
    {
        $this->key = $key;
        $this->displayName = $displayName;
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
    public function getDisplayName(): string
    {
        return $this->displayName;
    }



}
