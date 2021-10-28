<?php

namespace Concrete\Core\Logging\Search\ColumnSet;

use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Logging\Levels;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\Search\ColumnSet\Column\ChannelColumn;
use Concrete\Core\Logging\Search\ColumnSet\Column\LevelColumn;
use Concrete\Core\Logging\Search\ColumnSet\Column\LogIdentifierColumn;
use Concrete\Core\Logging\Search\ColumnSet\Column\MessageColumn;
use Concrete\Core\Logging\Search\ColumnSet\Column\TimeColumn;
use Concrete\Core\Logging\Search\ColumnSet\Column\UserIdentifierColumn;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Utility\Service\Text as TextService;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    /**
     * @param LogEntry $logEntry
     * @return string
     * @throws Exception
     * @throws BadArgumentType
     * @noinspection PhpUnused
     */
    public static function getCollectionTime($logEntry)
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateHelper */
        $dateHelper = $app->make(Date::class);
        return $dateHelper->formatDateTime($logEntry->getTime());
    }

    /**
     * @param LogEntry $logEntry
     * @return string
     * @noinspection PhpUnused
     */
    public static function getCollectionUser($logEntry)
    {
        $user = $logEntry->getUser();
        if ($user instanceof UserInfo) {
            return $logEntry->getUser()->getUserName();
        } else {
            return "";
        }
    }

    /**
     * @param LogEntry $logEntry
     * @return string
     * @noinspection PhpUnused
     */
    public function getCollectionLevel($logEntry)
    {
        return Levels::getLevelDisplayName($logEntry->getLevel());
    }

    /**
     * @param $logEntry LogEntry
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getFormattedMessage($logEntry)
    {
        $app = Application::getFacadeApplication();
        /** @var TextService $textHelper */
        $textHelper  = $app->make(TextService::class);
        return $textHelper->makenice($logEntry->getMessage());

    }

    public function __construct()
    {
        $this->addColumn(new LogIdentifierColumn());
        $this->addColumn(new ChannelColumn());
        $this->addColumn(new LevelColumn());
        $this->addColumn(new MessageColumn());
        $this->addColumn(new TimeColumn());
        $this->addColumn(new UserIdentifierColumn());
        $date = $this->getColumnByKey('l.logID');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
