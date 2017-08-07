<?php
namespace Concrete\Core\User\Search\ColumnSet;

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
        return '<a data-user-name="' . h($ui->getUserDisplayName()) . '" data-user-email="' . h($ui->getUserEmail()) . '" data-user-id="' . $ui->getUserID() . '" href="#">' . h($ui->getUserName()) . '</a>';
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

    public function __construct()
    {
        $this->addColumn(new UsernameColumn());
        $this->addColumn(new EmailColumn());
        $this->addColumn(new DateAddedColumn());
        $this->addColumn(new Column('uStatus', t('Status'), ['Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserStatus'], false));
        $this->addColumn(new NumberOfLoginsColumn());
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
