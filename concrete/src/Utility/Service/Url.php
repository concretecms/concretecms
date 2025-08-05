<?php
namespace Concrete\Core\Utility\Service;

use Loader;

class Url
{
    /**
     * @param string|string[] $variable
     * @param $value
     * @param string|bool $url
     * @return string
     */
    public function setVariable($variable, $value = false, $url = false)
    {
        // Minimal normalization for URLs that may be injected into HTML attributes.
        // We ONLY percent-encode quotes and strip CR/LF to close an XSS vector where
        // some call sites forget to escape with htmlspecialchars(). We do NOT HTML-escape
        // here to avoid double-encoding at render time (callers often use specialchars(..., false)).
        $encodeQuotesAndStripCRLF = static function ($s) {
            // Encode " and ' so they can't break out of href="...".
            // Remove \r and \n to prevent attribute splitting / header-style injection.
            return str_replace(['"', "'", "\r", "\n"], ['%22', '%27', '', ''], (string) $s);
        };

        if ($url == false) {
            // Use the current request as the base, but first strip any HTML-ish content.
            // sanitizeString() removes tags like <script>… and similar markup.
            $url = Loader::helper('security')->sanitizeString($_SERVER['REQUEST_URI']);
            $url = $encodeQuotesAndStripCRLF($url);
        } elseif (strpos($url, '?') === false) {
            // Base URL provided without a query: protect it too (in case it contains quotes).
            $url = $encodeQuotesAndStripCRLF($url);

            // Append the current query string, after sanitizing and applying the same light encoding.
            $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
            $qs = Loader::helper('security')->sanitizeString($qs);
            if ($qs !== false && $qs !== '') {
                $url .= '?' . $encodeQuotesAndStripCRLF($qs);
            }
        }

        /*
        Why this change?
        - Problem: In some places the resulting URL goes directly into HTML attributes (e.g., href="…")
          without being escaped, so a literal " or ' in the URL can break the attribute and enable XSS.
        - Fix (light touch for backward compatibility): Percent-encode only the characters that break out
          of attributes ( " → %22, ' → %27 ) and strip CR/LF. We do NOT HTML-escape here to avoid
          double-encoding at call sites that *do* properly escape with specialchars(..., false).
        - Explicit replacements: We target just quotes and CR/LF to minimize side effects on existing URLs.
          Broader normalization/validation (e.g., rebuilding the URL, enforcing schemes, or encoding more
          characters) could change behavior that callers rely on, so we intentionally keep it narrow.
        */

        $vars = array();
        if (!is_array($variable)) {
            $vars[$variable] = $value;
        } else {
            $vars = $variable;
        }

        foreach ($vars as $variable => $value) {
            $url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]*?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
            if (strpos($url, '?') === false) {
                $url = $url . '?' . $variable . '=' . $value;
            } else {
                $url = $url . '&' . $variable . '=' . $value;
            }
        }

        // THIS DOES NOT WORK. SOMEONE WILL NEED TO FIX THIS PROPERLY IF THE W3C FOLKS WANT IT TO WORK
        //$url = str_replace('&', '&amp;', $url);
        return $url;
    }

    public function unsetVariable($variable, $url = false)
    {
        // either it's key/value as variables, or it's an associative array of key/values

        if ($url == false) {
            $url = $_SERVER['REQUEST_URI'];
        } elseif (!strstr($url, '?')) {
            $url = $url . '?' . $_SERVER['QUERY_STRING'];
        }

        $vars = array();
        if (!is_array($variable)) {
            $vars[] = $variable;
        } else {
            $vars = $variable;
        }

        foreach ($vars as $variable) {
            $url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]*?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
        }

        // THIS DOES NOT WORK. SOMEONE WILL NEED TO FIX THIS PROPERLY IF THE W3C FOLKS WANT IT TO WORK
        //$url = str_replace('&', '&amp;', $url);
        return $url;
    }
    public function buildQuery($url, $params)
    {
        return $url . '?' . http_build_query($params, '', '&');
    }

    /**
     * Shortens a given url with the tiny url api.
     *
     * @param string $strURL
     *
     * @return string $url
     */
    public function shortenURL($strURL)
    {
        $file = Loader::helper('file');
        $url = $file->getContents("http://tinyurl.com/api-create.php?url=".$strURL);

        return $url;
    }
}
