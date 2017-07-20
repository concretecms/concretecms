<?php
namespace Concrete\Core\File\Image;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Image\Thumbnail\ThumbnailerInterface;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\StorageLocation\Configuration\DefaultConfiguration;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class BasicThumbnailer implements ThumbnailerInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * The currently configured JPEG compression level.
     *
     * @var int|null
     */
    protected $jpegCompression = null;

    /**
     * The currently configured PNG compression level.
     *
     * @var int|null
     */
    protected $pngCompression = null;

    /**
     * The currently configured format of the generated thumbnails.
     *
     * @var string|null
     */
    protected $thumbnailsFormat = null;

    /**
     * @var StorageLocationInterface
     */
    private $storageLocation;

    public function __construct(StorageLocationInterface $storageLocation = null)
    {
        $this->storageLocation = $storageLocation;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getStorageLocation()
     */
    public function getStorageLocation()
    {
        if ($this->storageLocation === null) {
            /** @var EntityManagerInterface $orm */
            $orm = $this->app['database/orm']->entityManager();
            $storageLocation = $orm->getRepository(StorageLocation::class)->findOneBy(['fslIsDefault' => true]);

            if ($storageLocation) {
                $this->storageLocation = $storageLocation;
            }
        }

        return $this->storageLocation;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setStorageLocation()
     */
    public function setStorageLocation(StorageLocationInterface $storageLocation)
    {
        $this->storageLocation = $storageLocation;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setJpegCompression()
     */
    public function setJpegCompression($level)
    {
        if (is_int($level) || is_float($level) || (is_string($level) && is_numeric($level))) {
            $this->jpegCompression = min(max((int) $level, 0), 100);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getJpegCompression()
     */
    public function getJpegCompression()
    {
        if ($this->jpegCompression === null) {
            $this->jpegCompression = (int) $this->app->make('config')->get('concrete.misc.default_jpeg_image_compression');
        }

        return $this->jpegCompression;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setPngCompression()
     */
    public function setPngCompression($level)
    {
        if (is_int($level) || is_float($level) || (is_string($level) && is_numeric($level))) {
            $this->pngCompression = min(max((int) $level, 0), 9);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getPngCompression()
     */
    public function getPngCompression()
    {
        if ($this->pngCompression === null) {
            $this->pngCompression = (int) $this->app->make('config')->get('concrete.misc.default_png_image_compression');
        }

        return $this->pngCompression;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setThumbnailsFormat()
     */
    public function setThumbnailsFormat($thumbnailsFormat)
    {
        $thumbnailsFormat = $thumbnailsFormat ? strtolower(trim((string) $thumbnailsFormat)) : '';
        if ($thumbnailsFormat === 'jpg') {
            $thumbnailsFormat = 'jpeg';
        }
        $this->thumbnailsFormat = in_array($thumbnailsFormat, ['jpeg', 'png', 'auto']) ? $thumbnailsFormat : 'jpeg';

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getThumbnailsFormat()
     */
    public function getThumbnailsFormat()
    {
        if ($this->thumbnailsFormat === null) {
            $this->setThumbnailsFormat($this->app->make('config')->get('concrete.misc.default_thumbnail_format'));
        }

        return $this->thumbnailsFormat;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::create()
     */
    public function create($mixed, $savePath, $width, $height, $fit = false, $format = false)
    {
        if ($format === false) {
            $format = $this->getThumbnailsFormat();
        }
        if ($format === 'auto') {
            if (preg_match('/\.jpe?g($|\?)/i', $savePath)) {
                $format = 'jpeg';
            } else {
                $format = 'png';
            }
        }
        $thumbnailOptions = [
            'jpeg_quality' => $this->getJpegCompression(),
            'png_compression_level' => $this->getPngCompression(),
        ];
        $filesystem = $this->getStorageLocation()->getFileSystemObject();

        if ($mixed instanceof ImageInterface) {
            $image = $mixed;
        } else {
            $image = Image::open($mixed);
        }
        if ($fit) {
            $thumb = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND);
            $filesystem->write(
                $savePath,
                $thumb->get($format, $thumbnailOptions)
            );
        } else {
            if ($height < 1) {
                $thumb = $image->thumbnail($image->getSize()->widen($width));
            } elseif ($width < 1) {
                $thumb = $image->thumbnail($image->getSize()->heighten($height));
            } else {
                $thumb = $image->thumbnail(new Box($width, $height));
            }
            $filesystem->write(
                $savePath,
                $thumb->get($format, $thumbnailOptions)
            );
        }
    }

    /**
     * Checks filesystem for thumbnail and if file doesn't exist will create it immediately.
     * concrete5's default behavior from the beginning up to 8.1.
     * @deprecated
     * @param $obj
     * @param $maxWidth
     * @param $maxHeight
     * @param bool $crop
     * @return \stdClass
     */
    private function checkForThumbnailAndCreateIfNecessary($obj, $maxWidth, $maxHeight, $crop = false)
    {
        $storage = $obj->getFileStorageLocationObject();
        $this->setStorageLocation($storage);
        $filesystem = $storage->getFileSystemObject();
        $configuration = $storage->getConfigurationObject();
        $version = null;

        $fh = \Core::make('helper/file');
        if ($obj instanceof File) {
            try {
                $fr = $obj->getFileResource();
                $fID = $obj->getFileID();
                $filename = md5(implode(':', array($fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp())))
                    . '.' . $fh->getExtension($fr->getPath());
            } catch (\Exception $e) {
                $filename = '';
            }
        } else {
            $filename = md5(implode(':', array($obj, $maxWidth, $maxHeight, $crop, filemtime($obj))))
                . '.' . $fh->getExtension($obj);
        }

        $abspath = '/cache/' . $filename;

        $src = $configuration->getPublicURLToFile($abspath);

        /** Attempt to create the image */
        if (!$filesystem->has($abspath)) {
            if ($obj instanceof File && $fr->exists()) {
                $image = \Image::load($fr->read());
            } else {
                $image = \Image::open($obj);
            }
            // create image there
            $this->create($image,
                $abspath,
                $maxWidth,
                $maxHeight,
                $crop);
        }

        $thumb = new \stdClass();
        $thumb->src = $src;

        // this is a hack, but we shouldn't go out on the network if we don't have to. We should probably
        // add a method to the configuration to handle this. The file storage locations should be able to handle
        // thumbnails.
        if ($configuration instanceof DefaultConfiguration) {
            $dimensionsPath = $configuration->getRootPath() . $abspath;
        } else {
            $dimensionsPath = $src;
        }

        try {
            //try and get it locally, otherwise use http
            $dimensions = getimagesize($dimensionsPath);
            $thumb->width = $dimensions[0];
            $thumb->height = $dimensions[1];
        } catch (\Exception $e) {

        }

        return $thumb;
    }

    /**
     * Checks thumbnail resolver for filename, schedule for creation via ajax if necessary.
     * @param $obj
     * @param $maxWidth
     * @param $maxHeight
     * @param bool $crop
     */
    private function returnThumbnailObjectFromResolver($obj, $maxWidth, $maxHeight, $crop = false)
    {
        if ($obj instanceof File) {
            $storage = $obj->getFileStorageLocationObject();
        } else {
            $storage = $this->getStorageLocation();
        }
        $this->setStorageLocation($storage);
        $filesystem = $storage->getFileSystemObject();
        $configuration = $storage->getConfigurationObject();
        $version = null;

        $fh = $this->app->make('helper/file');
        $assetGroup = ResponseAssetGroup::get();
        $assetGroup->requireAsset('core/frontend/thumbnail-builder');

        $baseFilename = '';
        $extension = '';
        if ($obj instanceof File) {
            try {
                $fr = $obj->getFileResource();
                $fID = $obj->getFileID();
                $extension = $fh->getExtension($fr->getPath());
                $baseFilename = md5(implode(':', [$fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp()]));
            } catch (Exception $e) {
            }
        } else {
            $extension = $fh->getExtension($obj);
            // We hide the warning from filemtime() because it will only throw the warning on remote files, and we
            // don't care too much about that
            $baseFilename = md5(implode(':', [$obj, $maxWidth, $maxHeight, $crop, @filemtime($obj)]));
        }

        $thumbnailsFormat = $this->getThumbnailsFormat();
        switch ($thumbnailsFormat) {
            case 'jpeg':
                $extension = 'jpg';
                break;
            case 'png':
                $extension = 'png';
                break;
            case 'auto':
                switch (strtolower($extension)) {
                    case 'jpeg':
                    case 'jpg':
                    case 'pjpeg':
                        $extension = 'jpg';
                        $thumbnailsFormat = 'jpeg';
                        break;
                    default:
                        $extension = 'png';
                        $thumbnailsFormat = 'png';
                        break;
                }
                break;
        }
        $filename = '';
        if ($baseFilename !== '') {
            $filename = $baseFilename . '.' . $extension;
        }

        $abspath = '/cache/thumbnails/' . $filename;

        if ($obj instanceof File) {
            $customThumb = new CustomThumbnail($maxWidth, $maxHeight, $abspath, $crop);

            $path_resolver = $this->app->make('Concrete\Core\File\Image\Thumbnail\Path\Resolver');
            $path_resolver->getPath($obj->getVersion(), $customThumb);
        } else { // @TODO This is a path or url and doesn't have a file object, so we just make the thumbnail now...
            if (!$filesystem->has($abspath)) {
                try {
                    $image = \Image::open($obj);
                    // create image there
                    $this->create($image,
                        $abspath,
                        $maxWidth,
                        $maxHeight,
                        $crop,
                        $thumbnailsFormat);
                } catch (\Exception $e) {
                    $abspath = false;
                }
            }
        }

        $src = '';
        if ($abspath) {
            $src = $configuration->getPublicURLToFile($abspath);
        }

        $thumb = new \stdClass();
        $thumb->src = $src;

        // this is a hack, but we shouldn't go out on the network if we don't have to. We should probably
        // add a method to the configuration to handle this. The file storage locations should be able to handle
        // thumbnails.
        if ($configuration instanceof DefaultConfiguration) {
            $dimensionsPath = $configuration->getRootPath() . $abspath;
        } else {
            $dimensionsPath = $src;
        }

        try {
            $dimensions = @getimagesize($dimensionsPath);
        } catch (Exception $e) {
            $dimensions = false;
        }
        $thumb->width = ($dimensions === false) ? null : $dimensions[0];
        $thumb->height = ($dimensions === false) ?: $dimensions[1];

        return $thumb;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getThumbnail()
     */
    public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false)
    {
        $config = $this->app->make('config');
        if ($config->get('concrete.misc.basic_thumbnailer_generation_strategy') == 'async') {
            return $this->returnThumbnailObjectFromResolver($obj, $maxWidth, $maxHeight, $crop);
        } else {
            return $this->checkForThumbnailAndCreateIfNecessary($obj, $maxWidth, $maxHeight, $crop);
        }
    }

    /**
     * @deprecated
     */
    public function outputThumbnail($mixed, $maxWidth, $maxHeight, $alt = null, $return = false, $crop = false)
    {
        $thumb = $this->getThumbnail($mixed, $maxWidth, $maxHeight, $crop);
        $html = '<img class="ccm-output-thumbnail" alt="' . $alt . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }
}
