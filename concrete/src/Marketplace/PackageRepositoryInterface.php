<?php

namespace Concrete\Core\Marketplace;

use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidPackageException;
use Concrete\Core\Marketplace\Exception\PackageAlreadyExistsException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Exception\UnableToPlacePackageException;
use Concrete\Core\Marketplace\Model\RemotePackage;
use Concrete\Core\Marketplace\Model\ValidateResult;
use Concrete\Core\Marketplace\Update\UpdatedFieldInterface;

interface PackageRepositoryInterface
{
    /**
     * Load a package by its remote ID
     */
    public function getPackage(ConnectionInterface $connection, string $packageId): ?RemotePackage;

    /**
     * Get a list of remote packages that are available for this connection
     * @param ConnectionInterface $connection
     * @param bool $latestOnly Only show the latest version of each package
     * @param bool $compatibleOnly Only show package versions that are compatible with the current concrete version
     * @return RemotePackage[]
     */
    public function getPackages(ConnectionInterface $connection, bool $latestOnly = false, bool $compatibleOnly = false): array;

    /**
     * Download a remote package and make it available for install
     * @throws PackageAlreadyExistsException If the package already exists and $overwrite is false.
     * @throws UnableToPlacePackageException If we're unable to move the downloaded package into it's proper spot.
     * @throws InvalidPackageException If the downloaded package doesn't look right, for example if there's no controller.php
     */
    public function download(ConnectionInterface $connection, RemotePackage $package, bool $overwrite = false): void;

    /**
     * Get the existing connection if one is set
     */
    public function getConnection(): ?ConnectionInterface;

    /**
     * Attempt to connect to the package repository
     * @throws InvalidConnectResponseException When a connection response doesn't match what was expected
     * @throws UnableToConnectException When connection fails for any other reason
     */
    public function connect(): ConnectionInterface;

    /**
     * Registers a new canonical URL with an existing marketplace connection.
     *
     * @param ConnectionInterface $connection
     * @return void
     */
    public function registerUrl(ConnectionInterface $connection): void;

    /**
     * Determine if a given connection is valid for the current site
     * @return bool|ValidateResult
     */
    public function validate(ConnectionInterface $connection, bool $returnFullObject = false);

    /**
     * Sends one or more updated fields to the marketplace backend for optional stage in the remote site object.
     * @param ConnectionInterface $connection
     * @param UpdatedFieldInterface[] $updatedFields
     */
    public function update(ConnectionInterface $connection, array $updatedFields): void;
}
