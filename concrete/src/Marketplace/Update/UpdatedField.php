<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update;

class UpdatedField implements UpdatedFieldInterface
{

    protected $name = '';

    protected $data;

    public function __construct(string $name, $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return string
     */
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
