<?php
namespace Concrete\Core\Csv\Strategy;

use Concrete\Core\Search\Result\Result;

/**
 * Class UserResultsStrategy.
 *
 * Turns a UserList object into rows for a CSV
 */
class UserResultsStrategy implements StrategyInterface
{
    private $rows = [];
    private $users;

    /**
     * UserListStrategy constructor.
     *
     * @param Result $userSearchResults
     */
    public function __construct(Result $userSearchResults)
    {
        $this->users = $userSearchResults->getItemListObject()->getResults();
    }

    public function getRows()
    {
        $this->processRows();

        return $this->rows;
    }

    private function processRows()
    {
        $this->processHeaders();
        $this->processEntries();
    }

    private function processEntries()
    {
        foreach ($this->users as $user) {
            $row = [];
            $row[] = $user->getUserName();
            $row[] = $user->getUserEmail();

            $userRegistrationDate = $user->getUserDateAdded();
            $row[] = $userRegistrationDate->format('d/m/Y G:i');

            $row[] = $user->isActive() ? 'Active' : 'Inactive';
            $row[] = $user->getNumLogins();

            $userAttributes = $user->getObjectAttributeCategory();
            foreach ($userAttributes->getList() as $userAttribute) {
                $attributeValue = $user->getAttributeValueObject($userAttribute->getAttributeKeyHandle());
                $row[] = $attributeValue ? $attributeValue->getPlainTextValue() : '';
            }

            $this->rows[] = $row;
        }
    }

    private function processHeaders()
    {
        // Static headers
        $headers = ['Username', 'Email', 'Signup Date', 'Status', '# Logins'];

        // Get headers for User attributes
        $firstUser = current($this->users);
        $userAttributes = $firstUser->getObjectAttributeCategory();
        foreach ($userAttributes->getList() as $userAttribute) {
            $headers[] = $userAttribute->getAttributeKeyDisplayName();
        }

        $this->rows = array_merge([$headers], $this->rows);
    }
}
