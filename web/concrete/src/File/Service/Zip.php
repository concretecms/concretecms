<?php
namespace Concrete\Core\File\Service;

use Illuminate\Filesystem\Filesystem;
use Exception;
use ZipArchive;
use DateTime;

/**
 * Wrapper for ZIP functions.
 */
class Zip
{
    /**
     * The Filesystem instance to use.
     *
     * @var Filesystem
     */
    protected $filesystem = null;

    /**
     * Set the Filesystem instance to use.
     *
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get the Filesystem instance to use.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        if ($this->filesystem === null) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * Can we try to use native commands?
     *
     * @var bool
     */
    protected $enableNativeCommands = true;

    /**
     * State that we can try to use native commands.
     */
    public function enableNativeCommands()
    {
        $this->enableNativeCommands = true;
    }

    /**
     * State that we can NOT try to use native commands.
     */
    public function disableNativeCommands()
    {
        $this->enableNativeCommands = false;
    }

    /**
     * Can we try to use native commands?
     *
     * @return bool
     */
    public function nativeCommandsEnabled()
    {
        return $this->enableNativeCommands;
    }

    /**
     * Cache for the available native commands.
     *
     * @var array
     */
    protected $availableNativeCommands = array();

    /**
     * Check if a native command is available.
     *
     * @param string $command
     *
     * @return bool
     */
    public function isNativeCommandAvailable($command)
    {
        switch ($command) {
            case 'zip':
            case 'unzip':
                break;
            default:
                return false;
        }
        if (!isset($this->availableNativeCommands[$command])) {
            $this->availableNativeCommands[$command] = false;
            $safeMode = @ini_get('safe_mode');
            if (empty($safeMode)) {
                if (function_exists('exec')) {
                    $disabledCommands = array_map('trim', explode(',', strtolower((string) @ini_get('disable_functions'))));
                    if (!in_array('exec', $disabledCommands, true)) {
                        $rc = 1;
                        $output = array();
                        @exec($command.' -v 2>&1', $output, $rc);
                        if ($rc === 0) {
                            $stdOut = implode("\n", $output);
                            if (stripos($stdOut, 'info-zip') !== false || stripos($stdOut, 'infozip') !== false) {
                                $this->availableNativeCommands[$command] = true;
                            }
                        }
                    }
                }
            }
        }

        return $this->availableNativeCommands[$command];
    }

    /**
     * Check if a native command is available and if we may use it.
     *
     * @param string $command
     *
     * @return bool
     */
    protected function mayUseNativeCommand($command)
    {
        return $this->enableNativeCommands ? $this->isNativeCommandAvailable($command) : false;
    }

    /**
     * Decompress a ZIP archive to a directory.
     *
     * @param string $zipFile The source ZIP archive.
     * @param string $destinationDirectory The destination folder.
     * @param array $options {
     *   @var bool $skipCheck Skip test compressed archive data
     * }
     *
     * @throws Exception
     */
    public function unzip($zipFile, $destinationDirectory, array $options = array())
    {
        $fs = $this->getFilesystem();
        $normalized = @realpath($zipFile);
        if ($normalized === false || !$fs->isFile($normalized)) {
            throw new Exception(t('Unable to find the ZIP file %s', $zipFile));
        }
        $zipFile = $normalized;
        $normalized = @realpath($destinationDirectory);
        if ($normalized === false || !$fs->isDirectory($normalized)) {
            throw new Exception(t('Unable to find the directory %s', $destinationDirectory));
        }
        if (!$fs->isWritable($normalized)) {
            throw new Exception(t('The directory "%s" is not writable', $destinationDirectory));
        }
        $destinationDirectory = $normalized;
        $options += array(
            'skipCheck' => false,
        );
        if ($this->mayUseNativeCommand('unzip')) {
            $this->unzipNative($zipFile, $destinationDirectory, $options);
        } else {
            $this->unzipPHP($zipFile, $destinationDirectory, $options);
        }
    }

