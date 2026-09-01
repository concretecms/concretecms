<?php

declare(strict_types=1);

namespace Concrete\Core\Csv\Export;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Column\ColumnExportableInterface;
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
     * @param \Concrete\Core\Search\Column\ColumnInterface[]|null $columns the search result columns to export, or null to use the legacy full-data format
     */
    public function __construct(
        Writer $writer,
        UserCategory $userCategory,
        Date $dateService,
        Repository $config,
        ?array $columns = null
    ) {
        parent::__construct($writer, $columns === null ? $userCategory : null);
        $this->appTimezone = $dateService->getTimezone('app');
        $this->columns = $columns === null ? null : array_values(array_filter($columns));
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
                yield $column->getColumnName();
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
     *
     * Columns are expected to already provide plain text: either directly through
     * getColumnValue(), or through getColumnExportValue() when the column implements
     * ColumnExportableInterface (typically because getColumnValue() returns markup).
     * This method only normalizes the small set of value shapes it's safe to handle
     * generically: iterables (joined with "; "), dates, booleans and scalars.
     */
    protected function normalizeColumnValue($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_iterable($value)) {
            $parts = [];
            foreach ($value as $part) {
                $part = $this->normalizeColumnValue($part);
                if ($part !== '') {
                    $parts[] = $part;
                }
            }

            return implode('; ', $parts);
        }
        if ($value instanceof \DateTimeInterface) {
            return $this->dateService->formatCustom($this->format, $value, 'app');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_scalar($value)) {
            return trim((string) $value);
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return trim((string) $value);
        }

        return '';
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