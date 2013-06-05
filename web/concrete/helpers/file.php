<?

/**
 * File helper
 * 
 * Functions useful for working with files and directories.
 * 
 * Used as follows:
 * <code>
 * $file = Loader::helper('file');
 * $path = 'http://www.concrete5.org/tools/get_latest_version_number';
 * $contents = $file->getContents($path);
 * echo $contents;
 * </code> 
 *
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class FileHelper extends Concrete5_Helper_File {}