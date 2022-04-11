<?php

namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Search\ColumnSet\Column\HomeFolderColumn;
use Concrete\Core\User\Search\ColumnSet\Column\NumberOfLoginsColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UsernameColumn;
use Concrete\Core\User\Search\ColumnSet\Column\DateAddedColumn;
use Concrete\Core\User\Search\ColumnSet\Column\EmailColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UserIDColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use Core;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'UserAttributeKey';

    public static function getUserName($ui)
    {
        return h($ui->getUserName());
    }

    public static function getUserEmail($ui)
    {
        return '<a href="mailto:' . h($ui->getUserEmail()) . '">' . h($ui->getUserEmail()) . '</a>';
    }

    public static function getUserDateAdded($ui)
    {
        return Core::make('helper/date')->formatDateTime($ui->getUserDateAdded());
    }

    public static function getUserDateLastLogin($ui)
    {
        $login = $ui->getLastLogin();
        if ($login) {
            return Core::make('helper/date')->formatDateTime($login);
        } else {
            return '';
        }
    }

    public static function getFolderName($ui)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        return (string)$db->fetchColumn("SELECT treeNodeName FROM TreeNodes WHERE treeNodeId = ? LIMIT 1", [$ui->getUserHomeFolderId()]);
    }

    public function __construct()
    {
        $this->addColumn(new UsernameColumn());
        $this->addColumn(new EmailColumn());
        $this->addColumn(new DateAddedColumn());
        $this->addColumn(new Column('uStatus', t('Status'), ['Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserStatus'], false));
        $this->addColumn(new NumberOfLoginsColumn());
        $this->addColumn(new HomeFolderColumn());
        $date = $this->getColumnByKey('u.uDateAdded');
        $this->setDefaultSortColumn($date, 'desc');
    }

    public static function getUserStatus($ui)
    {
        if ($ui->isActive()) {
            $currentStatus = t('Active');
        } elseif ($ui->isValidated()) {
            $currentStatus = t('Inactive');
        } else {
            $currentStatus = t('Unvalidated');
        }

        return $currentStatus;
    }
}
