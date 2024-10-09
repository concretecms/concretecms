<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update;

interface UpdatedFieldInterface
{
    public const FIELD_NAME = 'name';
    public const FIELD_USERS = 'users';
    public const FIELD_PRIVILEGED_USERS = 'privileged_users';
    public const FIELD_SITES = 'sites';
    public const FIELD_LOCALE = 'locale';
    public const FIELD_PACKAGES = 'packages';

    public function getName(): string;
    public function getData();
}
