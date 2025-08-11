<?php
namespace Concrete\Core\Csv\Export;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Service\Date;
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
     * Initialize the instance.
     *
     * @param Writer $writer
     * @param UserCategory $userCategory
     * @param Date $dateService
     */
    public function __construct(Writer $writer, UserCategory $userCategory, Date $dateService, Repository $config)
    {
        parent::__construct($writer, $userCategory);
        $this->appTimezone = $dateService->getTimezone('app');
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
        /* @var \Concrete\Core\User\UserInfo $userInfo */
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

    protected function getFormat(string $formatName = 'ATOM')
    {
        $datetime_format_constant = sprintf('DATE_%s', $formatName);

        if (defined($datetime_format_constant)) {
            return constant($datetime_format_constant);
        }

        return DATE_ATOM;
    }
}
