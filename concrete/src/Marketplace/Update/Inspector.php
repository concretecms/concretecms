<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\PackageService;

final class Inspector
{

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var PackageService
     */
    protected $packageService;

    public function __construct(PackageService $packageService, Connection $db)
    {
        $this->packageService = $packageService;
        $this->db = $db;
    }

    public function getUsersField(): UpdatedFieldInterface
    {
        return new UpdatedField(
            UpdatedFieldInterface::FIELD_USERS,
            (int)$this->db->fetchOne('select count(uID) from Users')
        );
    }

    public function getPrivilegedUsersField(): UpdatedFieldInterface
    {
        $privilegedUsers = (int)$this->db->fetchOne('select count(distinct uID) from UserGroups');
        $privilegedUsers++; // We have to count "admin" as well.
        return new UpdatedField(
            UpdatedFieldInterface::FIELD_PRIVILEGED_USERS,
            $privilegedUsers
        );
    }

    public function getSitesField(): UpdatedFieldInterface
    {
        return new UpdatedField(
            UpdatedFieldInterface::FIELD_SITES,
            (int)$this->db->fetchOne('select count(siteID) from Sites')
        );
    }

    public function getLocaleField(): UpdatedFieldInterface
    {
        return new UpdatedField(UpdatedFieldInterface::FIELD_LOCALE, Localization::activeLocale());
    }

    public function getPackagesField(): UpdatedFieldInterface
    {
        $packages = $this->packageService->getInstalledList();
        $packageHandles = [];
        foreach ($packages as $package) {
            $packageHandles[] = $package->getPackageHandle();
        }
        return new UpdatedField(
            UpdatedFieldInterface::FIELD_PACKAGES,
            json_encode($packageHandles, JSON_THROW_ON_ERROR)
        );
    }

}
