<?php

namespace Concrete\Core\User;

use Concrete\Core\Attribute\Category\UserCategory;
use League\Csv\Writer;

/**
 * A Writer class for user objects
 */
class CsvWriter
{

    /** @var Writer The writer we use to output */
    protected $writer;

    /** @var UserCategory */
    protected $category;

    public function __construct(Writer $writer, UserCategory $category)
    {
        $this->writer = $writer;
        $this->category = $category;
    }

    /**
     * Insert a header row for this result set
     */
    public function insertHeaders()
    {
        $this->writer->insertOne(iterator_to_array($this->getHeaders()));
    }

    /**
     * Insert all data from the passed userlist
     * @param \Concrete\Core\User\UserList $list
     */
    public function insertUserList(UserList $list)
    {
        $this->writer->insertAll($this->projectList($list));
    }

    /**
     * A generator that takes a UserList and converts it to CSV rows
     * @param \Concrete\Core\User\UserList $list
     * @return \Generator
     */
    private function projectList(UserList $list)
    {
        $statement = $list->deliverQueryObject()->execute();

        foreach ($statement as $result) {
            $user = $list->getResult($result);
            yield iterator_to_array($this->projectUser($user));
        }
    }

    /**
     * Turn a user into an array
     * @param \Concrete\Core\User\UserInfo $user
     * @return array
     */
    private function projectUser(UserInfo $user)
    {
        yield $user->getUserName();
        yield $user->getUserEmail();

        $userRegistrationDate = $user->getUserDateAdded();
        yield $userRegistrationDate->format('d/m/Y G:i');

        yield $user->isActive() ? 'Active' : 'Inactive';
        yield $user->getNumLogins();

        $attributes = $this->getAttributeKeys();
        foreach ($attributes as $attribute) {
            $value = $user->getAttributeValueObject($attribute);
            yield $value ? $value->getPlainTextValue() : '';
        }
    }

    /**
     * A generator that returns all headers
     * @return \Generator
     */
    private function getHeaders()
    {
        // Static headers
        $headers = ['Username', 'Email', 'Signup Date', 'Status', '# Logins'];

        foreach ($headers as $header) {
            yield $header;
        }

        // Get headers for User attributes
        $attributes = $this->category->getList();
        foreach ($attributes as $attribute) {
            yield $attribute->getAttributeKeyDisplayName();
        }
    }

    /**
     * Memoize the attribute keys so that we aren't looking them up over and over
     * @return array
     */
    private function getAttributeKeys()
    {
        if (!isset($this->attributeKeys)) {
            $this->attributeKeys = $this->category->getList();
        }

        return $this->attributeKeys;
    }

}
