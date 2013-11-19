<?php
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
$ddtz = date_default_timezone_get();
if((!is_string($ddtz)) || (!strlen($ddtz))) {
	$ddtz = 'UTC';
}
date_default_timezone_set($ddtz);
@ini_set('track_errors', true);
@ini_set('html_errors', false);
@ini_set('display_errors', 'stderr');
@ini_set('display_startup_errors', true);
@ini_set('log_errors', false);

try {
	if(empty($argv) || empty($argv[1])) {
		throw new Exception('Please specify the file name to work on.');
	}
	$sourceFilename = $argv[1];
	$destinationFilename = empty($argv[2]) ? $sourceFilename : $argv[2];
	$sot = ini_get('short_open_tag');
	if(empty($sot)) {
		throw new Exception('short_open_tag must be enabled. You can do this in php.ini or in the command-line (eg `php -d short_open_tag=On ...`)');
	}
	ShortTagsRemover::fileToFile($sourceFilename, $destinationFilename, true);
	die(0);
}
catch(Exception $x) {
	file_put_contents('php://stderr', $x->getMessage());
	die(1);
}

class ShortTagsRemover {
	public static function fileToFile($sourceFilename, $destinationFilename, $overwrite = false) {
		if(is_dir($destinationFilename)) {
			throw new Exception("'$destinationFilename' is a folder!");
		}
		if((!$overwrite) && is_file($destinationFilename)) {
			throw new Exception("'$destinationFilename' already exists!");
		}
		$content = self::fileToString($sourceFilename);
		if(!($hFile = @fopen($destinationFilename, 'wb'))) {
			throw new Exception("Error opening '$destinationFilename' for write!");
		}
		fwrite($hFile, $content);
		fclose($hFile);
	}
	public static function fileToString($sourceFilename) {
		if(!is_file($sourceFilename)) {
			throw new Exception("The file '$sourceFilename' does not exist.");
		}
		$content = @file_get_contents($sourceFilename);
		if($content === false) {
			throw new Exception("Error reading the file '$sourceFilename'.");
		}
		return self::stringToString($content);
	}
	public static function stringToString($phpCode) {
		$result = '';
		$tokens = token_get_all($phpCode);
		$numTokens = count($tokens);
		for($i = 0; $i < $numTokens; $i++) {
			$token = $tokens[$i];
			$expanded = '';
			if(is_array($token)) {
				switch($token[0]) {
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
			}
			else {
				$result .= $token;
			}
			if(strlen($expanded)) {
				$result .= $expanded;
				if(preg_match('/([ \t\r\n]+)$/', $token[1], $m)) {
					// The current token contains some white space
					$result .= $m[1];
				}
				else {
					// The next token is a white space? If not let's add a white space
					if(($i == ($numTokens - 1)) || (!is_array($tokens[$i + 1])) || ($tokens[$i + 1][0] != T_WHITESPACE)) {
						$result .= ' ';
					}
				}
			}
		}
		return $result;
	}
}
