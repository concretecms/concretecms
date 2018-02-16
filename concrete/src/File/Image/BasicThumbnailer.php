<?php
namespace Concrete\Core\File\Image;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Image\Thumbnail\ThumbnailerInterface;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Exception;
use Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;

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
            $orm = $this->app['database/orm']->entityManager();
            /* @var \Doctrine\ORM\EntityManagerInterface $orm */
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
            $this->jpegCompression = $this->app->make(BitmapFormat::class)->getDefaultJpegQuality();
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
            $this->pngCompression = $this->app->make(BitmapFormat::class)->getDefaultPngCompressionLevel();
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
        $thumbnailsFormat = strtolower(trim((string) $thumbnailsFormat));
        if ($thumbnailsFormat !== ThumbnailFormatService::FORMAT_AUTO) {
            if (!$this->app->make(BitmapFormat::class)->isFormatValid($thumbnailsFormat)) {
                $thumbnailsFormat = BitmapFormat::FORMAT_JPEG;
            }
        }
        $this->thumbnailsFormat = $thumbnailsFormat;

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
        switch ($format) {
            case ThumbnailFormatService::FORMAT_AUTO:
                $format = $this->app->make(ThumbnailFormatService::class)->getAutomaticFormatForFile($savePath);
                break;
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
     * Checks thumbnail resolver for filename, schedule for creation via ajax if necessary.
     *
     * @param File|string $obj file instance of path to a file
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param bool $crop
     *
     * @return \stdClass
     */
    private function returnThumbnailObjectFromResolver($obj, $maxWidth, $maxHeight, $crop = false)
    {
        return $this->processThumbnail(true, $obj, $maxWidth, $maxHeight, $crop);
    }

    /**
     * Checks filesystem for thumbnail and if file doesn't exist will create it immediately.
     * concrete5's default behavior from the beginning up to 8.1.
     *
     * @param File|string $obj file instance of path to a file
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param bool $crop
     *
     * @return \stdClass
     */
    private function checkForThumbnailAndCreateIfNecessary($obj, $maxWidth, $maxHeight, $crop = false)
    {
        return $this->processThumbnail(false, $obj, $maxWidth, $maxHeight, $crop);
    }

    /**
     * @param bool $async
     * @param File|string $obj
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param bool $crop
     *
     * @return \stdClass
     */
    private function processThumbnail($async, $obj, $maxWidth, $maxHeight, $crop)
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
        if ($async) {
            $assetGroup = ResponseAssetGroup::get();
            $assetGroup->requireAsset('core/frontend/thumbnail-builder');
        }
        $baseFilename = '';
        $extension = '';
        if ($obj instanceof File) {
            try {
                $fr = $obj->getFileResource();
                $fID = $obj->getFileID();
                $extension = $fh->getExtension($fr->getPath());
                $baseFilename = md5(implode(':', [$fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp()]));
            } catch (Exception $e) {
                $result = new \stdClass();
                $result->src = '';

                return $result;
            }
        } else {
            $extension = $fh->getExtension($obj);
            // We hide the warning from filemtime() because it will only throw the warning on remote files, and we
            // don't care too much about that
            $baseFilename = md5(implode(':', [$obj, $maxWidth, $maxHeight, $crop, @filemtime($obj)]));
        }
        $thumbnailFormat = $this->getThumbnailsFormat();
        if ($thumbnailFormat === ThumbnailFormatService::FORMAT_AUTO) {
            $thumbnailFormat = $this->app->make(ThumbnailFormatService::class)->getAutomaticFormatForFileExtension($extension);
        }
        $thumbnailExtension = $this->app->make(BitmapFormat::class)->getFormatFileExtension($thumbnailFormat);

        $filename = $baseFilename . '.' . $thumbnailExtension;

        $abspath = '/cache/thumbnails/' . $filename;

        if ($async && $obj instanceof File) {
            $customThumb = new CustomThumbnail($maxWidth, $maxHeight, $abspath, $crop);

            $path_resolver = $this->app->make('Concrete\Core\File\Image\Thumbnail\Path\Resolver');
            $path_resolver->getPath($obj->getVersion(), $customThumb);
        } else {
            if (!$filesystem->has($abspath)) {
                $created = false;
                try {
                    if ($obj instanceof File) {
                        $image = !is_callable([$fr, 'exists']) || $fr->exists() ? \Image::load($fr->read()) : null;
                    } else {
                        $image = \Image::open($obj);
                    }
                    if ($image) {
                        $this->create(
                            $image,
                            $abspath,
                            $maxWidth,
                            $maxHeight,
                            $crop,
                            $thumbnailFormat
                        );
                        $created = true;
                    }
                } catch (\Exception $e) {
                }
                if ($created === false) {
                    $result = new \stdClass();
                    $result->src = '';

                    return $result;
                }
            }
        }

        $thumb = new \stdClass();
        $thumb->src = $configuration->getPublicURLToFile($abspath);

        // this is a hack, but we shouldn't go out on the network if we don't have to. We should probably
        // add a method to the configuration to handle this. The file storage locations should be able to handle
        // thumbnails.
        if ($configuration instanceof LocalConfiguration) {
            $dimensionsPath = $configuration->getRootPath() . $abspath;
        } else {
            $dimensionsPath = $thumb->src;
        }

        try {
            $dimensions = @getimagesize($dimensionsPath);
        } catch (Exception $e) {
            $dimensions = false;
        }
        $thumb->width = ($dimensions === false) ? null : $dimensions[0];
        $thumb->height = ($dimensions === false) ? null : $dimensions[1];

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
