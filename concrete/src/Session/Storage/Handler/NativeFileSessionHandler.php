<?php
namespace Concrete\Core\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;

/**
 * NativeFileSessionHandler.
 *
 * Native session handler using PHP's built in file storage, with open_basedir restriction checking.
 *
 * Copied from Symfony's NativeFileSessionHandler and added in some exception handling.
 *
 * Default PHP config usually puts the session data in a global directory which the runtime should have no access
 * to. Those directories should be able to be warded off for security reasons using open_basedir restrictions. By
 * testing the existence of that directory such a restriction is useless as it will always generate a fatal error.
 */
class NativeFileSessionHandler extends NativeSessionHandler
{
    /**
     * Constructor.
     *
     * @param string $savePath Path of directory to save session files.
     *                         Default null will leave setting as defined by PHP.
     *                         '/path', 'N;/path', or 'N;octal-mode;/path
     *
     * @see http://php.net/session.configuration.php#ini.session.save-path for further details.
     *
     * @throws \InvalidArgumentException On invalid $savePath
     */
    public function __construct($savePath = null)
    {
        if (null === $savePath) {
            $savePath = ini_get('session.save_path');
        }

        $baseDir = $savePath;

        if ($count = substr_count($savePath, ';')) {
            if ($count > 2) {
                throw new \InvalidArgumentException(sprintf('Invalid argument $savePath \'%s\'', $savePath));
            }

            // characters after last ';' are the path
            $baseDir = ltrim(strrchr($savePath, ';'), ';');
        }

        ini_set('session.save_handler', 'files');

        try {
            if ($baseDir && !is_dir($baseDir)) {
                mkdir($baseDir, 0777, true);
            }

            ini_set('session.save_path', $savePath);
        } catch (\Exception $e) {
            /*
             * Catch any exceptions caused by open_basedir restrictions and ignore them.
             *
             * Not the most elegant solution but far less tedious than trying to analyze the save path.
             *
             * - if the exception is not open_basedir related, pass it on.
             * - if a save path was manually specified, pass it on.
             */

            if (strpos($e->getMessage(), 'open_basedir') === false || current(func_get_args())) {
                throw $e;
            }
        }
    }
}
