<?php
namespace Concrete\Core\Csv\Export;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\ObjectInterface;
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
     * Initialize the instance.
     *
     * @param Writer $writer
     * @param UserCategory $userCategory
     * @param Date $dateService
     */
    public function __construct(Writer $writer, UserCategory $userCategory, Date $dateService)
    {
        parent::__construct($writer, $userCategory);
        $this->appTimezone = $dateService->getTimezone('app');
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
            $dateTime->setTimezone($this->appTimezone);
            yield $dateTime->format('Y-m-d H:i:s');
        } else {
            yield '';
        }
        yield $userInfo->isActive() ? '1' : '0';
        yield (string) (int) $userInfo->getNumLogins();
    }
}
