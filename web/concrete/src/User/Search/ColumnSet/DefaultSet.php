<?php
namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use Core;

class DefaultSet extends Set
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

    public function __construct()
    {
        $this->addColumn(new Column('u.uName', t('Username'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserName')));
        $this->addColumn(new Column('u.uEmail', t('Email'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserEmail')));
        $this->addColumn(new Column('u.uDateAdded', t('Signup Date'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserDateAdded')));
        $this->addColumn(new Column('u.uNumLogins', t('# Logins'), 'getNumLogins'));
        $date = $this->getColumnByKey('u.uDateAdded');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
