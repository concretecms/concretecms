<?php

namespace Concrete\Core\File\Service;

use Core;

/**
 * HTAccess helper.
 *
 * Functions useful for working with the .htaccess file.
 */
class HTAccess
{
    /**
     * The comments to be placed in the .htaccess file before the rewrite rules used for pretty URLs.
     *
     * @var string
     */
    const REWRITERULES_OPENING_LINE = '# -- concrete5 urls start --';

    /**
     * The comments to be placed in the .htaccess file after the rewrite rules used for pretty URLs.
     *
     * @var string
     */
    const REWRITERULES_CLOSING_LINE = '# -- concrete5 urls end --';

    /**
     * Returns the full path to the .htaccess file.
     *
     * @return string
     */
    public function getHTAccessFilename()
    {
        return DIR_BASE.'/.htaccess';
    }

    /**
     * Read the current .htaccess file contents.
     *
     * @param bool $normalizeLineEndings Set to true to normalize the .htaccess contents line endings to \n
     *
     * @return string|null Returns the .htaccess file contents;
     *     if the .htaccess file exists but we were not able to read, null will be returned;
     *     if the .htaccess file does not exist, an empty string will be returned.
     */
    public function readHTAccess($normalizeLineEndings = false)
    {
        $filename = $this->getHTAccessFilename();
        if (file_exists($filename)) {
            $fh = Core::make('helper/file');
            /* @var File $fh */
            $result = $fh->getContents($filename);
            if ($result !== false && $normalizeLineEndings) {
                $result = str_replace(array("\r\n", "\r"), "\n", $result);
            }
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Set the contents of the current .htaccess file.
     *
     * @param string $newFileContents The new contents of the .htaccess file.
     *
     * @return bool Returns true on success, false if we were not able to save the .htaccess file.
     */
    public function saveHTAccess($newFileContents)
    {
        $filename = $this->getHTAccessFilename();
        if (file_exists($filename) && !is_writable($filename)) {
            $result = false;
        } else {
            $result = @file_put_contents($filename, (string) $newFileContents) !== false;
        }

        return $result;
    }

    /**
     * Returns the rewrite rules.
     *
     * @param bool $includeComments true to include the comments, false to exclude them.
     *
     * @return string
     */
    public function getRewriteRules($includeComments = true)
    {
        $dirRel = DIR_REL;
        $dispatcherFilename = DISPATCHER_FILENAME;

        $result = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase $dirRel/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME}/index.html !-f
    RewriteCond %{REQUEST_FILENAME}/index.php !-f
    RewriteRule . $dispatcherFilename [L]
</IfModule>
EOT;
        if ($includeComments) {
            $result = static::REWRITERULES_OPENING_LINE."\n$result\n".static::REWRITERULES_CLOSING_LINE;
        }

        return $result;
    }

    /**
     * Check if the .htaccess file contains the rewrite rules used for pretty URLs.
     *
     * @return bool|null Returns
     *     true if .htaccess contains the rewrite rules,
     *     false if .htaccess does not exist or if it does not contain rewrite rules,
     *     null null if we were not able to read the existing .htaccess file
     */
    public function hasRewriteRules()
    {
        $rr = $this->getCurrentRewriteRules();
        if ($rr === null) {
            $result = null;
        } else {
            $result = ($rr !== '') ? true : false;
        }

        return $result;
    }

    /**
     * Returns the current part of the .htaccess with the rewrite rules.
     *
     * @return string|null Returns
     *     a string containing the current rewrite rules if .htaccess contains the rewrite rules,
     *     an empty string if .htaccess does not exist or if it does not contain rewrite rules,
     *     null if we were not able to read the existing .htaccess file 
     */
    public function getCurrentRewriteRules()
    {
        $contents = $this->readHTAccess(true);
        if ($contents === null) {
            // Error reading the existing .htaccess file
            $result = null;
        } else {
            $contents = rtrim($contents);
            // Build the regular expression that detects if the current contents contains the rewrite rules
            $rxLookFor = ''
                // Start of the regular expression
                .'/'
                // First of all we have either the start of the file or a line ending
                .'(^|\n)'
                // then we may have the opening comment line
                .'(\s*'.preg_quote(static::REWRITERULES_OPENING_LINE, '/').'\s*\n+)?'
                // then we have the rewrite rules
                .'\s*'.preg_replace("/\n\s*/", "\\s*\\n\\s*", preg_quote(static::getRewriteRules(false), '/')).'\s*'
                // then we may have the closing comment line
                .'(\n\s*'.preg_quote(static::REWRITERULES_CLOSING_LINE, '/').'\s*)?'
                // finally we have the end of the file or a line ending
                .'(\n|$)'
                // End of the regular expression
                .'/';
            if (preg_match($rxLookFor, $contents, $match)) {
                $result = $match[0];
            } else {
                $result = '';
            }
        }

        return $result;
    }

    /**
     * Update the .htaccess file, by adding or removing the rewrite rules used to enable pretty URLs.
     *
     * @param bool $prettyUrlsEnabled true if the pretty URLs are enabled, false otherwise.
     *
     * @return bool Returns
     *     true if the htaccess has been updated (or it was already ok),
     *     false if we were not able to update it (users need to manually update the .htaccess file)
     */
    public function setCurrentRewriteRules($prettyUrlsEnabled)
    {
        $result = false;
        $currentRewriteRules = $this->getCurrentRewriteRules();
        if ($currentRewriteRules !== null) {
            if ($currentRewriteRules === '') {
                // The .htaccess file currently does not contain the rewrite rules
                if ($prettyUrlsEnabled) {
                    // Add the rewrite rules to the current .htaccess contents
                    $contents = $this->readHTAccess(true);
                    if ($contents !== null) {
                        $contents = rtrim($contents);
                        $newContents = (($contents === '') ? '' : "$contents\n\n").$this->getRewriteRules(true)."\n";
                        $result = $this->saveHTAccess($newContents);
                    }
                } else {
                    // No need to remove the rewrite rules since they are not in the .htaccess file
                    $result = true;
                }
            } else {
                // The .htaccess file currently contains the rewrite rules
                if ($prettyUrlsEnabled) {
                    // No need to add the rewrite rules since they are already in the .htaccess file
                    $result = true;
                } else {
                    $contents = $this->readHTAccess(true);
                    if ($contents !== null) {
                        $newContents = rtrim(str_replace($currentRewriteRules, "\n\n", $contents));
                        if ($newContents !== '') {
                            $newContents .= "\n";
                        }
                        $result = $this->saveHTAccess($newContents);
                    }
                }
            }
        }

        return $result;
    }
}
