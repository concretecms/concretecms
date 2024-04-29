<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class RemotePackage
{
    public string $handle;
    public string $name;
    public string $description;
    public string $summary;
    public string $download;
    public string $icon;
    public string $id;
    public string $version;
    public string $fileDescription;
    public array $compatibility;

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
