<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\CheckerGenerator;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The details of a permission key, as needed by the CheckerGenerator.
 */
final class PermissionKey
{
    /**
     * @var string
     */
    private $categoryHandle;

    /**
     * @var string
     */
    private $handle;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    public function __construct(string $categoryHandle, string $handle, string $name = '', string $description = '')
    {
        $this->categoryHandle = $categoryHandle;
        $this->handle = $handle;
        $this->name = $name;
        $this->description = $description;
    }

    public function getCategoryHandle(): string
    {
        return $this->categoryHandle;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
