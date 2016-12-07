<?php
namespace Concrete\Core\Utility\Service;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;
use Hautelook\Phpass\PasswordHash;

/**
 * \@package Helpers
 * @subpackage Validation
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * A helper that allows the creation of unique strings, for use when creating hashes, identifiers.
 *
 * \@package Helpers
 * @subpackage Validation
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Identifier
{

    /**
     * Like generate() below, but simply appends an ever increasing number to what you provide
     * until it comes back as not found.
     */
    public function generateFromBase($string, $table, $key)
    {
        $foundRecord = false;
        $db = Application::make(Connection::class);
        $i = '';
        $_string = '';
        while ($foundRecord == false) {
            $_string = $string . $i;
            $cnt = $db->GetOne("select count(" . $key . ") as total from " . $table . " where " . $key . " = ?",
                array($_string));
            if ($cnt < 1) {
                $foundRecord = true;
            } else {
                if ($i == '') {
                    $i = 0;
                }
                ++$i;
            }
        }

        return $_string;
    }

    /**
     * Generates a unique identifier for an item in a database table. Used, among other places, in generating
     * User hashes for email validation.
     *
     * @param string $table
     * @param string $key
     * @param int $length
     * @param bool $lowercase
     *
     * @return string
     */
    public function generate($table, $key, $length = 12, $lowercase = false)
    {
        $foundHash = false;
        $db = Application::make(Connection::class);
        while ($foundHash == false) {
            $string = $this->getString($length);
            if ($lowercase) {
                $string = strtolower($string);
            }
            $cnt = $db->GetOne("select count(" . $key . ") as total from " . $table . " where " . $key . " = ?",
                array($string));
            if ($cnt < 1) {
                $foundHash = true;
            }
        }

        return $string;
    }

    /**
     * Generate a cryptographically secure random string
     * @param int $length
     * @return string
     */
    public function getString($length = 12)
    {
        $size = ceil($length / 2);

        try {
            if (function_exists('random_bytes')) {
                $bytes = random_bytes($size);
            } else {
                $hash = new PasswordHash(8, false);
                $bytes = $hash->get_random_bytes($size);
            }
        } catch (\Exception $e) {
            die('Could not generate a random string.');
        }

        return substr(bin2hex($bytes), 0, $length);
    }

    public function deleteKey($table, $keyCol, $uHash)
    {
        $db = Application::make(Connection::class);
        $db->Execute("DELETE FROM " . $table . " WHERE " . $keyCol . "=?", array($uHash));
    }
}
