<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class RemotePackage
{
    /** @var string */
    public $handle;
    /** @var string */
    public $name;
    /** @var string */
    public $description;
    /** @var string */
    public $summary;
    /** @var string */
    public $download;
    /** @var string */
    public $icon;
    /** @var string */
    public $id;
    /** @var string */
    public $version;
    /** @var string */
    public $fileDescription;
    /** @var array */
    public $compatibility;

    public function __construct(
        string $handle,
        string $name,
        string $description,
        string $summary,
        string $download,
        string $icon,
        string $id,
        string $version,
        string $file_description,
        array $compatibility
    ) {
        $this->handle = $handle;
        $this->name = $name;
        $this->description = $description;
        $this->summary = $summary;
        $this->download = $download;
        $this->icon = $icon;
        $this->id = $id;
        $this->version = $version;
        $this->fileDescription = $file_description;
        $this->compatibility = $compatibility;
    }
}
