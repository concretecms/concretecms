<?php

declare(strict_types=1);

namespace Concrete\Core\Mail;

use RuntimeException;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * This class holds the configurable addresses for outgoing emails.
 * Packages can add their own addresses in the package controller on_start method with some code like this:
 * <pre><code>$app->extend(
 *     SenderConfiguration::class,
 *     static function(SenderConfiguration $configuration): SenderConfiguration {
 *         return $configuration->addEntry(
 *             (new SenderConfiguration\Entry(t('Name of the option'), 'email.configuration.key'))
 *                 ->setPackageHandle('your_package_handle')
 *         );
 *     }
 * );
 * </code></pre>.
 */
final class SenderConfiguration
{
    /**
     * @var \Concrete\Core\Mail\SenderConfiguration\Entry[]
     */
    private $entries = [];

    /**
     * @var string
     */
    private $allConfigurationKeys = [];

    /**
     * @param \Concrete\Core\Mail\SenderConfiguration\Entry[] $entries
     *
     * @return $this
     */
    public function addEntries(iterable $entries): self
    {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }

        return $this;
    }

    /**
     * @throws \RuntimeException in case of duplicated configuration keys
     *
     * @return $this
     */
    public function addEntry(SenderConfiguration\Entry $entry): self
    {
        $prefix = $entry->getPackageHandle();
        if ($prefix !== '') {
            $prefix = "{$prefix}::";
        }
        $key = $prefix . $entry->getEmailKey();
        if (in_array($key, $this->allConfigurationKeys, true)) {
            throw new RuntimeException(t('The configuration key %s has been already specified.', $key));
        }
        $this->allConfigurationKeys[] = $key;
        if ($entry->getNameKey() !== '') {
            $key = $prefix . $entry->getNameKey();
            if (in_array($key, $this->allConfigurationKeys, true)) {
                throw new RuntimeException(t('The configuration key %s has been already specified.', $key));
            }
            $this->allConfigurationKeys[] = $key;
        }

        $this->entries[] = $entry;

        return $this;
    }

    /**
     * @return \Concrete\Core\Mail\SenderConfiguration\Entry[]
     */
    public function getEntries(): array
    {
        $this->sortEntries();

        return $this->entries;
    }

    private function sortEntries(): void
    {
        usort(
            $this->entries,
            static function (SenderConfiguration\Entry $a, SenderConfiguration\Entry $b): int {
                $delta = $b->getPriority() - $a->getPriority();
                if ($delta === 0) {
                    $delta = mb_strtolower($a->getName()) <=> mb_strtolower($b->getName());
                }

                return $delta;
            }
        );
    }
}
