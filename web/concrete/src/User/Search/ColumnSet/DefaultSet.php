<?php
namespace Concrete\Core\User\Search\ColumnSet;

use \Concrete\Core\Search\Column\Column;
use \Concrete\Core\Search\Column\Set;
use Core;

class DefaultSet extends Set
{
    protected $attributeClass = 'UserAttributeKey';

    public function getUserName($ui)
    {
        return '<a data-user-name="' . $ui->getUserDisplayName() . '" data-user-email="' . $ui->getUserEmail() . '" data-user-id="' . $ui->getUserID() . '" href="#">' . $ui->getUserName() . '</a>';
    }

    public function getUserEmail($ui)
    {
        return '<a href="mailto:' . $ui->getUserEmail() . '">' . $ui->getUserEmail() . '</a>';
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
