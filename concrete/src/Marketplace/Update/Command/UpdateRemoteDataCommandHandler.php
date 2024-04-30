<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update\Command;

use Concrete\Core\Marketplace\PackageRepositoryInterface;

class UpdateRemoteDataCommandHandler
{
    /**
     * @var PackageRepositoryInterface
     */
    protected $repository;

    /**
     * @param PackageRepositoryInterface $repository
     */
    public function __construct(PackageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateRemoteDataCommand $command): void
    {
        $connection = $this->repository->getConnection();
        if ($connection) {
            $this->repository->update($connection, $command->getFields());
        }
    }
}
