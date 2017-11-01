<?php
namespace Concrete\Core\Permission\Access;

class CalendarAdminAccess extends Access
{
    // This is kind of a hack but we need it for the Journal Author entity
    protected $journal;
    protected $journal_entry;

    /**
     * @return mixed
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param mixed $journal
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;
    }

    /**
     * @return mixed
     */
    public function getJournalEntry()
    {
        return $this->journal_entry;
    }

    /**
     * @param mixed $journal_entry
     */
    public function setJournalEntry($journal_entry)
    {
        $this->journal_entry = $journal_entry;
    }
}
