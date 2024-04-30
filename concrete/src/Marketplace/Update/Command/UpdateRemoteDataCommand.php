<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Marketplace\Update\UpdatedFieldInterface;

final class UpdateRemoteDataCommand extends Command
{

    /**
     * @var UpdatedFieldInterface[]
     */
    protected $fields = [];

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array|UpdatedFieldInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }



}
