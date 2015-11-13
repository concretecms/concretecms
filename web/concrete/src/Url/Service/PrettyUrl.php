<?php

namespace Concrete\Core\Url\Service;

use Core;

/**
 * Helpful functions for working with pretty urls.
 */
class PrettyUrl
{
    const OPENING_LINE = '# -- concrete5 urls start --';
    const CLOSING_LINE = '# -- concrete5 urls end --';

    /**
     * Returns the mod_rewrite rules.
     *
     * @return string
     */
    public function getRewriteRules()
    {
        $dirRel = DIR_REL;
        $dispatcherFilename = DISPATCHER_FILENAME;

        return <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase $dirRel/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME}/index.html !-f
    RewriteCond %{REQUEST_FILENAME}/index.php !-f
    RewriteRule . $dispatcherFilename [L]
</IfModule>
EOT;
    }

    /**
     * Returns the .htaccess text to be copied/inserted.
     *
     * @return string
     */
    public function getHtaccessText()
    {
        return static::OPENING_LINE."\n".$this->getRewriteRules()."\n".static::CLOSING_LINE;
    }

    /**
     * Update the .htaccess file, by adding or removing the rewrite rules used to enable pretty URLs.
     *
     * @param bool $prettyUrlsEnabled true if the pretty URLs are enabled, false otherwise
     *
     * @return bool Return true if the htaccess has been updated (or it was already ok),
     * return false if we were not able to update it (users need to manually update the .htaccess file) 
     */
    public function updateHtaccessContents($prettyUrlsEnabled)
    {
        $htaccessFilename = DIR_BASE.'/.htaccess';
        $fh = Core::make('helper/file');
        /* @var \Concrete\Core\File\Service\File $fh */
        $oldContents = file_exists($htaccessFilename) ? $fh->getContents($htaccessFilename) : '';
        if ($oldContents === false) {
            $result = false;
        } else {
            // Normalize formatting
            $oldContents = rtrim(str_replace(array("\r\n", "\r"), "\n", $oldContents));
            // Check if current contents contains the rewrite rules
            $lookFor = $this->getRewriteRules();
            $lookFor =
                // Before we have either the start of the file or a line ending
                '(^|\n)'.
                // We may have the opening comment line
                '(\s*'.preg_quote(static::OPENING_LINE, '/').'\s*\n+)?'.
                // The rewrite rules
                '\s*'.preg_replace("/\n\s*/", "\\s*\\n\\s*", preg_quote($lookFor, '/')).'\s*'.
                // We may have the closing comment line
                '(\n\s*'.preg_quote(static::CLOSING_LINE, '/').'\s*)?'.
                // Finally we have the end of the file or a line ending
                '(\n|$)'
            ;
            $newContents = false;
            if (preg_match("/$lookFor/", $oldContents, $match)) {
                // .htaccess currently contains the rewrite rules
                if (!$prettyUrlsEnabled) {
                    $newContents = rtrim(str_replace($match[0], "\n", $oldContents));
                    if ($newContents !== '') {
                        $newContents .= "\n";
                    }
                }
            } else {
                // .htaccess currently does not contain the rewrite rules
                if ($prettyUrlsEnabled) {
                    // Let's add the rules
                    $newContents = (($oldContents === '') ? '' : "$oldContents\n\n").$this->getHtaccessText()."\n";
                }
            }
            if ($newContents === false) {
                // Nothing to write
                $result = true;
            } else {
                if (file_exists($htaccessFilename) && !is_writable($htaccessFilename)) {
                    $result = false;
                } else {
                    $result = @file_put_contents($htaccessFilename, $newContents) !== false;
                }
            }
        }

        return $result;
    }
}
