<?php

namespace Concrete\Core\Utility;

use DOMDocument;

class SearchIndexContentSanitizer
{
    private const BLOCK_ELEMENTS_PATTERN = '/<\s*\/?\s*(?:address|article|aside|blockquote|br|caption|dd|div|dl|dt|figcaption|figure|footer|form|h[1-6]|header|hr|li|main|nav|ol|p|pre|section|table|tbody|td|tfoot|th|thead|tr|ul)\b[^>]*>/i';

    private const NON_VISIBLE_ELEMENTS_PATTERN = '/<\s*(script|style)\b[^>]*>.*?(?:<\s*\/\s*\1\s*>|$)/is';

    /**
     * Convert searchable HTML-ish content into plain text for the page search index.
     *
     * Script/style contents are removed entirely because they are not visible to readers
     * and should never leak into indexed search content.
     */
    public function sanitize(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        $content = $this->normalizeMarkupSpacing($content);
        $content = $this->removeNonVisibleElementContent($content);
        $content = $this->extractText($content);
        $content = $this->decodeEntities($content);

        return $this->normalizeWhitespace($content);
    }

    private function normalizeMarkupSpacing(string $content): string
    {
        $content = preg_replace(self::BLOCK_ELEMENTS_PATTERN, ' ', $content) ?? $content;

        return str_ireplace(['&nbsp;', '&#160;', '&#xA0;'], ' ', $content);
    }

    private function removeNonVisibleElementContent(string $content): string
    {
        return preg_replace(self::NON_VISIBLE_ELEMENTS_PATTERN, ' ', $content) ?? $content;
    }

    private function extractText(string $content): string
    {
        if (!class_exists(DOMDocument::class)) {
            return strip_tags($content);
        }

        $document = new DOMDocument('1.0', $this->getEncoding());
        $previousErrorSetting = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML($this->wrapHtmlFragment($content), LIBXML_NONET);
            if (!$loaded) {
                return strip_tags($content);
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorSetting);
        }

        $body = $document->getElementsByTagName('body')->item(0);

        return $body !== null ? $body->textContent : $document->textContent;
    }

    private function wrapHtmlFragment(string $content): string
    {
        return sprintf(
            '<?xml encoding="%s" ?><!DOCTYPE html><html><body>%s</body></html>',
            $this->getEncoding(),
            $content
        );
    }

    private function decodeEntities(string $content): string
    {
        return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, $this->getEncoding());
    }

    private function normalizeWhitespace(string $content): string
    {
        $content = str_replace("\xc2\xa0", ' ', $content);
        $content = preg_replace('/\s+/u', ' ', $content) ?? $content;

        return trim($content);
    }

    private function getEncoding(): string
    {
        return defined('APP_CHARSET') ? APP_CHARSET : 'UTF-8';
    }
}
