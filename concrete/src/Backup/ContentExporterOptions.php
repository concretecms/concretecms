<?php

declare(strict_types=1);

namespace Concrete\Core\Backup;

use Concrete\Core\Http\Request;

final class ContentExporterOptions
{
    /**
     * Should we export items by using IDs instead of installation-independent identifiers?
     *
     * @var bool
     */
    private $exportIDs;

    /**
     * Should we export file references as <filename> instead of <prefix>:<filename>?
     *
     * @var bool
     */
    private $exportFilesWithoutPrefix = false;

    /**
     * Should we export file references by using their UUID instead of their ID?
     *
     * @var bool
     */
    private $exportFilesAsUUID;

    public function __construct(Request $request)
    {
        $isApiRequest = (bool) preg_match('{^/ccm/api/\d+(\.\d+)*/.}i', $request->getPath());
        if ($isApiRequest) {
            $this->exportIDs = (string) $request->query->get('export_ids', '') === '' || $request->query->getBoolean('export_ids');
        } else {
            $this->exportIDs = false;
        }
        $this->exportFilesAsUUID = $isApiRequest;
    }

    /**
     * Should we export items by using IDs instead of installation-independent identifiers?
     */
    public function isExportIDs(): bool
    {
        return $this->exportIDs;
    }

    /**
     * Should we export items by using IDs instead of installation-independent identifiers?
     *
     * @return $this
     */
    public function setExportIDs(bool $value): self
    {
        $this->exportIDs = $value;

        return $this;
    }

    /**
     * Should we export file references as <filename> instead of <prefix>:<filename>?
     */
    public function isExportFilesWithoutPrefix(): bool
    {
        return $this->exportFilesWithoutPrefix;
    }

    /**
     * Should we export file references by using their UUID instead of their ID?
     *
     * Files don't necessarily have a UUID: the ID is used for the ones that don't have it.
     */
    public function isExportFilesAsUUID(): bool
    {
        return $this->exportFilesAsUUID;
    }

    /**
     * Should we export file references by using their UUID instead of their ID?
     *
     * UUIDs are stable across installations, whereas IDs are not: that's why this option is turned on
     * by default when serving an API request.
     * Beware: files don't necessarily have a UUID, and the ID is used for the ones that don't have it.
     *
     * @return $this
     */
    public function setExportFilesAsUUID(bool $value): self
    {
        $this->exportFilesAsUUID = $value;

        return $this;
    }

    /**
     * Should we export file references as <filename> instead of <prefix>:<filename>?
     *
     * Files imported from a CIF package don't necessarily keep the prefix they had in the source
     * installation: when the file name in the "files" directory isn't in the <prefix>_<filename> form,
     * the importer generates a brand new prefix (see \Concrete\Core\File\Import\FileImporter::generatePrefix()).
     * In that case the prefix of the source installation can't identify the files, and the references
     * should be exported with the file name only.
     * Beware: file names alone may not be unique, so the imported references may resolve to another file.
     *
     * @return $this
     */
    public function setExportFilesWithoutPrefix(bool $value): self
    {
        $this->exportFilesWithoutPrefix = $value;

        return $this;
    }
}
