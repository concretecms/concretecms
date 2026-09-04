<?php
namespace Concrete\Core\File\Exception;

class FileVersionException extends FileException
{
    protected $fileVersionObject;

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @param \Concrete\Core\Entity\File\Version $fileVersion
     * @param string       $message
     * @param int          $code
     * @param \Exception   $previous
     */
    public function __construct(
        $file,
        $fileVersion,
        $message = "",
        $code = 0,
        ?\Exception $previous = null
    ) {
        $this->fileVersionObject = $fileVersion;
        parent::__construct($file, $message, $code, $previous);
    }

    /**
     * @return \Concrete\Core\Entity\File\Version
     */
    public function getFileVersionObject()
    {
        return $this->fileVersionObject;
    }
}