    /**
     * Compress the contents of a directory to a ZIP archive.
     *
     * @param string $sourceDirectory The directory to compress.
     * @param string $zipFile The ZIP file to create (it will be deleted if already existing, unless the 'append' option is set to true).
     * @param array $options {
     *   @var bool $includeDotFiles Shall the zip file include files and folders whose name starts with a dot?
     *   @var bool $skipCheck Skip test compressed archive data
     *   @var int $level Compression level (0 to 9)
     *   @var bool $append Append to an existing archive instead of overwriting it?
     * }
     *
     * @throws Exception
     */
    public function zip($sourceDirectory, $zipFile, array $options = array())
    {
        $fs = $this->getFilesystem();
        $normalized = @realpath($sourceDirectory);
        if ($normalized === false || !$fs->isDirectory($normalized)) {
            throw new Exception(t('Unable to find the directory %s', $destinationDirectory));
        }
        $sourceDirectory = $normalized;
        $zipFile = str_replace('/', DIRECTORY_SEPARATOR, $zipFile);
        if ($fs->exists($zipFile)) {
            if ($fs->isDirectory($zipFile)) {
                throw new Exception(t('The specified path of the ZIP archive (%s) is a directory, not a file', $zipFile));
            }
            $options['append'] = isset($options['append']) ? (bool) $options['append'] : false;
            if (!$options['append']) {
                if (@$fs->delete(array($zipFile)) === false) {
                    throw new Exception(t('Failed to delete file %s', $zipFile));
                }
            }
        } else {
            $options['append'] = false;
        }
        $options += array(
            'includeDotFiles' => false,
            'skipCheck' => false,
            'level' => 9,
        );
        if ($this->mayUseNativeCommand('zip')) {
            $this->zipNative($sourceDirectory, $zipFile, $options);
        } else {
            $this->zipPHP($sourceDirectory, $zipFile, $options);
        }
    }

    /**
     * List the contents of a ZIP archive.
     *
     * @param string $zipFile The ZIP file to inspect.
     * @param array $options {
     *   @var bool $skipCheck Skip test compressed archive data
     *   @var bool $excludeDirs Don't include directories
     *   @var bool $excludeFiles Don't include files
     * }
     *
     * @throws Exception
     *
     * @return array The keys of the resulting array contain the paths to the items, and the values will be arrays, whose keys are:
     * - string 'type' For directories it will be 'D', for files it will be 'F'
     * - \DateTime 'date' Last modification date/time
     * - int 'originalSize' (only for files) Uncompressed size of the file
     * - int 'compressedSize' (only for files) Compressed size of the file
     */
    public function listContents($zipFile, array $options = array())
    {
        $fs = $this->getFilesystem();
        if (!$fs->isFile($zipFile)) {
            throw new Exception(t('Unable to find the ZIP file %s', $zipFile));
        }
        $options += array(
            'skipCheck' => false,
            'excludeDirs' => false,
            'excludeFiles' => false,
        );
        $result = array();
        $zip = new ZipArchive();
        try {
            $flags = 0;
            if (!$options['skipCheck']) {
                $flags |= ZipArchive::CHECKCONS;
            }
            $zipErr = @$zip->open($zipFile, $flags);
            if ($zipErr !== true) {
                throw new Exception($this->describeZipArchiveError($zip, $zipErr));
            }
            for ($index = 0; $index < $zip->numFiles; ++$index) {
                $stat = @$zip->statIndex($index);
                if ($stat === false) {
                    throw new Exception(t('Failed to retrieve the details of a ZIP archive entry.'));
                }
                $isDir = substr($stat['name'], -1) === '/' || substr($stat['name'], -1) === '\\';
                if ($isDir) {
                    if ($options['excludeDirs']) {
                        continue;
                    }
                } else {
                    if ($options['excludeFiles']) {
                        continue;
                    }
                }
                $item = array(
                    'type' => $isDir ? 'D' : 'F',
                    'date' => (isset($stat['mtime']) && $stat['mtime']) ? DateTime::createFromFormat('U', $stat['mtime']) : null,
                );
                if (!$isDir) {
                    $item += array(
                        'originalSize' => isset($stat['size']) ? (int) $stat['size'] : null,
                        'compressedSize' => isset($stat['comp_size']) ? (int) $stat['comp_size'] : null,
                    );
                }
                $result[trim($stat['name'], '/\\')] = $item;
            }
            @$zip->close();
            $zip = null;
        } catch (Exception $x) {
            if ($zip !== null) {
                try {
                    @$zip->close();
                } catch (\Exception $foo) {
                }
                $zip = null;
            }
            throw $x;
        }

        return $result;
    }

