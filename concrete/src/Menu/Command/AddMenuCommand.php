<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Menu\Type\TypeInterface;

class AddMenuCommand extends Command
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @param TypeInterface $type
     */
    public function setType(TypeInterface $type): void
    {
        $this->type = $type;
    }



}
