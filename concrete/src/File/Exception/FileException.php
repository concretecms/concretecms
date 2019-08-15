<?php
namespace Concrete\Core\File\Exception;

class FileException extends \Exception
{
    protected $fileObject;

    /**
     * @param \Concrete\Core\File\File      $file
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct(
        $file,
        $message = "",
        $code = 0,
        \Exception $previous = null
    ) {
        $this->fileObject = $file;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return \Concrete\Core\File\File
     */
    public function getFileObject()
    {
        return $this->fileObject;
    }
}
