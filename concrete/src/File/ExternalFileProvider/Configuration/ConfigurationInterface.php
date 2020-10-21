<?php

namespace Concrete\Core\File\ExternalFileProvider\Configuration;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\File\ExternalFileProvider\ExternalFileList;
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
     * @return Error
     */
    public function validateRequest(Request $req);

    /**
     * @return mixed
     */
    public function getTypeObject();

    /**
     * @param string $keyword
     * @param string $selectedFileType
     * @return ExternalFileList
     */
    public function searchFiles($keyword, $selectedFileType);

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
     * @param $fileId
     * @return Version
     */
    public function importFile($fileId);
}
