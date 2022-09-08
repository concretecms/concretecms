<?php

declare(strict_types = 1);

namespace Concrete\Core\File\Upload;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\Utility\Service\Number;

defined('C5_EXECUTE') or die('Access Denied.');

abstract class ClientSideUploader
{
    protected const CONFIGKEY_PARALLELUPLOADS = 'concrete.upload.parallel';
    protected const CONFIGKEY_CHUNK_ENABLED = 'concrete.upload.chunking.enabled';
    protected const CONFIGKEY_CHUNK_SIZE = 'concrete.upload.chunking.chunkSize';

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\File\Image\BitmapFormat
     */
    protected $bitmapFormat;

    /**
     * @var \Concrete\Core\Utility\Service\Number
     */
    protected $numberService;

    public function __construct(Repository $config, BitmapFormat $bitmapFormat, Number $numberService)
    {
        $this->config = $config;
        $this->bitmapFormat = $bitmapFormat;
        $this->numberService = $numberService;
    }

    /**
     * Can big images be resized on the client side before they are uploaded to the server?
     */
    public abstract function supportClientSizeImageResizing(): bool;

    /**
     * Get the maximum time (in seconds) before PHP aborts uploads.
     *
     * @return int
     */
    public function getTimeout(): int
    {
        $raw = ini_get('max_execution_time');
        $maxExecutionTime = is_numeric($raw) ? (int) $raw : 30;
        if ($maxExecutionTime <= 0) {
            // Forever: let's assume 1 full day
            $maxExecutionTime = 24 * 60 * 60;
        }
        $raw = ini_get('max_input_time');
        $maxInputTime = is_numeric($raw) ? (int) $raw : -1;
        if ($maxInputTime === 0) {
            // Forever: let's assume 1 full day
            $maxInputTime = 24 * 60 * 60;
        } elseif ($maxInputTime < 0) {
            //  max_execution_time is used instead
            $maxInputTime = 0;
        }

        return $maxExecutionTime + $maxInputTime;
    }

    /**
     * Get the number of parallel uploads.
     *
     * @return int returns 1 for no parallel uploads
     */
    public function getParallelUploads(): int
    {
        $raw = $this->config->get(static::CONFIGKEY_PARALLELUPLOADS);
        if (is_numeric($raw)) {
            $int = (int) $raw;
            if ($int > 0) {
                return $int;
            }
        }
        return 1;
    }

    /**
     * Set the number of parallel uploads.
     *
     * @param int $value set to 1 for no parallel uploads.
     *
     * @return $this
     */
    public function setParallelUploads(int $value): self
    {
        if ($value <= 0) {
            $value = 1;
        }
        $this->config->set(static::CONFIGKEY_PARALLELUPLOADS, $value);
        $this->config->save(static::CONFIGKEY_PARALLELUPLOADS, $value);

        return $this;
    }

    /**
     * Should the client send files in chunks if they exceed a configured size?
     */
    public function isChunkingEnabled(): bool
    {
        return (bool) $this->config->get(static::CONFIGKEY_CHUNK_ENABLED);
    }

    /**
     * Should the client send files in chunks if they exceed a configured size?
     *
     * @return $this
     */
    public function setChunkingEnabled(bool $value): self
    {
        $this->config->set(static::CONFIGKEY_CHUNK_ENABLED, $value);
        $this->config->save(static::CONFIGKEY_CHUNK_ENABLED, $value);

        return $this;
    }

    /**
     * Get the size of the uploaded file chunks (in bytes), by considering both the configured value and the PHP settings.
     */
    public function getChunkSize(): int
    {
        return $this->getConfiguredChunkSize() ?? $this->getPHPMaxFileSize() ?? 2097152; // 2MB by default
    }

    /**
     * Get the configured size (in bytes) of the uploaded file chunks.
     *
     * @return int|null return NULL if it's not configured
     */
    public function getConfiguredChunkSize(): ?int
    {
        $chunkSize = $this->config->get(static::CONFIGKEY_CHUNK_SIZE);
        if (!is_numeric($chunkSize)) {
            return null;
        }
        $chunkSize = (int) $chunkSize;

        return $chunkSize > 0 ? $chunkSize : null;
    }

    /**
     * Set the configured size (in bytes) of the uploaded file chunks.
     *
     * @return $this
     */
    public function setConfiguredChunkSize(?int $value): self
    {
        if ($value !== null && $value <= 0) {
            $value = null;
        }
        $this->config->set(static::CONFIGKEY_CHUNK_SIZE, $value);
        $this->config->save(static::CONFIGKEY_CHUNK_SIZE, $value);

        return $this;
    }

    /**
     * Get the maximim size (in bytes) allowed by PHP for a single uploaded file.
     *
     * @return int|null return NULL if there's no limit
     */
    public function getPHPMaxFileSize(): ?int
    {
        // Maximum size of an uploaded file (default: 2M)
        $raw = $this->numberService->getBytes((string) ini_get('upload_max_filesize'));
        $uploadMaxFilesize = is_numeric($raw) ? (int) $raw : 2097152;
        if ($uploadMaxFilesize > 0) {
            // Let's substract a small value (just in case)
            $uploadMaxFilesize = max(1000, $uploadMaxFilesize - 100);
        }
        // Max size of post data allowed (default: 8M)
        $raw = $this->numberService->getBytes((string) ini_get('post_max_size'));
        $postMaxSize = is_numeric($raw) ? (int) $raw : 8388608;
        if ($postMaxSize > 0) {
            // Let's substract something to consider other posted fields.
            $postMaxSize = max(1000, $postMaxSize - 10000);
        }
        if ($uploadMaxFilesize <= 0) {
            if ($postMaxSize <= 0) {
                return null;
            }
            return $postMaxSize;
        }
        if ($postMaxSize <= 0) {
            return $uploadMaxFilesize;
        }

        return min($uploadMaxFilesize, $postMaxSize);
    }
}