    /**
     * Describe a ZipArchive related problem.
     *
     * @param ZipArchive $zip
     * @param int $errorCode
     */
    protected function describeZipArchiveError(ZipArchive $zip, $errorCode)
    {
        $result = '';
        switch ($errorCode) {
            case ZipArchive::ER_OK:
                break;
            case ZipArchive::ER_MULTIDISK:
                $result = t('Multi-disk ZIP archives are not supported.');
                break;
            case ZipArchive::ER_RENAME:
                $result = t('Renaming a temporary file failed working with a ZIP archive.');
                break;
            case ZipArchive::ER_CLOSE:
                $result = t('Closing ZIP archive failed.');
                break;
            case ZipArchive::ER_SEEK:
                $result = t('Seek error working with a ZIP archive.');
                break;
            case ZipArchive::ER_READ:
                $result = t('Error reading a file working with a ZIP archive.');
                break;
            case ZipArchive::ER_WRITE:
                $result = t('Error writing a file working with a ZIP archive.');
                break;
            case ZipArchive::ER_CRC:
                $result = t('CRC error working with a ZIP archive.');
                break;
            case ZipArchive::ER_ZIPCLOSED:
                $result = t('ZIP archive was closed.');
                break;
            case ZipArchive::ER_NOENT:
                $result = t('File not found working with a ZIP archive.');
                break;
            case ZipArchive::ER_EXISTS:
                $result = t('File already exists working with a ZIP archive.');
                break;
            case ZipArchive::ER_OPEN:
                $result = t('Failed to open a file working with a ZIP archive.');
                break;
            case ZipArchive::ER_TMPOPEN:
                $result = t('Failed to open a temporary file working with a ZIP archive.');
                break;
            case ZipArchive::ER_ZLIB:
                $result = t('ZLIB error working with a ZIP archive.');
                break;
            case ZipArchive::ER_MEMORY:
                $result = t('Out of memory problems working with a ZIP archive.');
                break;
            case ZipArchive::ER_CHANGED:
                $result = t('Entry has been changed working with a ZIP archive.');
                break;
            case ZipArchive::ER_COMPNOTSUPP:
                $result = t('Compression method not supported working with a ZIP archive.');
                break;
            case ZipArchive::ER_EOF:
                $result = t('Premature end of file working with a ZIP archive.');
                break;
            case ZipArchive::ER_INVAL:
                $result = t('Invalid argument working with a ZIP archive.');
                break;
            case ZipArchive::ER_NOZIP:
                $result = t('Not a ZIP archive.');
                break;
            case ZipArchive::ER_INTERNAL:
                $result = t('Internal error working with a ZIP archive.');
                break;
            case ZipArchive::ER_INCONS:
                $result = t('ZIP archive is inconsistent.');
                break;
            case ZipArchive::ER_REMOVE:
                $result = t('Can\'t remove file working with a ZIP archive.');
                break;
            case ZipArchive::ER_DELETED:
                $result = t('Entry has been deleted working with a ZIP archive.');
                break;
            default:
                $result = t('Unknown ZIP-related problem (code: %s).', $errorCode);
                break;
        }
        $status = @$zip->getStatusString();
        if (is_string($status) && $status !== '') {
            if ($result === '') {
                $result = $status;
            } else {
                $result .= "\n".$status;
            }
        }

        if ($result === '') {
            $result = t('Unknown ZIP-related problem');
        }

        return $result;
    }

    /**
     * Decompress a ZIP archive to a directory using the native 'unzip' command.
     *
     * @param string $zipFile
     * @param string $destinationDirectory
     * @param array $options
     *
     * @throws Exception
     */
    protected function unzipNative($zipFile, $destinationDirectory, array $options)
    {
        $cmd = 'unzip';
        $cmd .= ' -o'; // overwrite files WITHOUT prompting
        $cmd .= ' -q'; // quiet mode, to avoid overflow of stdout
        $cmd .= ' '.escapeshellarg($zipFile); // file to extract
        $cmd .= ' -d '.escapeshellarg($destinationDirectory); // destination directory
        $rc = 1;
        $output = array();
        @exec($cmd.' 2>&1', $output, $rc);
        if ($rc !== 0) {
            $error = trim(implode("\n", $output)) ?: t('Unknown error decompressing a ZIP archive');
            throw new Exception($error);
        }
    }

