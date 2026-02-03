<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle;

defined('C5_EXECUTE') or die('Access Denied.');

final class FileFlag
{
    /**
     * PHP-only but non PSR-compliant files (the class name doesn't match the file name).
     *
     * @var int
     */
    public const MODIFIED_PSR = 0b1;

    /**
     * Entry-point PHP files (where the PHP version may not match the PHP version requirements).
     *
     * @var int
     */
    public const ENTRYPOINT = 0b10;

    /**
     * View files, where we can't fix the indentation.
     *
     * @var int
     */
    public const VIEW = 0b100;

    /**
     * HP-only PSR-compliant files defining Doctrine entities.
     *
     * @var int
     */
    public const DOCTRINE_ENTITY = 0b1000;

    /**
     * PHP-only PSR-compliant files defining Doctrine entities.
     *
     * @var string[]
     */
    private const RX_SRC_ENTITY = [
        '{/(application|concrete|packages/\w+)/src/Entity/}',
    ];

    /**
     * PHP-only PSR-compliant files.
     *
     * @var string[]
     */
    private const RX_SRC = [
        '{/(application|concrete|packages/\w+)/(config|routes|src)/}',
        '{/(application|concrete)/bootstrap/[^/]+$}',
        '{/(concrete|packages/\w+)/config/}',
        '{/(concrete|packages/\w+)/routes/}',
    ];

    /**
     * PHP-only but non PSR-compliant files (the class name doesn't match the file name).
     *
     * @var string[]
     */
    private const RX_MODIFIED_PSR = [
        '{/controller\.php$}',
        '{/(application|concrete|packages/\w+)/(controllers|jobs)/}',
        '{/themes/\w+/page_theme\.php$}',
    ];

    /**
     * Entry-point PHP files (where the PHP version may not match the PHP version requirements).
     *
     * @var string[]
     */
    private const RX_ENTRYPOINT = [
        '{/concrete/bin/(concrete|concrete5)$}',
        '{/concrete/dispatcher\.php$}',
        '{/index.php$}',
        '{/\.php-cs-fixer(\.dist)?\.php$}',
    ];

    public static function resolveFileFlags(\SplFileInfo $file): int
    {
        $fullFilename = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());

        foreach (self::RX_SRC_ENTITY as $rx) {
            if (preg_match($rx, $fullFilename)) {
                return self::DOCTRINE_ENTITY;
            }
        }
        foreach (self::RX_SRC as $rx) {
            if (preg_match($rx, $fullFilename)) {
                return 0;
            }
        }
        foreach (self::RX_MODIFIED_PSR as $rx) {
            if (preg_match($rx, $fullFilename)) {
                return self::MODIFIED_PSR;
            }
        }
        foreach (self::RX_ENTRYPOINT as $rx) {
            if (preg_match($rx, $fullFilename)) {
                return self::ENTRYPOINT;
            }
        }

        return self::VIEW;
    }
}
