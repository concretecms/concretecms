<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update;

final class UpdatedField implements UpdatedFieldInterface
{
    /** @var string */
    protected $name;
    /** @var mixed */
    protected $data;

    public function __construct(string $name, $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
