<?php

ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
$ddtz = date_default_timezone_get();
if ((!is_string($ddtz)) || (!strlen($ddtz))) {
    $ddtz = 'UTC';
}
date_default_timezone_set($ddtz);
@ini_set('track_errors', true);
@ini_set('html_errors', false);
@ini_set('display_errors', 'stderr');
@ini_set('display_startup_errors', true);
@ini_set('log_errors', false);

try {
    $fileList = null;
    $sourceFilename = null;
    $destinationFilename = null;

    if (isset($argv)) {
        $n = count($argv);
        for ($i = 1; $i < $n; ++$i) {
            switch (strtolower($argv[$i])) {
                case '--list':
                    if (!is_null($sourceFilename)) {
                        throw new Exception('--list parameter can\'t be used togheter with single-file syntax');
                    }
                    if (!is_null($fileList)) {
                        throw new Exception('--list parameter has been specified more than once');
                    }
                    ++$i;
                    if ($i >= $n) {
                        throw new Exception('After the --list parameter you have to specify the text file containing the list of files to operate on');
                    }
                    $fileList = @$argv[$i];
                    break;
                default:
                    if (!is_null($fileList)) {
                        throw new Exception('--list parameter can\'t be used togheter with single-file syntax');
                    }
                    if (is_null($sourceFilename)) {
                        $sourceFilename = $argv[$i];
                    } elseif (is_null($destinationFilename)) {
                        $destinationFilename = $argv[$i];
                    } else {
                        throw new Exception('Too many parameters!');
                    }
            }
        }
    }
    $sot = ini_get('short_open_tag');
    if (empty($sot)) {
        throw new Exception('short_open_tag must be enabled. You can do this in php.ini or in the command-line (eg `php -d short_open_tag=On ...`)');
    }
    if (!is_null($fileList)) {
        ShortTagsRemover::fromFileList($fileList, true);
    } elseif (!is_null($sourceFilename)) {
        ShortTagsRemover::fileToFile($sourceFilename, is_null($destinationFilename) ? $sourceFilename : $destinationFilename, true);
    } else {
        $p = (isset($argv) && isset($argv[0])) ? $argv[0] : '<programName>';
        throw new Exception("Missing parameters. You have the following options:\n\n$p <fromFileName> <toFileName>\nTo copy from one file to the other file\n\n$p <fileName>\nTo work directly on the file\n\n$p --list <fileName>\nTo read the files to work on from a text file. Put one file name in every line to work on the file itseft, the source and destination filenames separated by TAB to copy from source to destination\n\n");
    }
    die(0);
} catch (Exception $x) {
    file_put_contents('php://stderr', $x->getMessage());
    die(1);
}

class ShortTagsRemover
{
    public static function fromFileList($listFilename, $overwrite = false)
    {
        if (is_dir($listFilename)) {
            throw new Exception("'$listFilename' is a folder!");
        }
        if (!is_file($listFilename)) {
            throw new Exception("The file '$listFilename' does not exist.");
        }
        $content = @file_get_contents($listFilename);
        if ($content === false) {
            throw new Exception("Error reading the file '$listFilename'.");
        }
        $processes = array();
        $content = trim(str_replace("\r", "\n", str_replace("\r\n", "\n", $content)));
        foreach (explode("\n", $content) as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $chunks = explode("\t", $line);
                switch (count($chunks)) {
                    case 1:
                        $processes[] = array($chunks[0], $chunks[0]);
                        break;
                    case 2:
                        $processes[] = array($chunks[0], $chunks[1]);
                        break;
                    default:
                        throw new Exception("Invalid line read from $listFilename: it contains more that two file names");
                }
            }
        }
        foreach ($processes as $process) {
            echo "Processing {$process[0]}... ";
            self::fileToFile($process[0], $process[1], $overwrite);
            echo "done.\n";
        }
    }
    public static function fileToFile($sourceFilename, $destinationFilename, $overwrite = false)
    {
        if (is_dir($destinationFilename)) {
            throw new Exception("'$destinationFilename' is a folder!");
        }
        if ((!$overwrite) && is_file($destinationFilename)) {
            throw new Exception("'$destinationFilename' already exists!");
        }
        $content = self::fileToString($sourceFilename);
        if (!($hFile = @fopen($destinationFilename, 'wb'))) {
            throw new Exception("Error opening '$destinationFilename' for write!");
        }
        fwrite($hFile, $content);
        fclose($hFile);
    }
    public static function fileToString($sourceFilename)
    {
        if (!is_file($sourceFilename)) {
            throw new Exception("The file '$sourceFilename' does not exist.");
        }
        $content = @file_get_contents($sourceFilename);
        if ($content === false) {
            throw new Exception("Error reading the file '$sourceFilename'.");
        }

        return self::stringToString($content);
    }
    public static function stringToString($phpCode)
    {
        $result = '';
        $tokens = token_get_all($phpCode);
        $numTokens = count($tokens);
        for ($i = 0; $i < $numTokens; ++$i) {
            $token = $tokens[$i];
            $expanded = '';
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_OPEN_TAG_WITH_ECHO:
                        $expanded = '<?php echo';
                        break;
                    case T_OPEN_TAG:
                        $expanded = '<?php';
                        break;
                    default:
                        $result .= $token[1];
                        break;
                }
            } else {
                $result .= $token;
            }
            if (strlen($expanded)) {
                $result .= $expanded;
                if (preg_match('/([ \t\r\n]+)$/', $token[1], $m)) {
                    // The current token contains some white space
                    $result .= $m[1];
                } else {
                    // The next token is a white space? If not let's add a white space
                    if (($i == ($numTokens - 1)) || (!is_array($tokens[$i + 1])) || ($tokens[$i + 1][0] != T_WHITESPACE)) {
                        $result .= ' ';
                    }
                }
            }
        }

        return $result;
    }
}
