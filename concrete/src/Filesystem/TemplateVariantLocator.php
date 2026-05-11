<?php

namespace Concrete\Core\Filesystem;

use Concrete\Core\Filesystem\FileLocator\Record;

/**
 * Resolves the renderable template variant for an explicit template filename.
 *
 * This class differs from {@see FileLocator} in one important way: it is aware
 * of PHP/Twig sibling templates and may return a different file extension than
 * the one that was originally requested.
 *
 * This class also differs from {@see TemplateLocator}, which is an older,
 * higher-level template selection helper unrelated to PHP/Twig sibling
 * resolution.
 *
 * The expected contract is:
 * - callers pass a full logical template path ending in `.php` or `.html.twig`
 * - `.html.twig` requests only probe `.html.twig`
 * - `.php` requests probe `.html.twig` first and then `.php`
 * - location precedence is preserved from {@see FileLocator}; once a location
 *   yields a candidate record, lower-priority locations are not considered
 */
class TemplateVariantLocator
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * Resolve the best renderable template variant for a logical template path.
     *
     * The returned record may reference a different extension than the
     * originally requested file. For example, asking for `view.php` may return
     * a record for `view.html.twig` when that sibling template exists in a
     * higher-priority location.
     *
     * @param string $file a logical file path ending in `.php` or `.html.twig`
     *
     * @return Record|null
     */
    public function getRecord(string $file)
    {
        $candidates = $this->getCandidateFiles($file);
        $fallbackCandidates = $this->getFallbackCandidateFiles($file);
        foreach ($this->fileLocator->getSearchLocations() as $location) {
            $location->setFilesystem($this->fileLocator->getFilesystem());
            $records = [];
            foreach ($candidates as $candidate) {
                $record = $location->contains($candidate);
                if (!$record) {
                    continue;
                }
                $records[$candidate] = $record;
                if ($record->exists()) {
                    return $record;
                }
            }
            foreach ($fallbackCandidates as $candidate) {
                if (isset($records[$candidate])) {
                    return $records[$candidate];
                }
            }
        }

        return null;
    }

    /**
     * Build the ordered sibling-variant candidates for a template request.
     *
     * `.html.twig` requests are explicit and only probe Twig.
     * `.php` requests first probe the Twig sibling and then the original PHP
     * file to preserve transparent Twig support for legacy PHP callers.
     *
     * @param string $file
     *
     * @return string[]
     */
    protected function getCandidateFiles(string $file): array
    {
        if (str_ends_with($file, '.html.twig')) {
            return [$file];
        }
        if (str_ends_with($file, '.php')) {
            return [substr($file, 0, -4) . '.html.twig', $file];
        }

        throw new \InvalidArgumentException(t('TemplateVariantLocator requires an explicit .php or .html.twig filename.'));
    }

    /**
     * Build the ordered fallback candidates for a template request.
     *
     * When no matching file exists within a winning location we prefer to fall
     * back to the explicitly requested filename, which preserves legacy PHP
     * behavior while still allowing Twig to transparently win when it exists.
     *
     * @param string $file
     *
     * @return string[]
     */
    protected function getFallbackCandidateFiles(string $file): array
    {
        if (str_ends_with($file, '.html.twig')) {
            return [$file];
        }
        if (str_ends_with($file, '.php')) {
            return [$file, substr($file, 0, -4) . '.html.twig'];
        }

        throw new \InvalidArgumentException(t('TemplateVariantLocator requires an explicit .php or .html.twig filename.'));
    }
}
