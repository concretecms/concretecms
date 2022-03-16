<?php

namespace Concrete\Core\File\Import;

use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\File\Service\Mime as MimeService;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Exception;
use Imagine\Image\ImagineInterface;
use Concrete\Core\File\Image\BitmapFormat;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

/**
 * Represents a file being imported.
 */
class ImportingFile
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    protected $fileService;

    /**
     * @var \Concrete\Core\File\Service\Mime
     */
    protected $mimeService;

    /**
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;

    /**
     * @var \Concrete\Core\File\Image\BitmapFormat
     */
    protected $bitmapFormat;

    /**
     * The full path to the local file being imported.
     *
     * @var string
     */
    private $localFilename;

    /**
     * The name of the file for concrete5.
     *
     * @var string
     */
    private $concreteFilename;

    /**
     * The sanitized name of the file for concrete5.
     *
     * @var string|null
     */
    private $concreteFilenameSanitized;

    /**
     * The extension of the file.
     *
     * @var string|null
     */
    private $fileExtension;

    /**
     * The mime type of the file.
     *
     * @var string|null
     */
    private $mimeType;

    /**
     * The type of the file.
     *
     * @var \Concrete\Core\File\Type\Type|null
     */
    private $fileType;

    /**
     * The image loaded from the current local file.
     *
     * @var \Imagine\Image\ImageInterface|false|null
     */
    private $image;

    /**
     * Custom data stored by processors for inter-processors communication.
     *
     * @var array
     */
    private $customData = [];

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Service\File $fileService
     * @param \Concrete\Core\File\Service\Mime $mimeService
     * @param \Imagine\Image\ImagineInterface $imagine
     * @param \Concrete\Core\File\Image\BitmapFormat $bitmapFormat
     * @param string $localFilename the full path to the local file being imported
     * @param string $concreteFilename the name of the file for concrete5 (if empty we'll derive it from $localFilename)
     */
    public function __construct(FileService $fileService, MimeService $mimeService, ImagineInterface $imagine, BitmapFormat $bitmapFormat, $localFilename, $concreteFilename = '')
    {
        $this->fileService = $fileService;
        $this->mimeService = $mimeService;
        $this->imagine = $imagine;
        $this->bitmapFormat = $bitmapFormat;
        $this->localFilename = $localFilename;
        $this->changeConcreteFilename($concreteFilename ?: $localFilename);
    }

    /**
     * @param string $newConcreteFilename
     *
     * @return $this
     */
    public function changeConcreteFilename($newConcreteFilename)
    {
        $newConcreteFilename = basename(trim((string) $newConcreteFilename));
        if ($newConcreteFilename === '') {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
        }
        if ($newConcreteFilename !== $this->concreteFilename) {
            $this->concreteFilename = $newConcreteFilename;
            $this->concreteFilenameSanitized = null;
            $this->fileExtension = null;
            $this->mimeType = null;
            $this->fileType = null;
            $this->releaseImage();
        }

        return $this;
    }

    /**
     * Get the full path to the local file being imported.
     *
     * @return string
     */
    public function getLocalFilename()
    {
        return $this->localFilename;
    }

    /**
     * Get the name of the file for concrete5.
     *
     * @return string
     */
    public function getConcreteFilename()
    {
        return $this->concreteFilename;
    }

    /**
     * Get the sanitized name of the file for concrete5.
     *
     * @return string
     */
    public function getConcreteFilenameSanitized()
    {
        if ($this->concreteFilenameSanitized === null) {
            $this->concreteFilenameSanitized = $this->fileService->sanitize($this->getConcreteFilename());
        }

        return $this->concreteFilenameSanitized;
    }

    /**
     * Get the extension of the file (lower case, without leading dots).
     *
     * @return string
     */
    public function getFileExtension()
    {
        if ($this->fileExtension === null) {
            $this->fileExtension = mb_strtolower($this->fileService->getExtension($this->getConcreteFilenameSanitized()));
        }

        return $this->fileExtension;
    }

    /**
     * Get the MIME type of the file (starting from the sanitized file extension).
     *
     * @return string empty string if unavailable
     */
    public function getMimeType()
    {
        if ($this->mimeType === null) {
            $pathInfo = pathinfo($this->getLocalFilename());
            $local = new Local($pathInfo["dirname"]);
            $fs = new Filesystem($local);

            try {
                $this->mimeType = $fs->getMimetype($pathInfo["basename"]);
            } catch (FileNotFoundException $e) {
                $this->mimeType = (string) $this->mimeService->mimeFromExtension($this->getFileExtension());
            }
        }

        return $this->mimeType;
    }

    /**
     * Get the type of the file.
     *
     * @return \Concrete\Core\File\Type\Type
     */
    public function getFileType()
    {
        if ($this->fileType === null) {
            $this->fileType = FileTypeList::getType($this->getFileExtension());
        }

        return $this->fileType;
    }

    /**
     * Set custom data for inter-processors communication.
     *
     * @param string $key the key of the value to be stored
     * @param mixed $value the value to be stored
     *
     * @return $this
     */
    public function setCustomData($key, $value)
    {
        $this->customData[$key] = $value;

        return $this;
    }

    /**
     * Load the image from the local file.
     *
     * @return \Imagine\Image\ImageInterface|null return NULL if it's not an image or if we couldn't load the image
     */
    public function getImage()
    {
        if ($this->image === null) {
            $this->image = false;
            try {
                if ($this->getFileType()->getGenericType() === FileType::T_IMAGE && !$this->getFileType()->isSVG()) {
                    $this->image = $this->imagine->open($this->getLocalFilename());
                }
            } catch (Exception $x) {
            }
        }

        return $this->image ?: null;
    }

    /**
     * Save the loaded image to the local file.
     */
    public function saveImage()
    {
        $imageType = $this->bitmapFormat->getFormatFromMimeType($this->getMimeType(), BitmapFormat::FORMAT_PNG);
        $imageOptions = $this->bitmapFormat->getFormatImagineSaveOptions($imageType);
        if ($imageType === BitmapFormat::FORMAT_GIF && $this->image->layers()->count() > 1) {
            $imageOptions['animated'] = true;
        }
        $this->image->save($this->getLocalFilename(), $imageOptions);
    }

    /**
     * Unload the loaded Image instance.
     */
    public function releaseImage()
    {
        if ($this->image) {
            $this->image = null;
            gc_collect_cycles();
        } elseif ($this->image === false) {
            $this->image = null;
        }
    }

    /**
     * Get custom data for inter-processors communication.
     *
     * @param string $key the key of the value to be retrieved
     * @param mixed $onNotFound what to return if there's no custom data identified by $key
     *
     * @return mixed
     */
    public function getCustomData($key, $onNotFound = null)
    {
        return array_key_exists($key, $this->customData) ? $this->customData[$key] : $onNotFound;
    }

    /**
     * Remove a value stored in the custom data.
     *
     * @param string $key the key of the value to be removed
     *
     * @return $this
     */
    public function resetCustomData($key)
    {
        unset($this->customData[$key]);

        return $this;
    }
}