    /**
     * Decompress a ZIP archive to a directory using the PHP functions.
     *
     * @param string $zipFile
     * @param string $destinationDirectory
     * @param array $options
     *
     * @throws Exception
     */
    protected function unzipPHP($zipFile, $destinationDirectory, array $options)
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('Unable to unzip files using ZipArchive. Please ensure the Zip extension is installed.');
        }
        $zip = new ZipArchive();
        try {
            $flags = 0;
            if (!$options['skipCheck']) {
                $flags |= ZipArchive::CHECKCONS;
            }
            $zipErr = @$zip->open($zipFile, $flags);
            if ($zipErr !== true) {
                throw new Exception($this->describeZipArchiveError($zip, $zipErr));
            }
            if (@$zip->extractTo($destinationDirectory) !== true) {
                throw new Exception($this->describeZipArchiveError($zip, ZipArchive::ER_OK));
            }
            @$zip->close();
            $zip = null;
        } catch (Exception $x) {
            if ($zip !== null) {
                try {
                    @$zip->close();
                } catch (\Exception $foo) {
                }
                $zip = null;
            }
            throw $x;
        }
    }

    /**
     * Compress the contents of a directory to a ZIP archive using the native 'zip' command.
     *
     * @param string $sourceDirectory
     * @param string $zipFile
     * @param array $options
     *
     * @throws Exception
     */
    protected function zipNative($sourceDirectory, $zipFile, array $options)
    {
        if (!$options['includeDotFiles'] && stripos(PHP_OS, 'WIN') === 0) {
            $this->zipPHP($sourceDirectory, $zipFile, $options);

            return;
        }
        $fs = $this->getFilesystem();
        $originalZipFile = $zipFile;
        $revertName = false;
        if (!strpos(basename($zipFile), '.')) {
            for ($i = 0; ; ++$i) {
                $tmp = "$zipFile-$i.zip";
                if (!$fs->exists($tmp)) {
                    $zipFile = $tmp;
                    break;
                }
            }
            if ($options['append']) {
                if (@$fs->move($originalZipFile, $zipFile) === false) {
                    throw new Exception(t('Failed to move a temporary file.'));
                }
                $revertName = true;
            }
        }
        try {
            $cmd = 'zip';
            $level = (isset($options['level']) && is_numeric($options['level'])) ? @intval($options['level']) : null;
            if ($level !== null && $level >= 0 && $level <= 9) {
                $cmd .= ' -'.$level;
            }
            $cmd .= ' -q'; // quiet mode, to avoid overflow of stdout
            $cmd .= ' -r'; // recurse into directories
            $cmd .= ' '.escapeshellarg($zipFile); // destination ZIP archive
            if ($options['includeDotFiles']) {
                $cmd .= ' .* *';
            } else {
                $cmd .= ' * -x \*/.\*';
            }
            $rc = 1;
            $output = array();
            $prevDir = @getcwd();
            if ($prevDir === false) {
                throw new Exception(t('Failed to determine current directory'));
            }
            if (@chdir($sourceDirectory) === false) {
                throw new Exception(t('Failed to enter directory '.$sourceDirectory));
            }
            @exec($cmd.' 2>&1', $output, $rc);
            @chdir($prevDir);
            if ($rc !== 0) {
                $error = trim(implode("\n", $output)) ?: t('Unknown error compressing a directory');
                throw new Exception($error);
            }
        } catch (Exception $x) {
            if ($fs->exists($zipFile)) {
                if ($revertName) {
                    @$fs->move($zipFile, $originalZipFile);
                } elseif (!$options['append']) {
                    @$fs->delete(array($zipFile));
                }
            }
            throw $x;
        }
        if ($originalZipFile !== $zipFile) {
            if (@$fs->move($zipFile, $originalZipFile) === false) {
                @$fs->delete(array($zipFile));
                throw new Exception(t('Failed to move a temporary file.'));
            }
        }
    }

    /**
     * Compress the contents of a directory to a ZIP archive using the PHP functions.
     *
     * @param string $sourceDirectory
     * @param string $zipFile
     * @param array $options
     *
     * @throws Exception
     */
    protected function zipPHP($sourceDirectory, $zipFile, array $options)
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('Unable to zip files using ZipArchive. Please ensure the Zip extension is installed.');
        }
        $zip = new ZipArchive();
        try {
            $flags = ZipArchive::CREATE;
            if (!$options['skipCheck']) {
                $flags |= ZipArchive::CHECKCONS;
            }
            $zipErr = @$zip->open($zipFile, $flags);
            if ($zipErr !== true) {
                throw new Exception($this->describeZipArchiveError($zip, $zipErr));
            }
            $skipPathLength = strlen(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $sourceDirectory), '/')) + 1;
            $contents = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDirectory),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($contents as $item) {
                switch ($item->getFilename()) {
                    case '.':
                    case '..':
                        break;
                    default:
                        $itemFullPath = $item->getRealPath();
                        $itemRelPath = substr(str_replace(DIRECTORY_SEPARATOR, '/', $itemFullPath), $skipPathLength);
                        if (
                            $options['includeDotFiles']
                            ||
                            (
                                strpos($itemRelPath, '.') !== 0
                                &&
                                strpos($itemRelPath, '/.') === false
                            )
                        ) {
                            if ($item->isDir()) {
                                $added = @$zip->addEmptyDir($itemRelPath);
                            } else {
                                $added = @$zip->addFile($itemFullPath, $itemRelPath);
                            }
                            if ($added !== true) {
                                throw new Exception($this->describeZipArchiveError($zip, ZipArchive::ER_OK));
                            }
                        }
                        break;
                }
            }
            if (@$zip->close() !== true) {
                throw new Exception($this->describeZipArchiveError($zip, ZipArchive::ER_OK));
            }
            $zip = null;
        } catch (Exception $x) {
            if ($zip !== null) {
                try {
                    @$zip->close();
                } catch (\Exception $foo) {
                }
                $zip = null;
            }
            @$this->getFilesystem()->delete(array($zipFile));
            throw $x;
        }
    }
}
