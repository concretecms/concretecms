<?php

declare(strict_types=1);

namespace Concrete\Core\Csv\Export;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Column\ColumnExportableInterface;
use Concrete\Core\Search\Column\Set;
use League\Csv\Writer;

defined('C5_EXECUTE') or die('Access Denied.');

class UserExporter extends AbstractExporter
{
    /**
     * @var \DateTimeZone
     */
    protected $appTimezone;

    /**
     * @var Date
     */
    protected $dateService;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var \Concrete\Core\Search\Column\ColumnInterface[]|null
     */
    protected $columns;

    /**
     * Initialize the instance.
     *
     * @param Set|null $columns the search result columns to export, or null to use the legacy full-data format
     */
    public function __construct(
        Writer $writer,
        UserCategory $userCategory,
        Date $dateService,
        Repository $config,
        ?Set $columns = null
    ) {
        parent::__construct($writer, $columns === null ? $userCategory : null);
        $this->appTimezone = $dateService->getTimezone('app');
        $this->columns = $columns === null ? null : $columns->getColumns();
        $this->dateService = $dateService;
        $this->format = $this->getFormat($config->get('concrete.export.csv.datetime_format', 'ATOM'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Export\AbstractExporter::getStaticHeaders()
     */
    protected function getStaticHeaders()
    {
        if ($this->columns !== null) {
            foreach ($this->columns as $column) {
                if ($column !== null) {
                    yield $column->getColumnName();
                }
            }

            return;
        }

        yield 'id';
        yield 'username';
        yield 'email';
        yield 'dateAdded';
        yield 'active';
        yield 'numLogins';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Export\AbstractExporter::getStaticFieldValues()
     */
    protected function getStaticFieldValues(ObjectInterface $userInfo)
    {
        // @var \Concrete\Core\User\UserInfo $userInfo
        if ($this->columns !== null) {
            foreach ($this->columns as $column) {
                if ($column === null) {
                    continue;
                }
                $value = $column instanceof ColumnExportableInterface
                    ? $column->getColumnExportValue($userInfo)
                    : $column->getColumnValue($userInfo);
                yield $this->normalizeColumnValue($value);
            }

            return;
        }

        yield (string) $userInfo->getUserID();
        yield $userInfo->getUserName();
        yield $userInfo->getUserEmail();

        $dateTime = $userInfo->getUserDateAdded();
        if ($dateTime) {
            $dateTime = clone $dateTime;
            yield $this->dateService->formatCustom($this->format, $dateTime, 'app');
        } else {
            yield '';
        }
        yield $userInfo->isActive() ? '1' : '0';
        yield (string) (int) $userInfo->getNumLogins();
    }

    /**
     * Convert a search result value to a single plain-text CSV field.
     */
    protected function normalizeColumnValue($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_iterable($value)) {
            $parts = [];
            foreach ($value as $part) {
                $parts[] = $this->normalizeColumnValue($part);
            }

            return implode('; ', array_values(array_filter($parts, 'strlen')));
        }
        if ($value instanceof \DateTimeInterface) {
            return $this->dateService->formatCustom($this->format, $value, 'app');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (!is_scalar($value)) {
            if (!is_object($value) || !method_exists($value, '__toString')) {
                return '';
            }
        }

        $value = (string) $value;
        $hasBadges = false;
        $value = preg_replace_callback(
            '~<([a-z][a-z0-9]*)\b[^>]*\bclass\s*=\s*["\'][^"\']*\bbadge\b[^"\']*["\'][^>]*>(.*?)</\1\s*>~is',
            static function (array $matches) use (&$hasBadges): string {
                $hasBadges = true;

                return ' ' . $matches[2] . '; ';
            },
            $value
        ) ?? $value;
        $value = preg_replace('/<\s*(script|style)\b[^>]*>.*?(?:<\s*\/\s*\1\s*>|$)/is', ' ', $value) ?? $value;
        // Add boundaries around inline markup so adjacent labels don't run together
        // when their tags are removed by the plain-text normalization.
        $value = preg_replace('/(<\/?[a-z][^>]*>)/i', ' $1 ', $value) ?? $value;
        $value = strip_tags($value);
        $value = html_entity_decode(
            $value,
            ENT_QUOTES | ENT_HTML5,
            defined('APP_CHARSET') ? APP_CHARSET : 'UTF-8'
        );
        $value = str_replace("\xc2\xa0", ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        $value = trim($value);

        return $hasBadges ? rtrim($value, '; ') : $value;
    }

    protected function getFormat(string $formatName = 'ATOM')
    {
        $datetime_format_constant = sprintf('DATE_%s', $formatName);

        if (defined($datetime_format_constant)) {
            return constant($datetime_format_constant);
        }

        return DATE_ATOM;
    }
}
