<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update\Command;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Marketplace\Exception\ErrorSavingRemoteDataException;
use Concrete\Core\Marketplace\PackageRepositoryInterface;

class UpdateRemoteDataCommandHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_MARKETPLACE;
    }

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
            try {
                $this->repository->update($connection, $command->getFields());
            } catch (ErrorSavingRemoteDataException $e) {
                $this->logger->warning(t('Error updating remote data: {message}'), ['message' => $e->getMessage()]);
            }
        }
    }
}
