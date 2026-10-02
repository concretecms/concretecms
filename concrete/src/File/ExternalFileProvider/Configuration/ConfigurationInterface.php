<?php

namespace Concrete\Core\File\ExternalFileProvider\Configuration;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\File\ExternalFileProvider\ExternalFileList;
use Concrete\Core\File\ExternalFileProvider\ExternalSearchRequest;
use Concrete\Core\Http\Request;

interface ConfigurationInterface
{

    /**
     * Load in data from a request object
     * @param Request $req
     * @return void
     */
    public function loadFromRequest(Request $req);

    /**
     * Validate a request, this is used during saving
     * @param Request $req
     * @return Error|null NULL if there are no errors
     */
    public function validateRequest(Request $req);

    /**
     * @return mixed
     */
    public function getTypeObject();

    /**
     * @param ExternalSearchRequest $externalSearchRequest
     * @return ExternalFileList
     */
    public function searchFiles($externalSearchRequest);

    /**
     * @return bool
     */
    public function supportFileTypes();

    /**
     * @return array
     */
    public function getFileTypes();

    /**
     * @return bool
     */
    public function hasCustomImportHandler();

    /**
     * @param string|int $fileId the identifier of the file in the external provider
     * @param int $uploadDirectoryId the ID of the folder node where the file should be imported
     *
     * @return Version|null NULL (or any other non-Version value) if the import failed
     */
    public function importFile($fileId, $uploadDirectoryId);
}
