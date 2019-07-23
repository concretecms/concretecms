<?php
namespace Concrete\Core\File\Exception;

class FileVersionException extends FileException
{
    protected $fileVersionObject;

    /**
     * @param \File        $file
     * @param \FileVersion $fileVersion
     * @param string       $message
     * @param int          $code
     * @param \Exception   $previous
     */
    public function __construct(
        $file,
        $fileVersion,
        $message = "",
        $code = 0,
        \Exception $previous = null
    ) {
        $this->fileVersionObject = $fileVersion;
        parent::__construct($file, $message, $code, $previous);
    }

    /**
     * @return \FileVersion
     */
    public function getFileVersionObject()
    {
        return $this->fileVersionObject;
    }
}
